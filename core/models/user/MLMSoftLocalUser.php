<?php


namespace MLMSoft\core\models\user;


use MLMSoft\core\MLMSoftDebug;
use MLMSoft\core\MLMSoftPlugin;
use stdClass;
use WP_Error;
use WP_Roles;
use WP_User;

class MLMSoftLocalUser extends WP_User
{
    const KEY_EXPIRATION = 7; //days

    const ACCOUNT_ID_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'account_id';
    const INVITE_CODE_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'invite_code';
    const STATUS_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'status';
    const USER_KEY_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'user_key';
    const USER_KEY_EXPIRATION_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'user_key_expiration';
    const INTERNAL_USER_FLAG_META_KEY = MLMSoftPlugin::PLUGIN_PREFIX . 'internal_user';

    /**
     * @var MLMSoftPlugin
     */
    private $mlmsoftPlugin;

    /**
     * @param int|string|stdClass|WP_User $id
     */
    public function __construct($id)
    {
        parent::__construct($id);
        $this->mlmsoftPlugin = MLMSoftPlugin::getInstance();
    }

    /**
     * @return MLMSoftLocalUser
     */
    public static function loadFromCurrent()
    {
        $user = wp_get_current_user();
        if (!$user) {
            $accountId = MLMSoftDebug::debugAccountId();
            if ($accountId) {
                return self::loadByAccountId($accountId);
            }
        }
        return new MLMSoftLocalUser($user);
    }

    /**
     * @param $accountId
     * @return false|static
     */
    public static function loadByAccountId($accountId)
    {
        $users = get_users(['meta_key' => self::ACCOUNT_ID_META_KEY, 'meta_value' => $accountId]);
        $user = isset($users[0]) ? $users[0] : null;
        if (!$user) {
            return false;
        }
        return new static($user);
    }

    /**
     * @param MLMSoftRemoteUser $remoteUser
     * @return false|static|WP_Error
     */
    public static function loadFromRemote($remoteUser)
    {
        $accountUser = self::loadByAccountId($remoteUser->getAccountField('id', ''));
        if ($accountUser) {
            return $accountUser;
        }

        $emailUser = get_user_by('email', $remoteUser->getProfileFieldValue('email'));
        if (is_wp_error($emailUser)) {
            return $emailUser;
        }
        if ($emailUser) {
            return new static($emailUser);
        }
        return false;
    }

    /**
     * @param MLMSoftRemoteUser $remoteUser
     * @return false|static
     */
    public static function create($remoteUser)
    {
        $title = $remoteUser->getAccountField('title');

        $userdata = [
            'user_pass' => 'k9sdfk23asdf9s',
            'user_login' => $remoteUser->getProfileFieldValue('login'),
            'first_name' => $remoteUser->getProfileFieldValue('firstname'),
            'last_name' => $remoteUser->getProfileFieldValue('lastname'),
            'user_email' => $remoteUser->getProfileFieldValue('email'),
            'display_name' => $title,
            'nickname' => $title,
        ];

        $user_id = wp_insert_user($userdata);

        if (is_wp_error($user_id)) {
            return $user_id;
        }
        return new static($user_id);
    }

    /**
     * @return int|string
     */
    public function getAccountId()
    {
        $accountId = get_user_meta($this->ID, self::ACCOUNT_ID_META_KEY, true);
        if (empty($accountId)) {
            return 0;
        }
        return $accountId;
    }

    /**
     * @param integer $accountId
     */
    public function setAccountId($accountId)
    {
		/**
        $existsAccountId = get_user_meta($this->ID, self::ACCOUNT_ID_META_KEY, true);
        if ($existsAccountId) {
            update_user_meta($this->ID, self::ACCOUNT_ID_META_KEY, $accountId);
        } else {
            add_user_meta($this->ID, self::ACCOUNT_ID_META_KEY, $accountId);
        }
		// */
		
		/**
		 * Using `update_user_meta` function only to prevent adding multiple meta for each update.
		 * ( @see Description https://developer.wordpress.org/reference/functions/update_user_meta/ )
		 *
		 * @since 3.4.13
		 */
        update_user_meta($this->ID, self::ACCOUNT_ID_META_KEY, $accountId);
    }

