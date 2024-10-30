<?php

namespace MLMSoft\integrations\woocommerce\modules;

use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\MLMSoftEWalletGateway;

class WCPaymentModule
{
    public function __construct()
    {
        $this->initGateways();
        add_filter('woocommerce_payment_gateways', [$this, 'registerPaymentGateways']);
    }

    public function registerPaymentGateways($methods)
    {
		/**
		 * @since 3.5.7
		 */
		if ( is_admin() ) {
			$methods[] = MLMSoftEWalletGateway::class;
		} else {
			/**
			 * Checking coupon activity.
			 *
			 * @scope front.
			 */
			if ( wc_coupons_enabled() ) {
				$methods[] = MLMSoftEWalletGateway::class;
			}
		}
        
        return $methods;
    }

    private function initGateways()
    {
        MLMSoftEWalletGateway::initGateway();
    }
}