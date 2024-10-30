<?php


namespace MLMSoft\integrations\woocommerce\models\user;


use MLMSoft\core\models\user\MLMSoftLocalUser;
use MLMSoft\core\models\user\MLMSoftRemoteUser;
use WC_Customer;

class MLMSoftRemoteWCUser extends MLMSoftRemoteUser
{
    public const CONFIRM_FIELDS_FILTER = 'mlmsoft_profile_edit_confirm_fields';

    public const PROFILE_FIELD_FIRSTNAME = 'firstname';
    public const PROFILE_FIELD_LASTNAME = 'lastname';
    public const PROFILE_FIELD_EMAIL = 'email';
    public const PROFILE_FIELD_PHONE = 'phone';

    public const PROFILE_FIELD_MATCH = [
        'account_first_name' => self::PROFILE_FIELD_FIRSTNAME,
        'account_last_name' => self::PROFILE_FIELD_LASTNAME,
        'account_email' => self::PROFILE_FIELD_EMAIL,
        'phone' => self::PROFILE_FIELD_PHONE,

        'first_name' => self::PROFILE_FIELD_FIRSTNAME,
        'last_name' => self::PROFILE_FIELD_LASTNAME,
        'billing_email' => self::PROFILE_FIELD_EMAIL,
        'billing_phone' => self::PROFILE_FIELD_PHONE,
    ];

    /**
     * @param MLMSoftLocalUser $localUser
     * @param array $request
     * @return bool
     * @throws \Exception
     */
    public function updateWCProfile($localUser, $request)
    {
        $customer = new WC_Customer($localUser->ID);
        $billingAddress1 = $customer->get_billing_address_1(null);
        $billingAddress2 = $customer->get_billing_address_2(null);
        $billingAddress = $billingAddress1 . ', ' . $billingAddress2;

        $dataToUpdate = [
            'Mailing_address' => $billingAddress,
            'Postal/ZIP_code' => $customer->get_billing_postcode(null),
        ];
        $billingCountry = $customer->get_billing_country(null);
        if ($billingCountry) {
            $dataToUpdate['country_id'] = $billingCountry;
        }
        foreach (self::PROFILE_FIELD_MATCH as $wcProfileField => $mlmsoftProfileField) {
            if (isset($request[$wcProfileField])) {
                $dataToUpdate[$mlmsoftProfileField] = sanitize_text_field(stripslashes($request[$wcProfileField]));
            }
        }

        $updateResult = $this->updateProfile($dataToUpdate);

        if ($updateResult) {
            $confirmFields = apply_filters_ref_array(self::CONFIRM_FIELDS_FILTER, [[]]);
            foreach ($confirmFields as $field) {
                if ($dataToUpdate[$field]) {
                    $this->confirmField($field, $dataToUpdate[$field]);
                }
            }
        }

        return $updateResult;
    }
}
