<?php

namespace MLMSoft\core;

use MLMSoft\admin\MLMSoftAdminPanel;
use MLMSoft\core\api\api2\MLMSoftApi2;
use MLMSoft\core\api\api3\MLMSoftApi3;
use MLMSoft\core\base\MLMSoftIntegrationBase;
use MLMSoft\core\models\user\MLMSoftReferral;
use MLMSoft\core\modules\MLMSoftAuthModule;
use MLMSoft\core\modules\MLMSoftHeadersModule;
use MLMSoft\integrations\pos\PosIntegration;
use MLMSoft\integrations\woocommerce\WCIntegration;
use MLMSoft\pages\admin\user\MLMSoftAccountInfoBlock;
use MLMSoft\traits\SingletonTrait;

class MLMSoftPlugin
{
    use SingletonTrait;

    const PLUGIN_BASE_NAME = 'mlm-soft-integration/mlm-soft-integration.php';
    const PLUGIN_PREFIX = 'mlmsoft_v3_';
    const PLUGIN_SESSION_KEY = 'mlmsoft';
    const TRANSLATION_DOMAIN = 'mlmsoft_v3_plugin';
    const REFERRAL_COOKIE_NAME = 'referral';

    const FILTER_INTEGRATIONS = self::PLUGIN_PREFIX . '_integrations';

    /**
     * @var MLMSoftOptions
     */
    public $options;

    /**
     * @var MLMSoftApi2
     */
    public $api2;

    /**
     * @var MLMSoftApi3
     */
    public $api3;


    /**
     * @var MLMSoftAdminPanel
     */
    private $admin;

    private static $session;

    public function __construct()
    {
        $this->options = new MLMSoftOptions();
        $this->admin = new MLMSoftAdminPanel($this->options);
        $this->api2 = new MLMSoftApi2($this->options);
        $this->api3 = new MLMSoftApi3($this->options);

        $this->registerHooks();
        $this->registerIntegrations();
        $this->registerBlocks();

        load_theme_textdomain(self::TRANSLATION_DOMAIN, plugin_dir_path(MLMSOFT_V3_PLUGIN_FILE) . 'languages');
    }

    public function registerIntegrations()
    {
        $integrations = [
            WCIntegration::class,
            PosIntegration::class
        ];

        $integrations = apply_filters(self::FILTER_INTEGRATIONS, $integrations);
        foreach ($integrations as $integration) {
            /** @var MLMSoftIntegrationBase $integrationInstance */
            $integrationInstance = new $integration();
            $integrationInstance->setPlugin($this);
            $nonExistentDependencies = [];
            foreach ($integrationInstance->dependencies as $dependency) {
                if (!class_exists($dependency)) {
                    $nonExistentDependencies[] = $dependency;
                }
            }
            if ($integrationInstance->isEnabled()) {
                $disabledIntegrations = [];
                if (count($integrationInstance->dependentIntegrations) > 0) {
                    foreach ($integrationInstance->dependentIntegrations as $dependentIntegration) {
                        /** @var MLMSoftIntegrationBase $dependentIntegrationInstance */
                        $dependentIntegrationInstance = new $dependentIntegration();
                        $dependentIntegrationInstance->setPlugin($this);
                        if (!$dependentIntegrationInstance->isEnabled()) {
                            $disabledIntegrations[] = $dependentIntegration;
                        }
                    }
                }
                $initAllowed = true;
                if (count($nonExistentDependencies) > 0) {
                    foreach ($nonExistentDependencies as $dependency) {
                        // echo "There is no $dependency dependence, which is required by $integration";
                    }
                    $initAllowed = false;
                }
                if (count($disabledIntegrations) > 0) {
                    foreach ($disabledIntegrations as $disabledIntegration) {
                        // echo "The $disabledIntegration that is required for $integration is disabled";
                    }
                    $initAllowed = false;
                }
                if ($initAllowed) {
                    $integrationInstance->init();
                }
            }
        }
    }

