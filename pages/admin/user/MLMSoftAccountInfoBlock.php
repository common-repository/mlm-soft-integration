<?php

namespace MLMSoft\pages\admin\user;

use MLMSoft\components\admin\AdminComponentLoader;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftLocalUser;
use MLMSoft\traits\SignedAjaxApiTrait;
use WP_User;

class MLMSoftAccountInfoBlock
{
    use SignedAjaxApiTrait;

    public const ENDPOINT_NAME = MLMSoftPlugin::PLUGIN_PREFIX . 'user_profile_api';

    /** @var AdminComponentLoader */
    private $adminComponentLoader;

    public function __construct()
    {
        $this->adminComponentLoader = new AdminComponentLoader();
        add_action('edit_user_profile', [$this, 'showUserAccountInfo'], 10000, 1);
        add_action('show_user_profile', [$this, 'showUserAccountInfo'], 10000, 1);

        $this->addHandler('set-account-id', [$this, 'setAccountId']);
        $this->addHandler('set-wp-user-info', [$this, 'setWpUserInfo']);
        $this->initAdmin(self::ENDPOINT_NAME, true);
    }

    /**
     * @param WP_User $user
     */
    public function showUserAccountInfo($user)
    {
        $user = new MLMSoftLocalUser($user);
        $internalUsers = MLMSoftPlugin::getInstance()->options->allowedUsers;
		
		/**
		 * Typecast to prevent `PHP Fatal error: Argument $haystack must be of type array, string given` in in_array function.
		 * @since 3.4.13
		 */
        $internalUsers = (array) MLMSoftPlugin::getInstance()->options->allowedUsers;		
		
        $login = $user->user_login;
        $internalUser = $user->isInternalUser();
        if (in_array($login, $internalUsers) && !$internalUser) {
            $internalUser = true;
        }
        $this->adminComponentLoader->addScriptParams('accountInfo', ['accountId' => $user->getAccountId(), 'wpUserId' => $user->ID, 'internalUser' => $internalUser]);
        $this->adminComponentLoader->showComponent('account-info');
    }

    public function setAccountId($body)
    {
        $accountId = $body['accountId'];
        $userId = $body['wpUserId'];

        $user = new MLMSoftLocalUser($userId);
        if ($user->ID) {
            $user->setAccountId($accountId);
            return true;
        }
        return false;
    }

    public function setWpUserInfo($body)
    {
        $userId = $body['wpUserId'];

        $user = new MLMSoftLocalUser($userId);
        if ($user->ID) {
            $userInfo = $body['userInfo'];
            $accountId = $userInfo['accountId'];

            $internalUser = boolval($userInfo['internalUser']);

            $options = MLMSoftPlugin::getInstance()->options;
            $allowedUsers = $options->allowedUsers;

            if (!$internalUser) {
                if (is_array($allowedUsers) && in_array($user->user_login, $allowedUsers)) {
                    $allowedUsers = array_diff($allowedUsers, [$user->user_login]);
                    $options->allowedUsers = $allowedUsers;
                }
            } else {
                if (!is_array($allowedUsers)) {
                    $allowedUsers = [];
                }
                if (!in_array($user->user_login, $allowedUsers)) {
                    $allowedUsers[] = $user->user_login;
                    $options->allowedUsers = $allowedUsers;
                }
            }

            $user->setAccountId($accountId);
            $user->setInternalUser($internalUser);
            return true;
        }
        return false;
    }
}