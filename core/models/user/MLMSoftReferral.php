<?php


namespace MLMSoft\core\models\user;


use Exception;
use MLMSoft\core\MLMSoftPlugin;

class MLMSoftReferral extends MLMSoftRemoteUser
{
    const REFERRAL_DATA_SESSION_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'referral_data';

    public static function loadFromSession()
    {
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        if ($referralData = $mlmsoftPlugin->getSessionValue(self::REFERRAL_DATA_SESSION_KEY)) {
            return new MLMSoftReferral($referralData['account'] ?? $referralData);
        }
        return false;
    }


    public static function loadByInviteCode($inviteCode)
    {
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        try {
            $publicAccountData = $mlmsoftPlugin->api3->get('account/search/by-invite-code', ['inviteCode' => $inviteCode]);
            return static::loadByAccountId($publicAccountData['id']);
        } catch (Exception $e) {
        }
        return false;
    }

    public function isHidden()
    {
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        $referralData = $mlmsoftPlugin->getSessionValue(self::REFERRAL_DATA_SESSION_KEY);
        if (!empty($referralData)) {
            return boolval($referralData['hidden']);
        }
        return false;
    }

    public function saveToSession($hidden = false, $key = self::REFERRAL_DATA_SESSION_KEY)
    {
        $this->mlmsoftPlugin->setSessionValue($key, [
            'account' => $this->getAccount(),
            'hidden' => $hidden
        ]);
    }
}
