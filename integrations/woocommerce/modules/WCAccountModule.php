<?php


namespace MLMSoft\integrations\woocommerce\modules;


use Exception;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftLocalUser;
use MLMSoft\core\models\user\MLMSoftReferral;
use MLMSoft\core\models\user\MLMSoftRemoteUser;
use MLMSoft\integrations\woocommerce\models\user\MLMSoftRemoteWCUser;
use MLMSoft\integrations\woocommerce\WCIntegration;
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;
use MLMSoft\integrations\woocommerce\modules\WCCheckoutModule; // @since 3.6.2
use WP_Error;

class WCAccountModule
{
    public const MLMSOFT_USER_PROFILE_FILTER = 'mlmsoft_user_profile';
    public const MLMSOFT_USER_REGISTER_USERNAME_FILTER = 'mlmsoft_user_register_username';

    /**
     * @var MLMSoftPlugin
     */
    private $mlmsoftPlugin;

    /**
     * @var WCIntegrationOptions
     */
    private $options;

    /**
     * WCAccountModule constructor.
     * @param MLMSoftPlugin $mlmsoftPlugin
     * @param WCIntegrationOptions $options
     */
    public function __construct($mlmsoftPlugin, $options)
    {
        $this->mlmsoftPlugin = $mlmsoftPlugin;
        $this->options = $options;

        add_filter('woocommerce_registration_errors', array($this, 'checkRegistrationPossibility'), 10, 3);
        add_filter('woocommerce_new_customer_data', array($this, 'newCustomerData'), 100, 2);
        add_action('woocommerce_created_customer', array($this, 'createdCustomer'), 10, 3);
        add_action('woocommerce_save_account_details', array($this, 'saveAccountDetails'), 10, 1);
        add_action('edit_user_profile_update', array($this, 'saveAccountDetails'), 10, 1);

        /**
         * @since 3.6.2
         */
        add_action('woocommerce_register_form', array($this, 'addSponsorField'), 1);        
    }
    
    /**
     * Add a sponsor field to my-account page.
     *
     * @since 3.6.2
     */
    public function addSponsorField()
    {
		
        $sponsorFieldStatus = $this->options->registrationSponsorFieldStatus;

        if ( empty($sponsorFieldStatus) || 'disabled' === $sponsorFieldStatus ) {
            return;
        }
		
        $value = !empty($_POST[WCIntegration::SPONSOR_ID_FIELD_NAME]) ? $_POST[WCIntegration::SPONSOR_ID_FIELD_NAME] : '';
        if (!$value && !empty($_COOKIE[MLMSoftPlugin::REFERRAL_COOKIE_NAME])) {
            $inviteCode = $_COOKIE[MLMSoftPlugin::REFERRAL_COOKIE_NAME];
            $referral = MLMSoftReferral::loadByInviteCode($inviteCode);
            if ($referral) {
                $value = $referral->getAccountField('id', null);
            }
        }
		
        $field = WCCheckoutModule::getSponsorIdField();
		
        woocommerce_form_field(WCIntegration::SPONSOR_ID_FIELD_NAME, $field, $value);		
    }

    /**
     * @param WP_Error $errors
     * @param string $username
     * @param string $email
     * @return mixed
     */
    public function checkRegistrationPossibility($errors, $username, $email)
    {
        $syncUsers = $this->mlmsoftPlugin->options->syncUsers;
        if (!$syncUsers) {
            return $errors;
        }
        $accountExists = MLMSoftRemoteUser::checkExists($email);

        if ($accountExists) {
            $errors->add('error!', 'User with this email already exists.', 'error');
            return $errors;
        }

        $referral = $this->getReferralFromRequest();
        if (is_wp_error($referral)) {
            return $referral;
        }

        if (!$referral) {
            return $errors;
        }

        $_REQUEST[WCIntegration::SPONSOR_REF_CODE_FIELD_DATA_NAME] = $referral->getAccount();

        return $errors;
    }

