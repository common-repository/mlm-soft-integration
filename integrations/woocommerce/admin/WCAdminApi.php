<?php

namespace MLMSoft\integrations\woocommerce\admin;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\traits\SignedAjaxApiTrait;
use MLMSoft\traits\SingletonTrait;

class WCAdminApi
{
    use SignedAjaxApiTrait;
    use SingletonTrait;

    public const WC_ADMIN_API_ENDPOINT = MLMSoftPlugin::PLUGIN_PREFIX . 'wc_admin_api_endpoint';

    public function __construct()
    {
        $this->addHandler('get-available-order-statuses', [$this, 'getAvailableOrderStatuses']);

        $this->initAdmin(self::WC_ADMIN_API_ENDPOINT, true);
    }

    public function getAvailableOrderStatuses()
    {
        return wc_get_order_statuses();
    }
}