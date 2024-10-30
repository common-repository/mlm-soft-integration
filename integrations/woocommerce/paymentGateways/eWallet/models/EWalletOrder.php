<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models;

use Exception;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\integrations\woocommerce\models\order\MLMSoftWCOrder;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules\EWalletCouponModule;
use WC_Coupon;

class EWalletOrder extends MLMSoftWCOrder
{
    /**
     * @return bool
     */
    public function isEWalletCouponApplied()
    {
        return !empty($this->getEWalletCouponCode());
    }

    /**
     * @param EWalletCoupon $coupon
     * @throws Exception
     */
    public function applyEWalletCoupon($coupon)
    {
        $accountId = $this->getCurrentAccountId();
        $coupon = EWalletCouponModule::prepareCoupon($coupon, $accountId);
        $operationTypeId = $this->couponModule->getPayWalletOperationTypeId();
        try {
            $coupon->payFromWallet($operationTypeId);
        } catch (Exception $exception) {
            $coupon->delete(true);
            throw $exception;
        }
        $this->apply_coupon($coupon->get_code());
    }

    /**
     * @param EWalletCoupon $coupon
     * @throws Exception
     */
    public function removeEWalletCoupon()
    {
        $coupon = $this->getEWalletCoupon();
        $accountId = $this->getCurrentAccountId();
        $coupon = EWalletCouponModule::prepareCoupon($coupon, $accountId, true);

        $operationTypeId = EWalletCouponModule::getInstance()->getCancelPaymentWalletOperationTypeId();

        try {
            $coupon->cancelPayment($operationTypeId);
        } catch (Exception $exception) {
            $this->apply_coupon($coupon->get_code());
            throw $exception;
        }

        $this->remove_coupon($coupon->get_code());

        $coupon->delete();
    }

    public function getEWalletCoupon()
    {
        $code = $this->getEWalletCouponCode();
        if (empty($code)) {
            return null;
        }
        return new EWalletCoupon($code);
    }

    public function getEWalletCouponCode()
    {
        $coupons = $this->get_coupons();
        /** @var WC_Coupon $coupon */
        foreach ($coupons as $coupon) {
            if (EWalletCoupon::isEWalletCoupon($coupon->get_code())) {
                return $coupon->get_code();
            }
        }
        return null;
    }

    public function getCurrentAccountId()
    {
        $accountId = $this->getOwnedAccountId();
        if (!$accountId) {
            throw new Exception(MLMSoftPlugin::translate('Account ID not found'));
        }
        return $accountId;
    }
}