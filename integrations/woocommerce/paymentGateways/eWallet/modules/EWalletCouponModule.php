<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules;

use Exception;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletCoupon;
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;
use MLMSoft\traits\SingletonTrait;
use WC_Coupon;

class EWalletCouponModule
{
    use SingletonTrait;

    public const E_WALLET_COUPON_EXPIRED_ACTION = MLMSoftPlugin::PLUGIN_PREFIX . 'e_wallet_coupon_expired';
    public const E_WALLET_EXPIRATION_TIME = 7200; // 2 Hours

    public const ORDER_TOTAL_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'order_total';
    public const EXTERNAl_CURRENCY_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'external_currency';

    public function __construct()
    {
        add_action('woocommerce_decrease_coupon_usage_count', [$this, 'decreaseCouponUsageCount']);
        add_action(self::E_WALLET_COUPON_EXPIRED_ACTION, [$this, 'couponExpired'], 10, 1);
    }

    /**
     * @param EWalletCoupon $coupon
     */
    public static function prepareCoupon($coupon, $accountId, $setPaid = false)
    {
        if (!$coupon->getAccountId()) {
            // The coupon was created before these changes
            $coupon->setAccountId($accountId);
            if ($setPaid) {
                $coupon->setPaid();
            }
            $coupon->save();
        }
        return $coupon;
    }

    /**
     * @param string $code
     * @throws Exception
     */
    public function couponExpired($code)
    {
        if (EWalletCoupon::isEWalletCoupon($code)) {
            $coupon = new EWalletCoupon($code);
            if ($coupon->get_usage_count(null)) {
                return;
            }
            $operationTypeId = $this->getCancelPaymentWalletOperationTypeId();
            $coupon->cancelPayment($operationTypeId);
            $coupon->delete();
        }
    }

    /**
     * @param WC_Coupon $coupon
     */
    public function decreaseCouponUsageCount($coupon)
    {
        if (EWalletCoupon::isEWalletCoupon($coupon->get_code())) {
            $coupon = new EWalletCoupon($coupon->get_code());
            if ($coupon->getAccountId()) {
                $operationTypeId = $this->getCancelPaymentWalletOperationTypeId();
                $coupon->cancelPayment($operationTypeId);
                $coupon->delete();
            }
        }
    }

    /**
     * @return float
     */
    public function getMaxAmount()
    {
        $orderTotal = $this->getOrderTotal();
        $maxPercent = WCIntegrationOptions::getInstance()->maxPercentToPayBonuses;
        $maxPercent /= 100;
        return round($orderTotal * $maxPercent, 2);
    }

    /**
     * @return string
     */
    public function getExternalCurrency()
    {
        $currency = get_woocommerce_currency();
        return apply_filters(self::EXTERNAl_CURRENCY_FILTER, $currency);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getPayWalletOperationTypeId()
    {
        $walletOperationTypeId = WCIntegrationOptions::getInstance()->walletOperationIdToPayWithBonuses;
        if (!$walletOperationTypeId) {
            throw new Exception(MLMSoftPlugin::translate('Wallet operation type ID is not defined'));
        }
        return $walletOperationTypeId;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getCancelPaymentWalletOperationTypeId()
    {
        $walletOperationTypeId = WCIntegrationOptions::getInstance()->walletOperationIdToCancelPayWithBonuses;
        if (!$walletOperationTypeId) {
            throw new Exception(MLMSoftPlugin::translate('Wallet operation type ID is not defined'));
        }
        return $walletOperationTypeId;
    }

    /**
     * @return float|int
     */
    public function getOrderTotal()
    {
        $total = 0;
        $order_id = absint(get_query_var('order-pay'));

        if (0 < $order_id) {
            $order = wc_get_order($order_id);
            if ($order) {
                $total = (float)$order->get_total();
            }
        } elseif (0 < WC()->cart->total) {
            $total = (float)WC()->cart->total;
        }

        return apply_filters(self::ORDER_TOTAL_FILTER, $total);
    }
}
