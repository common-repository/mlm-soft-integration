<?php

namespace MLMSoft\core\modules;

use MLMSoft\core\MLMSoftPlugin;

class MLMSoftWallet
{
    /**
     * @var MLMSoftPlugin
     */
    private $mlmsoftPlugin;

    public function __construct()
    {
        $this->mlmsoftPlugin = MLMSoftPlugin::getInstance();
    }

    public function getAllWallets()
    {
        return $this->mlmsoftPlugin->api2->execGet('wallet/get-list');
    }

    public function getWalletsBalance($accountId)
    {
        return $this->mlmsoftPlugin->api3->get("account/$accountId/wallet");
    }

    /**
     * @param integer $accountId
     * @param float $amount
     * @param string $walletAlias
     * @param integer $walletOperationTypeId
     * @param string $comment
     * @return bool
     * @throws \HttpException
     */
    public function addWalletOperation($accountId, $amount, $walletAlias, $walletOperationTypeId, $comment = '')
    {
        return $this->mlmsoftPlugin->api3->post("account/$accountId/wallet/$walletAlias/transaction", [
            'operationTypeId' => $walletOperationTypeId,
            'amount' => $amount,
            'comment' => $comment
        ]);
    }
}
