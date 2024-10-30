<?php

namespace MLMSoft\core;

class MLMSoftDebug
{
    public static function isDebug()
    {
        return defined('MLMSOFT_DEBUG') && MLMSOFT_DEBUG;
    }

    public static function debugAccountId()
    {
        if (!self::isDebug()) {
            return false;
        }
        return defined('MLMSOFT_DEBUG_ACCOUNT') ? MLMSOFT_DEBUG_ACCOUNT : 1;
    }

    public static function getCorsHost()
    {
        if (!self::isDebug()) {
            return false;
        }
        $hosts = defined('MLMSOFT_DEBUG_CORS_HOSTS') ? MLMSOFT_DEBUG_CORS_HOSTS : [];
        $http_origin = $_SERVER['HTTP_ORIGIN'] ?? '';

        if (in_array($http_origin, $hosts)) {
            return $http_origin;
        }
        return '';
    }
}