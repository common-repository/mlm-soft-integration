<?php

namespace MLMSoft\core;


use MLMSoft\core\base\WPOptionsBase;

/**
 * @since 3.5.5 Added property $domainSignature.
 */
 
/**
 * @property string $projectUrl [project_url]
 * @property string $onlineOfficeUrl [online_office]
 * @property string $api2token [api2_token]
 * @property string $api3Login [api3_login]
 * @property string $api3Password [api3_password]
 * @property string $_api3AccessToken [api3_access_token]
 * @property string $_api3RefreshToken [api3_refresh_token]
 * @property array $allowedUsers [allowed_users]
 * @property string $statusPropertyAlias (Status) [status_property_alias]
 * @property boolean $affiliateHeaderEnabled (false) [affiliate_header_enabled]
 * @property boolean $authorizedHeaderEnabled (false) [authorized_user_header_enabled]
 * @property boolean $syncUsers (true) [sync_users]
 * @property string $redirectAfterRemoteAuth [redirect_after_remote_auth]
 * @property string $domainSignature [domain_signature]
 */
class MLMSoftOptions extends WPOptionsBase
{

    protected function getOptionPrefix()
    {
        return MLMSoftPlugin::PLUGIN_PREFIX;
    }
}