<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules;

use Exception;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\MLMSoftEWalletGateway;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletCartHelper;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletCoupon;
use MLMSoft\traits\SingletonTrait;
use WC_Coupon;

class EWalletCartModule
{
    use SingletonTrait;

    public function __construct()
    {
        add_filter('woocommerce_available_payment_gateways', [$this, 'removeGatewayIfPaid']);
        add_filter('woocommerce_cart_totals_coupon_label', [$this, 'getCouponLabel'], 10, 2);
        add_action('woocommerce_removed_coupon', [$this, 'couponRemoved']);
        add_action('woocommerce_cart_item_set_quantity', [$this, 'cartUpdated']);
        add_action('woocommerce_add_to_cart', [$this, 'cartUpdated']);
        add_action('woocommerce_cart_item_removed', [$this, 'cartUpdated']);
        add_action('woocommerce_cart_item_restored', [$this, 'cartUpdated']);
    }

    /**\
     * @param array $gateways
     * @return mixed
     */
    public function removeGatewayIfPaid($gateways)
    {
        if (EWalletCartHelper::getInstance()->isCouponAppliedToCart()) {
            unset($gateways[MLMSoftEWalletGateway::GATEWAY_ID]);
        }
        return $gateways;
    }

    /**
     * @param string $label
     * @param WC_Coupon $coupon
     * @return string
     */
    public function getCouponLabel($label, $coupon)
    {
        if (EWalletCoupon::isEWalletCoupon($coupon->get_code())) {
            return MLMSoftPlugin::translate('Payment from an e-wallet');
        }
        return $label;
    }

    /**
     * @param string $code
     */
    public function couponRemoved($code)
    {
        if (EWalletCoupon::isEWalletCoupon($code)) {
            $coupon = new EWalletCoupon($code);
            if (!$coupon->get_id()) {
                return;
            }
            try {
                EWalletCartHelper::getInstance()->removeEWalletCoupon($coupon);
            } catch (Exception $exception) {
                wc_add_notice($exception->getMessage(), 'error');
            }
        }
    }

    /**
     * @throws Exception
     */
    public function cartUpdated()
    {
        $eWalletCartHelper = EWalletCartHelper::getInstance();
        $coupon = $eWalletCartHelper->getCartCoupon();
        if (!empty($coupon)) {
            EWalletCartHelper::getInstance()->removeEWalletCoupon($coupon);
        }
    }
}