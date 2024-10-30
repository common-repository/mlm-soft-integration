<?php

namespace MLMSoft\integrations\pos\admin;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\traits\AjaxApiTrait;

class PosAdminApi
{
    use AjaxApiTrait;

    public const POS_ADMIN_API_ENDPOINT = MLMSoftPlugin::PLUGIN_PREFIX . 'pos_admin_api_endpoint';

    public function __construct()
    {
        $this->addHandler('get-pos-order-statuses', [$this, 'getPosStatuses']);

        $this->initAdmin(self::POS_ADMIN_API_ENDPOINT, true);
    }

    public function getPosStatuses()
    {
        $plugin = MLMSoftPlugin::getInstance();
        return $plugin->api3->get('pos/order/document-status');
    }
}