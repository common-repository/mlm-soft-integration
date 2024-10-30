<?php

namespace MLMSoft\integrations\pos\models;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\integrations\pos\PosIntegrationOptions;
use MLMSoft\integrations\woocommerce\models\product\MLMSoftWCProduct;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletOrder;
use MLMSoft\lib\helpers\ArrayHelper;
use MLMSoft\traits\EntityMetaProperties;
use WC_Order_Item_Product;

/**
 * @property integer $posId
 * @property integer $posOrderCreated
 * @property integer $posDocumentId
 * @property integer $isPaid
 */
class PosOrder extends EWalletOrder
{
    use EntityMetaProperties;

    public const POS_ORDER_DATA_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'pos_order_data';

    public function createPosOrder()
    {
        if ($this->posOrderCreated) {
            return;
        }
        $accountId = $this->getOwnedAccountId();
        if (!$accountId) {
            return;
        }
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        $data = $this->getPosOrderData();
        $document = $mlmsoftPlugin->api3->post("account/$accountId/pos/order", $data);
        $this->posOrderCreated = true;
        $this->posDocumentId = $document['id'];
        $this->save();
        $this->add_order_note(MLMSoftPlugin::translate('Pos order created. Document id: {{documentId}}', ['documentId' => $this->posDocumentId]));
        $coupon = $this->getEWalletCoupon();
        if (!empty($coupon) && $coupon->isPaid()) {
            $coupon = new PosEWalletCoupon($coupon->get_id());
            $coupon->createPosPayment($this->posDocumentId);
            $this->add_order_note(MLMSoftPlugin::translate('Payment from {{couponCode}} created successfully', ['couponCode' => $coupon->get_code()]));
        }
    }

    public function updatePosOrder()
    {
        $accountId = $this->getOwnedAccountId();
        if (!$accountId) {
            return;
        }
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        $data = $this->getPosOrderData();
        $mlmsoftPlugin->api3->put("account/$accountId/pos/order/", $data);
    }

    protected function getPosOrderData()
    {
        $orderItems = $this->get_items();
        $posOrderItems = [];

        /** @var WC_Order_Item_Product $item */
        foreach ($orderItems as $item) {
            $product = new MLMSoftWCProduct($item->get_product());
            $posOrderItems[] = [
                'article' => $product->get_sku(),
                'price' => $product->get_price(null),
                'currency' => get_woocommerce_currency(),
                'quantity' => $item->get_quantity(),
                'properties' => $product->getVolumes()
            ];
        }

        $posOrder = [
            'number' => $this->get_id(),
            'accountId' => $this->getOwnedAccountId(),
            'comment' => $this->get_customer_note(),
            'sum' => floatval($this->get_total(null)),
            'currency' => $this->get_currency(),
            'properties' => $this->getVolumes(),
            'delivery' => [
                'shippingMethod' => $this->get_shipping_method()
            ],
            'items' => $posOrderItems
        ];
        if ($this->isVolumesSent()) {
            $posOrder['isVolumeSent'] = true;
        }

        if ($this->is_paid()) {
            $posOrder['isPaid'] = true;
        }

        $statusMatch = PosIntegrationOptions::getInstance()->posWcStatusMatch;
        if (!empty($statusMatch)) {
            $statusMap = ArrayHelper::map($statusMatch, 'wcStatus', 'posStatus');
            $orderStatus = $this->get_status();
            if (isset($statusMap['wc-' . $orderStatus])) {
                $posOrder['statusId'] = $statusMap['wc-' . $orderStatus];
            }
        }

        if ($this->posId) {
            $posOrder['posId'] = $this->posId;
        }
        return apply_filters_ref_array(self::POS_ORDER_DATA_FILTER, [$posOrder, $this]);
    }

    protected function getMetaPrefix()
    {
        return MLMSoftPlugin::PLUGIN_PREFIX;
    }
}