<?php


namespace MLMSoft\core\models\user;


use Exception;
use MLMSoft\core\MLMSoftPlugin;
use WP_Error;

class MLMSoftRemoteUser
{
    /**
     * @var array
     */
    private $mlmsoftAccount;

    /**
     * @var MLMSoftPlugin
     */
    protected $mlmsoftPlugin;

    private $profile;
    private $planPositions;
    private $planProperties;

    public function __construct($mlmsoftUser)
    {
        $this->mlmsoftAccount = $mlmsoftUser;
        $this->mlmsoftPlugin = MLMSoftPlugin::getInstance();
    }

    public static function loadByAccountId($accountId)
    {
        return new static(self::loadUserData($accountId));
    }

    public static function loadByWpUser($wpUser)
    {
        $localUser = new MLMSoftLocalUser($wpUser);
        $accountId = $localUser->getAccountId();
        if (!$accountId) {
            return false;
        }
        try {
            $data = self::loadUserData($accountId);
        } catch (Exception $e) {
            return false;
        }
        return new static($data);
    }

    public static function loadByAccessToken($token)
    {
        return new static(self::loadUserDataByToken($token));
    }

    public static function loadByLoginAndPass($login, $password)
    {
        $mlmSoftPlugin = MLMSoftPlugin::getInstance();
        try {
            $loginResponse = $mlmSoftPlugin->api3->post('auth/login', [
                'login' => $login,
                'password' => $password,
                'networkAccount' => true
            ]);
        } catch (Exception $e) {
            $errors = new WP_Error();
            
            /**
             * @since 3.6.5
             */
            $message = $e->getMessage();
            if ( $message === 'Login or password is incorrect' ) {
                /**
                 * Translation @see wp-includes\pluggable.php
                 */       
                $message = __( '<strong>Error:</strong> Invalid username, email address or incorrect password.');
            }
            
            $errors->add('error', $message, 'error');
            return $errors;
        }

        if (!$loginResponse['accessToken']) {
            $errors = new WP_Error();
            $errors->add('error', 'Invalid authorization', 'error');
            return $errors;
        }
        return static::loadByAccessToken($loginResponse['accessToken']);
    }

    public static function checkExists($email)
    {
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        $res = $mlmsoftPlugin->api3->post('account/check-exists', [
            'email' => $email
        ]);
        return $res['exists'];
    }

    public static function createNew($login, $password, $sponsorId, $profile)
    {
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();

        $res = $mlmsoftPlugin->api3->post('account/create', [
            'login' => $login,
            'password' => $password,
            'sponsorId' => $sponsorId,
            'profile' => $profile
        ]);

        if (!$res) {
            return false;
        }

        return static::loadByAccountId($res['info']['account']['id']);
    }


    public function getProfile($formatted = true)
    {
        if (!$this->profile) {
            $this->profile = $this->mlmsoftPlugin->api3->get('account/' . $this->getAccountField('id', 0) . '/profile');
        }
        if (!$this->profile) {
            return [];
        }
        if ($formatted) {
            return $this->formatProfile($this->profile);
        }
        return $this->profile;
    }

    public function getProfileField($fieldAlias, $default = false)
    {
        $profile = $this->getProfile();
        return $this->getFieldOrDefault($profile, $fieldAlias, $default);
    }

    public function getProfileFieldValue($fieldAlias, $default = '')
    {
        $profile = $this->getProfile();
        $field = $this->getFieldOrDefault($profile, $fieldAlias, $default);
        if (isset($field['value'], $field['value']['raw'])) {
            return $field['value']['raw'];
        }
        return $default;
    }

    public function getUserField($field, $default = false)
    {
        $users = $this->getFieldOrDefault($this->mlmsoftAccount, 'users', []);
        if (count($users) > 0) {
            return $this->getFieldOrDefault($users[0], $field, $default);
        }
        return $default;
    }

    public function getFullName()
    {
        return $this->getUserField('firstName') . ' ' . $this->getUserField('lastName');
    }

    public function getAccount()
    {
        return $this->mlmsoftAccount;
    }

    public function getAccountField($field, $default = false)
    {
        $account = $this->getAccount();
        return $this->getFieldOrDefault($account, $field, $default);
    }

    public function getPlanPositions()
    {
        if (!$this->planPositions) {
            $accountId = $this->getAccountField('id');
            $this->planPositions = $this->mlmsoftPlugin->api3->get("account/$accountId/position");
        }
        return $this->planPositions;
    }

    public function getPlanProperty($alias, $default = false)
    {
        $planProperties = $this->getPlanProperties();
        if (isset($planProperties[$alias])) {
            return $planProperties[$alias];
        }
        return $default;
    }

    public function getPlanPropertyValue($alias, $default = false)
    {
        $planProperties = $this->getPlanProperties();
        if (isset($planProperties[$alias])) {
            $planProperty = $planProperties[$alias];
            if (!empty($planProperty) && isset($planProperty['value'], $planProperty['value']['raw'])) {
                return $planProperty['value']['raw'];
            }
        }
        return $default;
    }

    public function getPlanProperties($formatted = true)
    {
        $this->getPlanPositions();
        if (!$this->planProperties) {
            $this->planProperties = [];
            foreach ($this->planPositions as $position) {
                $this->planProperties = array_merge($this->planProperties, $position['properties']);
            }
        }
        if ($formatted) {
            return $this->formatPropertyValues($this->planProperties);
        }
        return $this->planProperties;
    }

    public function setPassword($password)
    {
        $accountId = $this->getAccountField('id', 0);
        return $this->mlmsoftPlugin->api3->post("account/$accountId/set-password", [
            'password' => $password
        ]);
    }

    public function updateProfile($profile)
    {
        $accountId = $this->getAccountField('id', 0);
        $res = $this->mlmsoftPlugin->api3->put('account/' . $accountId . '/profile', [
            'profileFields' => $profile
        ]);
        return (bool)$res;
    }

    public function confirmField($field, $value)
    {
        $accountId = $this->getAccountField('id', 0);
        if (!$accountId) {
            return false;
        }
        $res = $this->mlmsoftPlugin->api3->put('account/' . $accountId . '/profile/' . $field, [
            'value' => $value,
            'forceConfirm' => true
        ]);
        return (bool)$res;
    }


    protected static function loadUserData($accountId)
    {
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        return $mlmsoftPlugin->api3->get("account/$accountId/info");
    }

    protected static function loadUserDataByToken($token)
    {
        $mlmsoftPlugin = MLMSoftPlugin::getInstance();
        return $mlmsoftPlugin->api3->get('account/current', [], $token);
    }

    protected function formatProfile($data)
    {
        $result = [];
        foreach ($data as $item) {
            $result[$item['alias']] = $item;
        }
        return $result;
    }

    protected function formatPropertyValues($properties)
    {
        $result = [];
        foreach ($properties as $property) {
            $result[$property['alias']] = $property;
        }
        return $result;
    }

    protected function getFieldOrDefault($array, $field, $default)
    {
        if (isset($array[$field])) {
            return $array[$field];
        }
        return $default;
    }
}
