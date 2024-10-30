<?php


namespace MLMSoft\integrations\woocommerce\models\order;


use MLMSoft\core\MLMSoftPlugin;

class MLMSoftV2WCOrder extends MLMSoftWCOrder
{
    public function sendVolumes($documentName = null)
    {
        $accountId = $this->getOwnedAccountId();
        if (!$accountId) {
            return;
        }
        $properties = $this->getVolumes();
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        foreach ($properties as $key => $value) {
            $mlmsoftPlugin->api2->execPost('account/volume-change', [
                'accountId' => $accountId,
                'pointsAmount' => $value,
                'orderId' => (string)$this->get_id(),
                'volumePropertyAlias' => $key
            ]);
        }
    }
}