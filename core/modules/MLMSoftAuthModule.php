<?php

namespace MLMSoft\core\modules;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftLocalUser;
use MLMSoft\core\models\user\MLMSoftRemoteUser;
use MLMSoft\core\lib\crypto\MLMSoftAesEncryption;
use WP_Error;
use WP_User;

class MLMSoftAuthModule
{
    const MLMSOFT_AUTH_SUCCESS_FILTER = 'mlmsoft_auth_success';
    const MLMSOFT_BEFORE_USER_AUTH_FILTER = 'mlmsoft_before_user_auth';
    const MLMSOFT_AUTH_USER_LOGIN_FILTER = 'mlmsoft_auth_user_login';

    /**
     * @var MLMSoftPlugin
     */
    private $mlmsoftPlugin;

    /**
     * @since 3.4.24
     */
    private $remoteUserAuth = false;
	
    public function __construct()
    {
        $this->mlmsoftPlugin = MLMSoftPlugin::getInstance();
		
        /**
         * @since 3.4.24
         */
        $this->checkRemoteUserAuth();

        /**
         * @see wp-includes\pluggable.php
         */
        add_filter('authenticate', [$this, 'checkUserAuth'], 10, 3);

        /**
         * @see wp-includes\user.php
         */
        add_filter('password_reset', [$this, 'passwordReset'], 10, 2);
    }
	
    /**
     * Вход по ссылке из онлайн-офиса.
     *
     * @since 3.4.24
     */
    public function checkRemoteUserAuth()
    {
        if ( ! defined('MLMSOFT_REMOTE_AUTH_AES_PHRASE') ) {
            /**
             * @see wp-config.php
             */
            return;
        }

        if ( isset($_GET['incorrect_credentials']) ) {
            wc_add_notice( 
                'Login or password is incorrect',					 
                'notice'
            );
        }

        if( isset($_GET['username']) && isset($_GET['token']) ) {
            
            $username = sanitize_email($_GET['username']);
            $this->remoteUserAuth = true;
            
            $password = MLMSoftAesEncryption::decryptRemoteAuthToken($_GET['token'], MLMSOFT_REMOTE_AUTH_AES_PHRASE);
            
            if (!$password) {
                wp_redirect( home_url('my-account/?incorrect_credentials') );
                exit;
            }
            
            $this->checkUserAuth(null, $username, $password);
        }
    }
	
    public function checkUserAuth($user, $username, $password)
    {
        $syncUsers = $this->mlmsoftPlugin->options->syncUsers;
        if (!$syncUsers) {
            return $user;
        }
        $allowedUsers = $this->mlmsoftPlugin->options->allowedUsers;

        $internalUser = !empty($allowedUsers) && in_array($username, $allowedUsers) || isset($_REQUEST["standard_login"]) || isset($_COOKIE["standard_login"]);
        if (!$internalUser) {
            $wpUser = wp_authenticate_username_password($user, $username, $password);
            if (is_wp_error($wpUser)) {
                $wpUser = wp_authenticate_email_password($user, $username, $password);
            }
            if (!is_wp_error($wpUser)) {
                $mlmsoftUser = new MLMSoftLocalUser($wpUser);
                $internalUser = $mlmsoftUser->isInternalUser();
            }
        }
        if ($internalUser) {
            return $user;
        } else {
            remove_filter('authenticate', 'wp_authenticate_username_password', 20);
            remove_filter('authenticate', 'wp_authenticate_email_password', 20);
            remove_filter('authenticate', 'wp_authenticate_spam_check', 99);
        }

        if ($user instanceof WP_User) {
            return $user;
        }

        if (is_wp_error($user)) {
            return $user;
        }

        if (empty($username) || empty($password)) {
            $error = new WP_Error();
            if (empty($username)) {
                /**
                 * @since 3.6.5
                 */
                $error->add('empty_username', __('<strong>Error:</strong> The username field is empty.'));
            }
            if (empty($password)) {
                /**
                 * @since 3.6.5
                 */
                $error->add('empty_password', __('<strong>Error:</strong> The password field is empty.'));
            }
            return $error;
        }

        $action = isset($_GET['action']) ? $_GET['action'] : '';

        if (!is_user_logged_in() && $action !== 'logout') {

            $beforeAuth = apply_filters_ref_array(self::MLMSOFT_BEFORE_USER_AUTH_FILTER, [$user, $username, $password]);
            if ($beforeAuth instanceof WP_Error) {
                return $beforeAuth;
            }

            $userLogin = apply_filters_ref_array(self::MLMSOFT_AUTH_USER_LOGIN_FILTER, [$username, $password]);
            if ($userLogin instanceof WP_Error) {
                return $userLogin;
            }

            $sessionValues = [];
            if (array_key_exists('redirect_to', $_REQUEST)) {
                $sessionValues['redirect_to'] = esc_url_raw($_REQUEST['redirect_to']);
            }
            $sessionValues['rememberme'] = true;

            $this->mlmsoftPlugin->setSessionValues($sessionValues);

            $remoteUser = MLMSoftRemoteUser::loadByLoginAndPass($userLogin, $password);
            if (is_wp_error($remoteUser)) {
				/**
				 * Login or password is incorrect.
				 */
                return $remoteUser;
            }
            $localUser = MLMSoftLocalUser::loadFromRemote($remoteUser);
            if (!$localUser) {
                $localUser = MLMSoftLocalUser::create($remoteUser);
            }
            if (is_wp_error($localUser)) {
                return $localUser;
            }
            $localUser->setPassword($password);
            $localUser->syncWithRemote($remoteUser);

            $error = apply_filters_ref_array(self::MLMSOFT_AUTH_SUCCESS_FILTER, [$user, $localUser, $password]);
            if ($error instanceof WP_Error) {
                return $error;
            }
		
            /**
             * @since 3.4.24
             */
            if ( $this->isRemoteUserAuth() ) {
                if ( wp_check_password( $password, $localUser->data->user_pass, $localUser->ID) ) {

                    wp_set_current_user($localUser->ID, $localUser->user_login);
                    wp_set_auth_cookie($localUser->ID);
					
                    /**
                     * Редирект после логина на заданую в опции страницу.
                     *
                     * @since 3.5.2
                     */
                    $redirect_page = trim($this->mlmsoftPlugin->options->redirectAfterRemoteAuth);
                    $redirect_to = home_url();

                    if ( !empty($redirect_page) ) {
                        if ( 0 === strpos($redirect_page, $redirect_to) ) {
                            /**
                             * The home URL is already contained in $redirect_page.
                             */
                             $redirect_to = $redirect_page;
                        } else {
                            if ( 0 === strpos($redirect_page, 'http') ) {
                                /**
                                 * We consider the external URL to be incorrect.
                                 * Do nothing here.
                                 */
                            } else {
                                $redirect_to = home_url($redirect_page);
                            }
                        }
                    }
					
                    wp_redirect($redirect_to);
                    exit;
                }
            }
			
            return $localUser;
        }

        return new WP_Error();
    }

    public function passwordReset($user, $newPass)
    {
        $syncUsers = $this->mlmsoftPlugin->options->syncUsers;
        if (!$syncUsers) {
            return;
        }
        $remoteUser = MLMSoftRemoteUser::loadByWpUser($user);
        if ($remoteUser) {
            $remoteUser->setPassword($newPass);
        }
    }
	
    /**
     * @since 3.4.24
     *
     * @return boolean
     */
    public function isRemoteUserAuth()
    {
        return $this->remoteUserAuth;
    }
}