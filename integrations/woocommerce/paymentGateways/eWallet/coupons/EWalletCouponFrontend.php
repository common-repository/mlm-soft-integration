<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet\coupons;

use Exception;
use HttpException;
use MLMSoft\components\admin\AdminComponentLoader;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftRemoteUser;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletCoupon;
use MLMSoft\traits\SingletonTrait;
use WC_Coupon;

class EWalletCouponFrontend
{
    use SingletonTrait;

    public const E_WALLET_COUPON_DATA_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'e-wallet-coupon';
    public const E_WALLET_COUPON_DATA_PANEL_TARGET = MLMSoftPlugin::PLUGIN_PREFIX . 'e-wallet-info';

    /** @var AdminComponentLoader */
    private $adminComponentLoader;

    public function __construct()
    {
        add_filter('woocommerce_coupon_data_tabs', [$this, 'addDataTab']);
        add_action('woocommerce_coupon_data_panels', [$this, 'addCouponDataPanel'], 1, 2);

        $this->adminComponentLoader = new AdminComponentLoader();
    }

    public function addDataTab($data)
    {
        $data[self::E_WALLET_COUPON_DATA_KEY] = [
            'label' => MLMSoftPlugin::translate('EWallet info'),
            'target' => self::E_WALLET_COUPON_DATA_PANEL_TARGET,
            'class' => '',
        ];
        return $data;
    }

    /**
     * @param $couponId
     * @param WC_Coupon $coupon
     * @throws HttpException
     */
    public function addCouponDataPanel($couponId, $coupon)
    {
        echo '<div id="' . self::E_WALLET_COUPON_DATA_PANEL_TARGET . '" class="panel woocommerce_options_panel">';
        if (!EWalletCoupon::isEWalletCoupon($coupon->get_code())) {
            echo '<div style="margin: 10px">' . MLMSoftPlugin::translate('Coupon is not connected to the e-wallet') . '</div>';
        } else {
            $coupon = new EWalletCoupon($coupon->get_code());
            if (!$coupon->getAccountId()) {
                echo '<div style="margin: 10px">' . MLMSoftPlugin::translate('Account ID not found in the coupon (old version)') . '</div>';
            } else {
                $this->showInfo($coupon);
            }
        }
        echo '</div>';
    }

    /**
     * @param EWalletCoupon $coupon
     * @throws HttpException
     */
    public function showInfo($coupon)
    {
        $mlmSoftPlugin = MLMSoftPlugin::getInstance();
        $accountId = $coupon->getAccountId();
        $remoteUser = MLMSoftRemoteUser::loadByAccountId($accountId);
        try {
            $wallets = $mlmSoftPlugin->api3->get("account/$accountId/wallet");
        } catch (Exception $exception) {
            $wallets = [];
        }
        $walletId = $coupon->getWalletId();
        $couponWallet = [];
        foreach ($wallets as $wallet) {
            if ($wallet['id'] == $walletId) {
                $couponWallet = $wallet;
                break;
            }
        }
        $link = admin_url() . 'edit.php?post_type=shop_coupon';
        $this->adminComponentLoader->addScriptParams('couponEWalletParams', [
            'coupon' => [
                'code' => $coupon->get_code(),
                'isPaid' => $coupon->isPaid(),
                'wallet' => $couponWallet,
                'payAmount' => $coupon->getPayAmount()
            ],
            'redirectLink' => $link,
            'account' => $remoteUser->getAccount()
        ]);
        $this->adminComponentLoader->showComponent('coupon-e-wallet-info');
    }
}