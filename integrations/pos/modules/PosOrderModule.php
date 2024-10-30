<?php

namespace MLMSoft\integrations\pos\modules;

use MLMSoft\integrations\pos\models\PosEWalletCoupon;
use MLMSoft\integrations\pos\models\PosOrder;
use MLMSoft\integrations\woocommerce\models\order\MLMSoftWCOrder;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletCoupon;
use WC_Coupon;

class PosOrderModule
{
    public function __construct()
    {
        add_action('woocommerce_checkout_order_created', array($this, 'checkoutOrderCreated'), 10, 1);
        add_action('woocommerce_order_status_changed', array($this, 'orderChanged'), 10, 1);
        add_action('woocommerce_decrease_coupon_usage_count', [$this, 'decreaseCouponUsageCount']);
        if (class_exists(MLMSoftWCOrder::class)) {
            add_action(MLMSoftWCOrder::VOLUMES_SENT_ACTION, [$this, 'updateVolumeSentFlag'], 10, 1);
        }
    }

    /**
     * @param \WC_Order $order
     * @throws \Exception
     */
    public function checkoutOrderCreated($order)
    {
        $posOrder = new PosOrder($order);
        if (isset($_REQUEST[PosCheckoutModule::WC_POS_WAREHOUSE_FILED_NAME])) {
            $posOrder = new PosOrder($order);
            $posOrder->posId = $_REQUEST[PosCheckoutModule::WC_POS_WAREHOUSE_FILED_NAME];
            $posOrder->save();
        }
        $posOrder->createPosOrder();
    }

    /**
     * @param \WC_Order $order
     * @throws \Exception
     */
    public function orderChanged($order)
    {
        $posOrder = new PosOrder($order);
        $posOrder->updatePosOrder();
    }

    /**
     * @param WC_Coupon $coupon
     */
    public function decreaseCouponUsageCount($coupon)
    {
        if (EWalletCoupon::isEWalletCoupon($coupon->get_code())) {
            $coupon = new PosEWalletCoupon($coupon->get_code());
            $order = new PosOrder($coupon->getOrderId());
            if (!empty($order) && $order->posDocumentId) {
                $coupon->deletePosPayment($order->posDocumentId);
            }
        }
    }

    /**
     * @param MLMSoftWCOrder $order
     */
    public function updateVolumeSentFlag($order)
    {
        $posOrder = new PosOrder($order);
        $posOrder->updatePosOrder();
    }
}