    private function registerBlocks()
    {
        $pages = [
            MLMSoftAccountInfoBlock::class
        ];
        foreach ($pages as $page) {
            new $page();
        }
    }

    private function registerHooks()
    {
        add_action('init', [$this, 'checkReferral'], 1);
        add_action('init', [$this, 'checkStandardLogin'], 1);
        add_action('init', [$this, 'modulesInit'], 1);
    }

    public function modulesInit()
    {
        new MLMSoftAuthModule();
        new MLMSoftHeadersModule();
    }

    public function checkReferral()
    {
        if (!empty($_GET['referral'])) {
            $this->setReferral($_GET['referral'], isset($_GET['h_info']));
        }
        if (!empty($_COOKIE[self::REFERRAL_COOKIE_NAME])) {
            $user = MLMSoftReferral::loadFromSession();
            if (!$user) {
                if (str_starts_with($_COOKIE[self::REFERRAL_COOKIE_NAME], 'h_')) {
                    $this->setReferral(substr($_COOKIE[self::REFERRAL_COOKIE_NAME], 2), true);
                } else {
                    $this->setReferral($_COOKIE[self::REFERRAL_COOKIE_NAME]);
                }
            }
        }
    }

    public function checkStandardLogin()
    {
        if (!empty($_GET['standard_login'])) {
            setcookie('standard_login', '1');
        }
    }

    public function setReferral($code, $hidden = false)
    {
        $inviteCode = sanitize_text_field($code);
        $referral = MLMSoftReferral::loadByInviteCode($inviteCode);
        if ($referral) {
            $referral->saveToSession($hidden);
            $hostParts = explode('.', $_SERVER['HTTP_HOST']);
            $hostParts = array_reverse($hostParts);
            $cookieHost = $hostParts[1] . '.' . $hostParts[0];
            $cookieValue = $hidden ? 'h_' . $inviteCode : $inviteCode;
            setcookie(self::REFERRAL_COOKIE_NAME, $cookieValue, time() + 730 * 24 * 60 * 60, '/', $cookieHost);
            $_COOKIE[self::REFERRAL_COOKIE_NAME] = $cookieValue;
        }
    }

    public function getSessionValue($key)
    {
        if (headers_sent()) {
            $data = $this->getCacheSession();
            return $data[$key] ?? false;
        }
        $this->startSession();
        $value = $_SESSION[self::PLUGIN_SESSION_KEY][$key] ?? false;
        $this->stopSession();
        return $value;
    }

    public function setSessionValue($key, $value)
    {
        $this->startSession();
        $_SESSION[self::PLUGIN_SESSION_KEY][$key] = $value;
        $this->stopSession();
    }

    public function setSessionValues($array)
    {
        $this->startSession();
        foreach ($array as $key => $value) {
            $_SESSION[self::PLUGIN_SESSION_KEY][$key] = $value;
        }
        $this->stopSession();
    }

    /**
     * @param string $message
     * @return string
     */
    public static function translate($message, $params = [])
    {
        $translatedMessage = __($message, self::TRANSLATION_DOMAIN);
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $translatedMessage = str_replace('{{' . $key . '}}', $value, $translatedMessage);
            }
        }
        return $translatedMessage;
    }

    private function startSession()
    {
        if (!session_id()) {
            session_start();
        }
        if (!isset($_SESSION[self::PLUGIN_SESSION_KEY])) {
            $_SESSION[self::PLUGIN_SESSION_KEY] = [];
        }
    }

    private function stopSession()
    {
        $this->setCacheSession($_SESSION[self::PLUGIN_SESSION_KEY]);
        session_write_close();
    }

    private function getCacheSession()
    {
        $data = wp_cache_get('session', self::PLUGIN_SESSION_KEY);
        if (!$data) {
            return [];
        }
        return $data;
    }

    private function setCacheSession($data)
    {
        wp_cache_set('session', $data, self::PLUGIN_SESSION_KEY);
    }
}
