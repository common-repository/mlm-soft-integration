<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models;

use Exception;
use MLMSoft\core\MLMSoftPlugin;
use WC_Coupon;

class EWalletCoupon extends WC_Coupon
{
    public const COUPON_AMOUNT_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'coupon_amount';
    public const COUPON_PAYMENT_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'coupon_payment';
    public const COUPON_CANCEL_PAYMENT_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'coupon_cancel_payment';

    public const WALLET_ID_META_KEY = 'wallet_id';
    public const ACCOUNT_ID_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'account_id';
    public const PAY_AMOUNT_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'pay_amount';
    public const ORDER_ID_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'order_id';

    public const WALLET_CODE_PREFIX = 'e-wallet-pay_';

    private const BONUSES_RESERVED = MLMSoftPlugin::PLUGIN_PREFIX . 'bonuses_reserved';

    public function __construct($data = '')
    {
        parent::__construct($data);
    }

    /**
     * @param integer $accountId
     * @param integer $walletId
     * @param float $amount
     * @param integer $expirationTime
     * @return EWalletCoupon
     */
    public static function create($accountId, $walletId, $amount, $expirationTime = 3600)
    {
        $amount = apply_filters(self::COUPON_AMOUNT_FILTER, $amount);

		/**
		 * Using the walletId in the coupon code.
		 * @since 3.6.3
		 */
		$code = self::createCouponCode($walletId);
        $coupon = new EWalletCoupon();
        $coupon->set_code($code);
        $coupon->set_description('');
        $coupon->set_discount_type('fixed_cart');
        $coupon->set_amount($amount);
        $coupon->set_individual_use(false);
        $coupon->set_usage_limit(1);
        $coupon->setWalletId($walletId);
        $coupon->setAccountId($accountId);
        $coupon->setPayAmount($amount);
        $coupon->set_date_expires(time() + $expirationTime);
        $coupon->save();
        return $coupon;
    }

    /**
     * @param string $code
     * @return bool
     */
    public static function isEWalletCoupon($code)
    {
        return str_starts_with($code, self::WALLET_CODE_PREFIX);
    }

    /**
     * @param integer $walletId
     */
    public function setWalletId($walletId)
    {
        $this->update_meta_data(self::WALLET_ID_META_KEY, $walletId);
    }

    /**
     * @return string
     */
    public function getWalletId()
    {
        return $this->get_meta(self::WALLET_ID_META_KEY, true);
    }

    /**
     * @param integer $orderId
     */
    public function setOrderId($orderId)
    {
        $this->update_meta_data(self::ORDER_ID_META_KEY, $orderId);
    }

    /**
     * @return integer
     */
    public function getOrderId()
    {
        return $this->get_meta(self::ORDER_ID_META_KEY, true);
    }

    /**
     * @param $accountId
     */
    public function setAccountId($accountId)
    {
        $this->update_meta_data(self::ACCOUNT_ID_META_KEY, $accountId);
    }

    /**
     * @return string
     */
    public function getAccountId()
    {
        return $this->get_meta(self::ACCOUNT_ID_META_KEY, true);
    }

    /**
     * @param float $amount
     */
    public function setPayAmount($amount)
    {
        $this->update_meta_data(self::PAY_AMOUNT_META_KEY, $amount);
    }

    /**
     * @return float
     */
    public function getPayAmount()
    {
        return $this->get_meta(self::PAY_AMOUNT_META_KEY, true);
    }

    /**
     * @return bool
     */
    public function isPaid()
    {
        return (bool)$this->get_meta(self::BONUSES_RESERVED, true);
    }

    /**
     * @param bool $value
     */
    public function setPaid($value = true)
    {
        $this->update_meta_data(self::BONUSES_RESERVED, $value);
        $this->save();
    }

    /**
     * @param integer $operationTypeId
     * @throws Exception
     */
    public function payFromWallet($operationTypeId)
    {
        if ($this->isPaid()) {
            return;
        }

        $paymentAmount = $this->getPayAmount();
        if (empty($paymentAmount) || !$paymentAmount) {
            $paymentAmount = $this->get_amount(null);
        }

        $amount = -abs($paymentAmount);
        $comment = MLMSoftPlugin::translate('Pay with bonuses. Coupon {{couponCode}}', ['couponCode' => $this->get_code()]);

        $this->createWalletOperation($operationTypeId, $amount, $comment);

        $this->setPaid();
    }

    /**
     * @param integer $operationTypeId
     * @throws Exception
     */
    public function cancelPayment($operationTypeId)
    {
        if (!$this->isPaid()) {
            return;
        }

        $paymentCancellationAmount = $this->getPayAmount();
        if (empty($paymentCancellationAmount) || !$paymentCancellationAmount) {
            $paymentCancellationAmount = $this->get_amount(null);
        }

        $amount = abs($paymentCancellationAmount);
        $comment = MLMSoftPlugin::translate('Cancel payment. Coupon {{couponCode}}', ['couponCode' => $this->get_code()]);

        $this->createWalletOperation($operationTypeId, $amount, $comment);

        $this->setPaid(false);
    }

    /**
     * @param integer $operationTypeId
     * @param float $amount
     * @param string $comment
     * @throws \HttpException
     */
    private function createWalletOperation($operationTypeId, $amount, $comment)
    {
        $accountId = $this->getAccountId();
        $walletId = $this->getWalletId();

        if (!$accountId || !$walletId) {
            throw new Exception(MLMSoftPlugin::translate('The coupon was not created correctly'));
        }

        $mlmSoftPlugin = MLMSoftPlugin::getInstance();

        $mlmSoftPlugin->api3->post("account/$accountId/wallet/$walletId/operation", [
            'operationTypeId' => $operationTypeId,
            'amount' => $amount,
            'comment' => $comment
        ]);
    }

    private static function createCouponCode($walletId)
    {
		/**
         * Using the walletId in the coupon code.
		 * @since 3.6.3
		 * Example: e-wallet-pay_{walletId}_{time}-{randomBytes}
		 */
		$walletId .= '_';
        $code = self::WALLET_CODE_PREFIX . $walletId . time();
        $randomBytes = bin2hex(random_bytes(8));
        return $code . '-' . $randomBytes;
    }
}