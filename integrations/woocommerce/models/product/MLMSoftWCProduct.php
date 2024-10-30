<?php

namespace MLMSoft\integrations\woocommerce\models\product;

use MLMSoft\integrations\woocommerce\WCIntegration;
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;
use WC_Product;

class MLMSoftWCProduct extends WC_Product
{
    public function getVolumes()
    {
        $productVolumeFields = WCIntegrationOptions::getInstance()->volumeProductFields;
        $result = [];

        foreach ($productVolumeFields as $field) {
            $propertyAlias = $field['volumeProperty'];
            $value = $this->getVolume($propertyAlias);
            $result[$propertyAlias] = $value;
        }

        return $result;
    }

    public function getVolume($alias)
    {
        $alias = WCIntegration::PRODUCT_VOLUME_PROPERTY_META_PREFIX . $alias;
        return $this->get_meta($alias, true);
    }

    public static function getVolumeFromProduct($product, $alias)
    {
        $alias = WCIntegration::PRODUCT_VOLUME_PROPERTY_META_PREFIX . $alias;
        return $product->get_meta($alias, true);
    }
}