    /**
     * @return bool
     */
    public function isInternalUser()
    {
        return boolval(get_user_meta($this->ID, self::INTERNAL_USER_FLAG_META_KEY, true));
    }

    /**
     * @param boolean $flag
     */
    public function setInternalUser($flag)
    {
        update_user_meta($this->ID, self::INTERNAL_USER_FLAG_META_KEY, $flag);
    }

    /**
     * @param $password
     */
    public function setPassword($password)
    {
        wp_set_password($password, $this->ID);
    }

    /**
     * @param MLMSoftRemoteUser $remoteUser
     */
    public function syncWithRemote($remoteUser)
    {
        $this->updateMeta($remoteUser);
        $this->updatePropertyValues($remoteUser);
        $this->updateUserRole($remoteUser);
    }

    /**
     * @param MLMSoftRemoteUser $remoteUser
     * @return bool
     */
    public function updateMeta($remoteUser)
    {
        $accountId = $remoteUser->getAccountField('id');
        if (!$accountId) {
            return false;
        }

        if (!get_user_meta($this->ID, self::ACCOUNT_ID_META_KEY)) {
            add_user_meta($this->ID, self::ACCOUNT_ID_META_KEY, $accountId);
        } else {
            update_user_meta($this->ID, self::ACCOUNT_ID_META_KEY, $accountId);
        }

        $inviteCode = $remoteUser->getAccountField('invite_code');

        if (!get_user_meta($this->ID, self::INVITE_CODE_META_KEY)) {
            add_user_meta($this->ID, self::INVITE_CODE_META_KEY, $inviteCode);
        } else {
            update_user_meta($this->ID, self::INVITE_CODE_META_KEY, $inviteCode);
        }

        return true;
    }

    /**
     * @param MLMSoftRemoteUser $remoteUser
     * @return bool
     */
    public function updatePropertyValues($remoteUser)
    {
        $propertyData = $remoteUser->getPlanProperties();
        if (!$propertyData) {
            return false;
        }

        $statusPropertyAlias = $this->mlmsoftPlugin->options->statusPropertyAlias;

        if ($statusPropertyAlias && isset($propertyData[$statusPropertyAlias])) {
            $value = $propertyData[$statusPropertyAlias]['value'];
            update_user_meta($this->ID, self::STATUS_META_KEY, $value['raw']);
            return true;
        }

        return false;
    }

    /**
     * @param MLMSoftRemoteUser $remoteUser
     * @return bool
     */
    public function updateUserRole($remoteUser)
    {
        $propertyData = $remoteUser->getPlanProperties();
        if (!$propertyData) {
            return false;
        }

        $statusPropertyAlias = $this->mlmsoftPlugin->options->statusPropertyAlias;

        if ($statusPropertyAlias && isset($propertyData[$statusPropertyAlias])) {
            $status = $propertyData[$statusPropertyAlias]['value']['presentable'];
            return $this->setUserRole($status);
        }

        return false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function setUserRole($name)
    {
        $roles = (new WP_Roles())->roles;
        $roleKey = '';
        foreach ($roles as $key => $role) {
            if (mb_strtolower($role['name']) == mb_strtolower($name)) {
                $roleKey = $key;
                break;
            }
        }
        if (!empty($roleKey)) {
            $this->set_role($roleKey);
            return true;
        }
        return false;
    }

    public function getUserKey($updateIfExpired = true)
    {
        $expiration = get_user_meta($this->ID, self::USER_KEY_EXPIRATION_META_KEY, true);
        $updateKey = false;
        if (!$expiration || (time() > $expiration && $updateIfExpired)) {
            $expiration = time() + self::KEY_EXPIRATION * 24 * 60 * 60;
            update_user_meta($this->ID, self::USER_KEY_EXPIRATION_META_KEY, $expiration);
            $updateKey = true;
        }

        if ($updateKey) {
            $key = base64_encode(random_bytes(64));
            update_user_meta($this->ID, self::USER_KEY_META_KEY, $key);
        } else {
            $key = get_user_meta($this->ID, self::USER_KEY_META_KEY, true);
        }
        return $key;
    }
}