    public function newCustomerData($newUser)
    {
        $syncUsers = $this->mlmsoftPlugin->options->syncUsers;
        if (!$syncUsers) {
            return $newUser;
        }

        $referral = $this->getReferralFromRequest();

        $login = wc_strtolower($newUser['user_email']);
        $password = $newUser['user_pass'];
        $sponsorId = $referral ? (int)$referral->getAccountField('id', 0) : 0;

        $profile = [
            'firstname' => sanitize_text_field(stripslashes($_REQUEST['billing_first_name'])),
            'lastname' => sanitize_text_field(stripslashes($_REQUEST['billing_last_name'])),
            'email' => wc_strtolower($newUser['user_email']),
            'phone' => sanitize_text_field(stripslashes($_REQUEST['billing_phone'])),
        ];

        $profile = apply_filters_ref_array(self::MLMSOFT_USER_PROFILE_FILTER, [$profile, $sponsorId]);

        $remoteUser = MLMSoftRemoteUser::createNew($login, $password, $sponsorId, $profile);

        if (!$remoteUser) {
            return new WP_Error('', MLMSoftPlugin::translate('Internal error. Can not create user'));
        }

        $userLogin = apply_filters_ref_array(self::MLMSOFT_USER_REGISTER_USERNAME_FILTER, [$profile['email'], $remoteUser]);
        if ($userLogin instanceof WP_Error) {
            return $userLogin;
        }
        // Saving User Profile in Wordpress
        $newUser['user_login'] = $userLogin;
        $newUser['nickname'] = $profile['firstname'] . ' ' . $profile['lastname'];
        $newUser['first_name'] = $profile['firstname'];
        $newUser['last_name'] = $profile['lastname'];
        $newUser['user_email'] = $profile['email'];

        return $newUser;
    }

    public function createdCustomer($user_id, $newUser, $password_generated)
    {
        $syncUsers = $this->mlmsoftPlugin->options->syncUsers;
        if (!$syncUsers) {
            return;
        }

        $login = wc_strtolower($newUser['user_email']);
        $password = $newUser['user_pass'];

        $remoteUser = MLMSoftRemoteUser::loadByLoginAndPass($login, $password);
        $localUser = MLMSoftLocalUser::loadFromRemote($remoteUser);
        $localUser->syncWithRemote($remoteUser);
    }

    public function saveAccountDetails($wpUserId)
    {
        $syncUsers = $this->mlmsoftPlugin->options->syncUsers;
        if (!$syncUsers) {
            return;
        }

        $localUser = new MLMSoftLocalUser($wpUserId);
        $remoteUser = MLMSoftRemoteWCUser::loadByWpUser($localUser);
        if (!$remoteUser) {
            return;
        }
        try {
            $res = $remoteUser->updateWCProfile($localUser, $_REQUEST);
        } catch (Exception $e) {
            $res = false;
        }
        if (!$res) {
            wc_add_notice('Error updating user profile', 'error');
            return;
        }

        if (!empty($_REQUEST['password_1'])) {
            $res = $remoteUser->setPassword($_REQUEST['password_1']);
            if (!$res) {
                wc_add_notice('Error updating user password', 'error');
            }
        }
    }

    /**
     * @return MLMSoftReferral|WP_Error|null
     */
    private function getReferralFromRequest()
    {
        $checkSponsorField = $this->options->registrationSponsorField;

        switch ($checkSponsorField) {
            case 'id':
                if (!isset($_REQUEST[WCIntegration::SPONSOR_ID_FIELD_NAME])) {
                    break;
                }
                $sponsorId = sanitize_text_field(stripslashes($_REQUEST[WCIntegration::SPONSOR_ID_FIELD_NAME]));
                if ($sponsorId) {
                    $referral = MLMSoftReferral::loadByAccountId($sponsorId);
                    if (!$referral) {
                        return new WP_Error('error', MLMSoftPlugin::translate('Sponsor with this id doesn\'t exists.'));
                    }
                } else {
                    $referral = MLMSoftReferral::loadFromSession();
                }
                return $referral;
            case 'ref_code':
                if (!isset($_REQUEST[WCIntegration::SPONSOR_REF_CODE_FIELD_NAME])) {
                    break;
                }
                $value = sanitize_text_field(stripslashes($_REQUEST[WCIntegration::SPONSOR_REF_CODE_FIELD_NAME]));
                if ($value) {
                    $referral = MLMSoftReferral::loadByInviteCode($value);
                    if (!$referral) {
                        return new WP_Error('error', MLMSoftPlugin::translate('Cannot find any sponsor with this sponsor referral code in the database.'));
                    }
                } else {
                    $referral = MLMSoftReferral::loadFromSession();
                }
                return $referral;
        }

        return null;
    }
}
