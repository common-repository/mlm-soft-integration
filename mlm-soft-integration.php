<?php

/*
Plugin Name: MLM Soft Integration
Description: WP integration with mlm-soft.com cloud platform
Version: 3.6.6
Author: MLM Soft Ltd.
Author URI: https://mlm-soft.com
Text Domain: mlmsoft
License: GPLv2
*/

use MLMSoft\core\MLMSoftDebug;

define('MLMSOFT_V3_PLUGIN_FILE', __FILE__);

spl_autoload_register(function ($className) {
    if (strpos($className, 'MLMSoft\\') === 0) {
        $className = substr_replace($className, plugin_dir_path(__FILE__), 0, strlen('MLMSoft\\'));
        $className = str_replace('\\', '/', $className);
        include $className . '.php';
    }
});

add_action('plugins_loaded', array('MLMSoft\core\MLMSoftPlugin', 'getInstance'), 100);

function add_cors_http_header()
{
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Origin: ' . MLMSoftDebug::getCorsHost(), true);
    header('Access-Control-Allow-Credentials: true', true);
    header("Access-Control-Allow-Methods: *", true);
    header('Access-Control-Max-Age: 3600', true);
    header('Access-Control-Allow-Headers: authorization, content-type, xsrf-token, origin', true);

    if ('OPTIONS' == $_SERVER['REQUEST_METHOD']) {
        status_header(200);
        exit();
    }
}

if (MLMSoftDebug::isDebug()) {
    add_action('init', 'add_cors_http_header');
}
