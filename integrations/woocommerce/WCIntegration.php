<?php

namespace MLMSoft\integrations\woocommerce;

use MLMSoft\core\base\MLMSoftIntegrationBase;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\integrations\woocommerce\admin\WCAdmin;
use MLMSoft\integrations\woocommerce\modules\WCAccountModule;
use MLMSoft\integrations\woocommerce\modules\WCCheckoutModule;
use MLMSoft\integrations\woocommerce\modules\WCOrderModule;
use MLMSoft\integrations\woocommerce\modules\WCPaymentModule;
use WC_Checkout;

class WCIntegration extends MLMSoftIntegrationBase
{
    public const SPONSOR_ID_FIELD_NAME = 'wc_integration_sponsor_id';
    public const SPONSOR_REF_CODE_FIELD_NAME = 'wc_integration_sponsor_referral_code';
    public const SPONSOR_REF_CODE_FIELD_DATA_NAME = 'wc_integration_sponsor_referral_code_data';

    public const PRODUCT_VOLUME_PROPERTY_META_PREFIX = MLMSoftPlugin::PLUGIN_PREFIX . 'product_volume_property_';

    public const PRODUCT_VOLUME_TYPE_STATUS = 'status';
    public const PRODUCT_VOLUME_TYPE_VOLUME = 'volume';

    public $dependencies = [
        WC_Checkout::class
    ];

    public const WC_STATUS_TO_MLMSOFT_INVOICE_STATUS = [
        'pending' => 'new',
        'processing' => 'processing',
        'on-hold' => 'unpaid',
        'completed' => 'completed',
        'cancelled' => 'canceled',
        'refunded' => 'refunded',
        'failed' => 'payment-error',
    ];

    /**
     * @var WCIntegrationOptions
     */
    private $options;

    public function __construct()
    {
        $this->options = new WCIntegrationOptions();
    }

    public function isEnabled()
    {
        return $this->options->enabled;
    }

    public function init()
    {
        new WCAdmin($this->options);
        new WCAccountModule($this->mlmsoftPlugin, $this->options);
        new WCCheckoutModule();
        new WCOrderModule($this->mlmsoftPlugin, $this->options);
        new WCPaymentModule();
    }
}
