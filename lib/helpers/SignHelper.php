<?php

namespace MLMSoft\lib\helpers;

use MLMSoft\core\MLMSoftDebug;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftLocalUser;

class SignHelper
{
    public const SIGN_DATA_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'sign_data';
    public const SIGN_KEY_EXPIRATION = 300;

    public static function createSignKey()
    {
        $user = MLMSoftLocalUser::loadFromCurrent();
        if (!$user->ID) {
            session_start();
            $existsValue = $_SESSION[self::SIGN_DATA_KEY] ?? null;
            if (!isset($existsValue, $existsValue['expiration']) || $existsValue['expiration'] > time()) {
                $salt = bin2hex(random_bytes(64));
                $key = self::getSignKey($salt);
                $_SESSION[self::SIGN_DATA_KEY] = [
                    'key' => $key,
                    'salt' => $salt,
                    'expiration' => time() + self::SIGN_KEY_EXPIRATION
                ];
            } else {
                $key = $existsValue['key'];
            }
            session_write_close();
        } else {
            $key = $user->getUserKey();
            $key = hash('sha256', $key);
        }

        return $key;
    }

    public static function validate($body)
    {
        if (MLMSoftDebug::isDebug()) {
            return null;
        }
        $user = MLMSoftLocalUser::loadFromCurrent();
        if (!$user->ID) {
            session_start();
            $sessionKey = $_SESSION[self::SIGN_DATA_KEY];
            session_write_close();
            if (!isset($sessionKey, $sessionKey['salt'])) {
                return MLMSoftPlugin::translate('Session not found');
            }
            $salt = $sessionKey['salt'];
            $key = self::getSignKey($salt);
        } else {
            $key = $user->getUserKey(false);
            $key = hash('sha256', $key);
        }
        if (!isset($_GET['timestamp'])) {
            return MLMSoftPlugin::translate('Request validation error (time)');
        }
        if (!isset($_GET['digest'])) {
            return MLMSoftPlugin::translate('Request validation error (digest)');
        }
        $timestamp = $_GET['timestamp'];
        unset($_GET['timestamp']);

        $digest = $_GET['digest'];
        unset($_GET['digest']);

        if (empty($body)) {
            $body = '{}';
        } else {
            $body = json_encode($body, JSON_UNESCAPED_SLASHES);
        }

        $query = http_build_query($_GET);

        $digestMessage = $body . '|' . $timestamp . '|' . $query;

        $internalDigest = hash_hmac('sha256', $digestMessage, $key);

        if ($digest != $internalDigest) {
            return MLMSoftPlugin::translate('Request validation error (internal digest)');
        }

        return null;
    }

    private static function getSignKey($salt)
    {
        $user = wp_get_current_user();
        if (!empty($user)) {
            $userData = [
                'userId' => $user->ID,
                'salt' => $salt
            ];
        } else {
            $userData = [
                'userId' => 0,
                'salt' => $salt
            ];
        }
        $key = hash('sha256', json_encode($userData));

        return $key;
    }
}