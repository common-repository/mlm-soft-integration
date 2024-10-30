<?php

namespace MLMSoft\integrations\pos\modules;

use MLMSoft\components\common\PublicComponentLoader;
use MLMSoft\core\MLMSoftPlugin;

class PosCheckoutModule
{
    public const WC_POS_WAREHOUSE_FILED_NAME = 'wc_warehouse';

    /** @var PublicComponentLoader */
    private $componentLoader;


    public function __construct()
    {
        $this->componentLoader = new PublicComponentLoader();

        add_filter('woocommerce_checkout_fields', [$this, 'addPosWarehouseField'], 10, 2);
        add_filter('woocommerce_form_field', [$this, 'showPosWarehouseField'], 10, 3);
    }

    public function addPosWarehouseField($fields)
    {
        $fields['billing'][self::WC_POS_WAREHOUSE_FILED_NAME] = [
            'type' => 'select',
            'label' => MLMSoftPlugin::translate('Warehouse'),
            'required' => false,
            'placeholder' => MLMSoftPlugin::translate('Warehouse'),
            'field_name' => self::WC_POS_WAREHOUSE_FILED_NAME,
            'class' => ['form-row-wide']
        ];

        return $fields;
    }

    public function showPosWarehouseField($field, $key, $args)
    {
        if ($key == self::WC_POS_WAREHOUSE_FILED_NAME) {
            $api3 = MLMSoftPlugin::getInstance()->api3;
            $warehouses = $api3->get('pos/warehouse');

            $classes = is_array($args['class']) ? implode(' ', $args['class']) : $args['class'];
            $props = [
                'component-id' => $args['id'],
                'component-label' => MLMSoftPlugin::translate('Warehouse'),
                'component-class' => $classes,
                'input-name' => $key,
                'required' => boolval($args['required'])
            ];

            $this->componentLoader->addScriptParams('posWarehouses', [
                'warehouses' => $warehouses
            ]);
            return $this->componentLoader->getComponentHtml('pos-warehouse-select', $props);
        }
        return $field;
    }
}