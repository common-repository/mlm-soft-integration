<?php

namespace MLMSoft\integrations\pos;

use MLMSoft\core\base\MLMSoftIntegrationBase;
use MLMSoft\integrations\pos\admin\PosAdminApi;
use MLMSoft\integrations\pos\modules\PosCheckoutModule;
use MLMSoft\integrations\pos\modules\PosOrderModule;
use MLMSoft\integrations\woocommerce\WCIntegration;

class PosIntegration extends MLMSoftIntegrationBase
{
    public $dependentIntegrations = [
        WCIntegration::class
    ];

    /** @var PosIntegrationOptions */
    protected $options;

    public function __construct()
    {
        $this->options = new PosIntegrationOptions();
    }

    public function isEnabled()
    {
        return $this->options->enabled;
    }

    public function init()
    {
        new PosAdminApi();
        new PosOrderModule();
        new PosCheckoutModule();
    }
}