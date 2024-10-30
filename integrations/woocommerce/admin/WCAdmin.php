<?php

namespace MLMSoft\integrations\woocommerce\admin;

use MLMSoft\integrations\woocommerce\WCIntegration;
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;

class WCAdmin
{
    /**
     * @var WCIntegrationOptions
     */
    private $options;

    /**
     * WCAdmin constructor.
     * @param WCIntegrationOptions $options
     */
    public function __construct($options)
    {
        $this->options = $options;
        WCAdminApi::getInstance();

        add_action('woocommerce_product_options_general_product_data', [$this, 'showProductVolumeFields'], 10, 0);
        add_action('woocommerce_process_product_meta', [$this, 'saveProductVolumeMeta'], 10, 1);
    }

    public function showProductVolumeFields()
    {
        $productVolumeFields = $this->options->volumeProductFields;

        foreach ($productVolumeFields as $field) {
            $alias = WCIntegration::PRODUCT_VOLUME_PROPERTY_META_PREFIX . $field['volumeProperty'];
            echo '<div class="options_group ' . $alias . '" style="background-color: #e3f2ff;">';
            $args = array(
                'id' => $alias,
                'label' => __($field['name'], 'woocommerce-mlm'),
                'placeholder' => __($field['name'], 'woocommerce-mlm'),
                'type' => 'number',
                'desc' => __($field['name'], 'woocommerce-mlm'),
                'desc_tip' => true,
                'custom_attributes' => array(
                    'step' => '0.1',
                    'min' => '0'
                )
            );
            woocommerce_wp_text_input($args);
            echo '</div>';
        }
    }

    public function saveProductVolumeMeta($post_id)
    {
        $productVolumeFields = $this->options->volumeProductFields;

        $product = wc_get_product($post_id);
        foreach ($productVolumeFields as $field) {
            $alias = WCIntegration::PRODUCT_VOLUME_PROPERTY_META_PREFIX . $field['volumeProperty'];
            $productVolumeValue = isset($_POST[$alias]) ? $_POST[$alias] : '';
            $product->update_meta_data($alias, sanitize_text_field($productVolumeValue));
        }
        $product->save();
    }
}