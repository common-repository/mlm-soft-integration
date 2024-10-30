<?php

namespace MLMSoft\integrations\woocommerce\paymentGateways\eWallet;

use Exception;
use MLMSoft\core\MLMSoftDebug;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftLocalUser;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletCartHelper;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\models\EWalletCoupon;
use MLMSoft\integrations\woocommerce\paymentGateways\eWallet\modules\EWalletCouponModule;
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;
use MLMSoft\lib\helpers\ArrayHelper;
use MLMSoft\traits\SignedAjaxApiTrait;
use MLMSoft\traits\SingletonTrait;

class EWalletApi
{
    use SignedAjaxApiTrait;
    use SingletonTrait;

    public const API_ENDPOINT = 'e-wallet-api';
    public const GATEWAY_E_WALLETS_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'gateway_e_wallets';

    public function __construct()
    {
        $this->addHandler('get-payment-info', [$this, 'getPaymentInfo']);
        $this->addHandler('pay-with-bonuses', [$this, 'endpointPayWithBonuses']);
        $this->initAdmin(self::API_ENDPOINT, true);
    }

    public function getPaymentInfo()
    {
        $accountId = $this->getAccountId();
        $mlmSoftPlugin = MLMSoftPlugin::getInstance();
        $wallets = $mlmSoftPlugin->api3->get("account/$accountId/wallet");
        $currencyWalletMatches = WCIntegrationOptions::getInstance()->currencyWalletMatch;
        $currency = get_woocommerce_currency();
        $currentMatches = [];
        if (!empty($currencyWalletMatches)) {
            foreach ($currencyWalletMatches as $match) {
                if ($match['currency'] == $currency) {
                    $currentMatches[] = $match;
                }
            }
        }

        $walletMap = ArrayHelper::map($wallets, 'id', function ($array) {
            return $array;
        });

        $couponModule = EWalletCouponModule::getInstance();
        $maxAmount = $couponModule->getMaxAmount();
        $orderTotal = $couponModule->getOrderTotal();

        foreach ($currentMatches as $match) {
            $walletId = $match['walletId'];
            if (!empty($walletMap[$walletId])) {
                $maxPercent = $match['maxPercent'] / 100;
                if ($maxPercent > 0) {
                    $walletMap[$walletId]['maxAmount'] = round($orderTotal * $maxPercent, 2);
                }
                $result[] = $walletMap[$walletId];
            }
        }
        $wallets = apply_filters(self::GATEWAY_E_WALLETS_FILTER, $result);

        $currency = $couponModule->getExternalCurrency();
        return [
            'wallets' => $wallets,
            'currency' => $currency,
            'maxAmount' => $maxAmount
        ];
    }

    public function endpointPayWithBonuses($body)
    {
        if (!isset($body, $body['amount'], $body['walletId'])) {
            $this->sendError(MLMSoftPlugin::translate('Request validation error (required fields)'));
        }
        $amount = floatval($body['amount']);
        $walletId = $body['walletId'];
        $maxAmount = $this->getMAxAmountForWallet($walletId);
        if ($amount > $maxAmount) {
            $this->sendError(MLMSoftPlugin::translate('Amount must be less than {{maxAmount}}', ['maxAmount' => $maxAmount]));
        }
        $accountId = $this->getAccountId();
        $coupon = EWalletCoupon::create($accountId, $walletId, $amount, EWalletCouponModule::E_WALLET_EXPIRATION_TIME);
        try {
            EWalletCartHelper::getInstance()->applyEWalletCoupon($coupon);
        } catch (Exception $exception) {
            $this->sendError($exception->getMessage());
        }
        return true;
    }

    private function getAccountId()
    {
        $mlmsoftUser = MLMSoftLocalUser::loadFromCurrent();
        $accountId = $mlmsoftUser->getAccountId();
        if (!$accountId) {
            $accountId = MLMSoftDebug::debugAccountId();
        }
        if (!$accountId) {
            $this->sendError(MLMSoftPlugin::translate('Account ID not found'));
        }
        return $accountId;
    }

    private function getMAxAmountForWallet($walletId)
    {
        $currencyWalletMatches = WCIntegrationOptions::getInstance()->currencyWalletMatch;

        $couponModule = EWalletCouponModule::getInstance();
        $maxAmount = $couponModule->getMaxAmount();
        $orderTotal = $couponModule->getOrderTotal();

        $currency = get_woocommerce_currency();
        if (!empty($currencyWalletMatches)) {
            foreach ($currencyWalletMatches as $match) {
                if ($match['currency'] == $currency) {
                    if ($walletId == $match['walletId'] && !empty($match['maxPercent'])) {
                        $maxPercent = $match['maxPercent'] / 100;
                        if ($maxPercent > 0) {
                            $walletMaxAmount = round($orderTotal * $maxPercent, 2);
                            return min($walletMaxAmount, $maxAmount);
                        }
                    }
                }
            }
        }

        return $maxAmount;
    }
}
