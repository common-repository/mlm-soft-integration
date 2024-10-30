<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet;

use MLMSoft\components\common\PublicComponentLoader;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\coupons\EWalletCouponAdminApi;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\coupons\EWalletCouponFrontend;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules\EWalletCartModule;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules\EWalletCouponModule;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules\EWalletOrderModule;
use WC_Payment_Gateway;

// @since 3.6.6
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;

class MLMSoftEWalletGateway extends WC_Payment_Gateway
{
    public const GATEWAY_ID = 'e-wallet';

    private $componentLoader;

    public function __construct()
    {
        $this->id = self::GATEWAY_ID;
        $this->icon = '';
        $this->has_fields = true;
        $this->method_title = MLMSoftPlugin::translate('MLM Soft E-Wallet');
        $this->method_description = MLMSoftPlugin::translate('Payment from MLM Soft e-wallet balance');

        /**
         * Checking coupon activity.
         *
         * @since 3.5.7
         */
        if ( ! wc_coupons_enabled() ) {
            
            $url = add_query_arg( 
                array(
                    'page' => 'wc-settings',
                    'tab' => 'general',
                ),
                admin_url('admin.php') 
            );

            $this->method_description .= '<br />';
            $this->method_description .= sprintf(
                MLMSoftPlugin::translate('You should %1sEnable coupons%2s to start using'),
                '<a href="'.$url.'">',
                '</a>'
            );
            
            /**
             * Hide the status button for our gateway. 
             */
            add_action('admin_print_styles', array($this, 'gateway_admin_styles'));				
        }

        $this->supports = array(
            'products'
        );
        $this->init_settings();
        $this->init_form_fields();
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');
        $this->componentLoader = new PublicComponentLoader();

        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
        
        /**
         * @since 3.6.6
         */
        add_action('wp_print_styles', array($this, 'checkout_page_styles'));
    }

    public static function initGateway()
    {
        EWalletApi::getInstance();
        EWalletCouponAdminApi::getInstance();
        EWalletCartModule::getInstance();
        EWalletOrderModule::getInstance();
        EWalletCouponModule::getInstance();
        EWalletCouponFrontend::getInstance();
    }

    public function init_form_fields()
    {
        $this->form_fields = array(
            'title' => array(
                'title' => MLMSoftPlugin::translate('Title'),
                'type' => 'text',
                'description' => MLMSoftPlugin::translate('Name of payment method'),
                'default' => 'E-Wallet',
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => MLMSoftPlugin::translate('Description'),
                'type' => 'textarea',
                'description' => MLMSoftPlugin::translate('Description of payment method'),
                'default' => MLMSoftPlugin::translate('Payment with bonuses from an e-wallet'),
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function payment_scripts()
    {
        if (!is_cart() && !is_checkout() && !isset($_GET['pay_for_order'])) {
            return;
        }
        $this->componentLoader->enqueue();
    }
    
    /**
     * @since 3.6.6
     */
    public function checkout_page_styles()
    {
        $useCss = WCIntegrationOptions::getInstance()->useCheckoutCss;
        $css = WCIntegrationOptions::getInstance()->checkoutCss;

        if ( $useCss && $this->componentLoader->getStylesCount() > 0 ) {
            $key = $this->componentLoader->getStylesPrefix() . '0';
            wp_add_inline_style($key, $css);
        }
    }
    
    /**
     * Hide the status button for our gateway.
     *
     * @since 3.5.7
     */
    public function gateway_admin_styles()
    { 
        if ( isset($_GET['page'], $_GET['tab']) && 'checkout' === $_GET['tab'] ) {
            $key = 'woocommerce_admin_menu_styles';
            $styles = '.woocommerce_page_wc-settings tr[data-gateway_id="'.self::GATEWAY_ID.'"] .wc-payment-gateway-method-toggle-enabled{display:none;}';
            wp_add_inline_style($key, $styles);			
        }
    }

    public function payment_fields()
    {
        $this->componentLoader->showComponent('e-wallet-payment');
    }

    public function process_payment($order_id)
    {
        wc_add_notice(MLMSoftPlugin::translate('This payment system is not meant for direct payments.'), 'error');
    }
}
