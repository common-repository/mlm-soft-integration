<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules;

use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletOrder;
use MLMSoft\traits\SingletonTrait;

class EWalletOrderModule
{
    use SingletonTrait;

    public function __construct()
    {
        add_action('woocommerce_order_status_changed', [$this, 'statusChanged'], 10, 4);
        add_action('woocommerce_checkout_order_created', [$this, 'checkoutOrderCreated'], 10, 4);
    }

    /**
     * @param $orderId
     * @param $statusFrom
     * @param $statusTo
     * @param $order
     * @throws \Exception
     */
    public function statusChanged($orderId, $statusFrom, $statusTo, $order)
    {
        if ($statusTo == 'cancelled') {
            $eWalletOrder = new EWalletOrder($orderId);
            if ($eWalletOrder->isEWalletCouponApplied()) {
                $eWalletOrder->removeEWalletCoupon();
            }
        }
    }

    /**
     * @param \WC_Order $order
     * @throws \Exception
     */
    public function checkoutOrderCreated($order)
    {
        $eWalletOrder = new EWalletOrder($order);
        $coupon = $eWalletOrder->getEWalletCoupon();
        if (!empty($coupon)) {
            $coupon->setOrderId($order->get_id());
            $coupon->save();
        }
    }
}