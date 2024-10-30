<?php

namespace MLMSoft\integrations\woocommerce\models\cart;

use MLMSoft\integrations\woocommerce\models\product\MLMSoftWCProduct;
use WC_Cart;

class MLMSoftWCCart
{
    /** @var WC_Cart */
    private $cart;

    /**
     * @param WC_Cart $cart
     */
    public function __construct($cart)
    {
        $this->cart = $cart;
    }

    public function getVolumeSum($alias)
    {
        $result = 0;

        $orderItems = $this->cart->get_cart();
        /**
         * @var integer $item_id
         * @var \WC_Order_Item_Product $item
         */
        foreach ($orderItems as $item_id => $item) {
            /** @var MLMSoftWCProduct $product */
            $product = new MLMSoftWCProduct($item['product_id']);
            $result += (int)$product->getVolume($alias) * $item['quantity'];
        }

        return $result;
    }
}