<?php

namespace MLMSoft\core\modules;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftReferral;
use MLMSoft\core\models\user\MLMSoftRemoteUser;

class MLMSoftHeadersModule
{
    public const AFFILIATE_HEADER_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'affiliate_header_filter';
    public const AUTHORIZED_USER_HEADER_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'authorized_user_header_filter';

    /**
     * @var MLMSoftPlugin
     */
    private $mlmsoftPlugin;

    public function __construct()
    {
        $this->mlmsoftPlugin = MLMSoftPlugin::getInstance();
        add_action('wp_head', [$this, 'addHeader'], 10, 1);
        add_filter(self::AFFILIATE_HEADER_FILTER, [$this, 'getAffiliateHeader'], 100, 1);
        add_filter(self::AUTHORIZED_USER_HEADER_FILTER, [$this, 'getAuthorizedUserHeader'], 100, 1);
    }

    public function addHeader($a)
    {
        if (!is_user_logged_in()) {
            $showHeader = $this->mlmsoftPlugin->options->affiliateHeaderEnabled;
            if ($showHeader) {
                $referral = MLMSoftReferral::loadFromSession();
                if ($referral) {
                    $content = apply_filters(self::AFFILIATE_HEADER_FILTER, $referral);
                    if (!$referral->isHidden()) {
                        $this->showHeader($content);
                    }
                }
            }
        } else {
            $showHeader = $this->mlmsoftPlugin->options->authorizedHeaderEnabled;
            if ($showHeader) {
                $user = wp_get_current_user();
                $remoteUser = MLMSoftRemoteUser::loadByWpUser($user);
                if ($remoteUser) {
                    $content = apply_filters(self::AUTHORIZED_USER_HEADER_FILTER, $remoteUser);
                    $this->showHeader($content);
                }
            }
        }
    }

    /**
     * @param MLMSoftReferral $user
     */
    public function getAffiliateHeader($user)
    {
        $content = MLMSoftPlugin::translate('Opportunity presented by: {{fullName}}', ['fullName' => $user->getFullName()]);
        $content .= '<span style="float: right"><a style="color: #ffffff;" href="/wp-login.php">' . MLMSoftPlugin::translate('Sign In') . '</a></span>';
        $content .= '<span style="margin-right: 10px; float: right"><a style="color: #ffffff;" href="/wp-login.php?action=register&referral=' . $user->getAccountField('invite_code') . '">' . MLMSoftPlugin::translate('Register') . '</a></span>';
        return $content;
    }

    /**
     * @param MLMSoftRemoteUser $user
     * @return false|string
     */
    public function getAuthorizedUserHeader($user)
    {
        $content = $user->getFullName();
        $content .= '<span style="float: right"><a style="color: #ffffff;" href="/wp-login.php?action=logout">' . MLMSoftPlugin::translate('Logout') . '</a></span>';
        $content .= '<span style="margin-right: 10px; float: right"><a style="color: #ffffff;" href="' . $this->mlmsoftPlugin->options->onlineOfficeUrl . '">' . MLMSoftPlugin::translate('Online office') . '</a></span>';
        return $content;
    }

    private function showHeader($content)
    {
        echo '<div style="background: #000000; opacity: 0.5; width: 100%; color: #ffffff; position: fixed; bottom: 0; z-index: 5000; padding: 20px;">';
        echo $content;
        echo '</div>';
    }
}