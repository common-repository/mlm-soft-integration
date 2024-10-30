<?php


namespace MLMSoft\integrations\woocommerce\models\user;


use MLMSoft\core\models\user\MLMSoftLocalUser;
use WC_Customer;

class MLMSoftWCUser extends MLMSoftLocalUser
{
    /**
     * @param MLMSoftRemoteWCUser $remoteUser
     */
    public function syncProfile($remoteUser)
    {
        $customer = new WC_Customer($this->ID);

        $addressParts = explode(', ', $remoteUser->getProfileFieldValue('Mailing_address'));
        $customer->set_billing_address_1($addressParts[0]);
        $customer->set_billing_address_2($addressParts[1] ?? '');

        $postcode = $remoteUser->getProfileFieldValue('Postal/ZIP_code');
        $customer->set_billing_postcode($postcode);

        $country = $remoteUser->getProfileFieldValue('country_id');
        $customer->set_billing_country($country);

        $firstName = $remoteUser->getProfileFieldValue('firstname');
        $customer->set_billing_first_name($firstName);
        $customer->set_first_name($firstName);

        $lastName = $remoteUser->getProfileFieldValue('lastname');
        $customer->set_billing_last_name($lastName);
        $customer->set_last_name($lastName);

        $email = $remoteUser->getProfileFieldValue('email');
        $customer->set_billing_email($email);
        $customer->set_email($email);

        $phone = $remoteUser->getProfileFieldValue('phone');
        $customer->set_billing_phone($phone);

        $customer->save();
    }
}
