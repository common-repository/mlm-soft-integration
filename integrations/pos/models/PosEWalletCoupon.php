<?php

namespace MLMSoft\integrations\pos\models;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletCoupon;
use MLMSoft\traits\EntityMetaProperties;

/**
 * @property integer $paymentId [pos_payment_id]
 */
class PosEWalletCoupon extends EWalletCoupon
{
    use EntityMetaProperties;

    public function createPosPayment($documentId)
    {
        if ($this->isPaid()) {
            $amount = $this->getPayAmount();
            $payment = MLMSoftPlugin::getInstance()->api3->post('pos/order/' . $documentId . '/payment/wallet/' . $this->getWalletId(), [
                'amount' => $amount,
                'comment' => MLMSoftPlugin::translate('Pay from e-wallet - {{couponCode}}', ['couponCode' => $this->get_code()])
            ]);
            $this->paymentId = $payment['id'];
            $this->save();
        }
    }

    public function deletePosPayment($documentId)
    {
        if ($this->paymentId) {
            MLMSoftPlugin::getInstance()->api3->delete('pos/order/' . $documentId . '/payment/' . $this->paymentId);
            $this->paymentId = null;
            $this->save();
        }
    }

    protected function getMetaPrefix()
    {
        return MLMSoftPlugin::PLUGIN_PREFIX;
    }
}