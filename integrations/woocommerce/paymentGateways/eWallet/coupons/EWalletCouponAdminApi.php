<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet\coupons;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletCoupon;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules\EWalletCouponModule;
use MLMSoft\traits\SignedAjaxApiTrait;
use MLMSoft\traits\SingletonTrait;

class EWalletCouponAdminApi
{
    use SignedAjaxApiTrait;
    use SingletonTrait;

    public const API_ENDPOINT = MLMSoftPlugin::PLUGIN_PREFIX . 'e_wallet_coupon_admin';

    public function __construct()
    {
        $this->addHandler('return-bonuses', [$this, 'returnBonuses']);
        $this->addHandler('get-wallet-types', [$this, 'getWalletTypes']);
        $this->addHandler('get-currencies', [$this, 'getCurrencies']);

        $this->initAdmin(self::API_ENDPOINT, true);
    }

    public function returnBonuses($body)
    {
        if (!isset($body, $body['couponCode']) || !EWalletCoupon::isEWalletCoupon($body['couponCode'])) {
            $this->sendError(MLMSoftPlugin::translate('Coupon not found'));
        }
        $coupon = new EWalletCoupon($body['couponCode']);
        if (!$coupon->get_id()) {
            $this->sendError(MLMSoftPlugin::translate('Coupon not found'));
        }
        if ($coupon->get_usage_count(null) > 0) {
            $this->sendError(MLMSoftPlugin::translate('Cannot return a used coupon'));
        }
        if (!$coupon->getAccountId()) {
            $this->sendError(MLMSoftPlugin::translate('Coupon cannot be returned (Old version)'));
        }
        $operationTypeId = EWalletCouponModule::getInstance()->getCancelPaymentWalletOperationTypeId();
        $coupon->cancelPayment($operationTypeId);
        $coupon->delete();
    }

    public function getWalletTypes()
    {
        $plugin = MLMSoftPlugin::getInstance();
        return $plugin->api3->get('wallet-type');
    }

    public function getCurrencies()
    {
        return get_woocommerce_currencies();
    }
}