<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models;

use Exception;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftLocalUser;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules\EWalletCouponModule;
use MLMSoft\traits\SingletonTrait;
use WC_Cart;
use WC_Coupon;

class EWalletCartHelper
{
    use SingletonTrait;

    /** @var WC_Cart */
    private $cart;

    /** @var EWalletCouponModule */
    private $couponModule;

    public function __construct()
    {
        $this->cart = WC()->cart;
        $this->couponModule = EWalletCouponModule::getInstance();
    }

    /**
     * @return bool
     */
    public function isCouponAppliedToCart()
    {
        if (empty($this->cart)) {
            return false;
        }
        $coupons = $this->cart->get_coupons();
        /** @var WC_Coupon $coupon */
        foreach ($coupons as $coupon) {
            if (EWalletCoupon::isEWalletCoupon($coupon->get_code())) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return EWalletCoupon|null
     */
    public function getCartCoupon()
    {
        if (empty($this->cart)) {
            return null;
        }
        $coupons = $this->cart->get_coupons();
        /** @var WC_Coupon $coupon */
        foreach ($coupons as $coupon) {
            $code = $coupon->get_code();
            if (EWalletCoupon::isEWalletCoupon($code)) {
                return new EWalletCoupon($code);
            }
        }
        return null;
    }

    /**
     * @param EWalletCoupon $coupon
     * @throws Exception
     */
    public function applyEWalletCoupon($coupon)
    {
        if (!empty($this->cart)) {
            $accountId = self::getCurrentAccountId();
            $coupon = EWalletCouponModule::prepareCoupon($coupon, $accountId);
            $operationTypeId = $this->couponModule->getPayWalletOperationTypeId();
            try {
                $coupon->payFromWallet($operationTypeId);
            } catch (Exception $exception) {
                $coupon->delete(true);
                throw $exception;
            }
            as_schedule_single_action(time() + EWalletCouponModule::E_WALLET_EXPIRATION_TIME, EWalletCouponModule::E_WALLET_COUPON_EXPIRED_ACTION, [$coupon->get_code()]);
            $this->cart->apply_coupon($coupon->get_code());
        }
    }

    /**
     * @param EWalletCoupon $coupon
     * @throws Exception
     */
    public function removeEWalletCoupon($coupon)
    {
        if (!empty($this->cart)) {
            $accountId = self::getCurrentAccountId();
            $coupon = EWalletCouponModule::prepareCoupon($coupon, $accountId, true);

            $operationTypeId = $this->couponModule->getCancelPaymentWalletOperationTypeId();

            try {
                $coupon->cancelPayment($operationTypeId);
                $this->cart->remove_coupon($coupon);
            } catch (Exception $exception) {
                $this->cart->apply_coupon($coupon->get_code());
                throw $exception;
            }

            $coupon->delete();
        }
    }

    /**
     * @return int|string
     * @throws Exception
     */
    public static function getCurrentAccountId()
    {
        $mlmSoftUser = MLMSoftLocalUser::loadFromCurrent();
        $accountId = $mlmSoftUser->getAccountId();
        if (!$accountId) {
            throw new Exception(MLMSoftPlugin::translate('Account ID not found'));
        }
        return $accountId;
    }
}