<?php


namespace MLMSoft\integrations\woocommerce\modules;


use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\core\models\user\MLMSoftReferral;
use MLMSoft\integrations\woocommerce\WCIntegration;
use MLMSoft\integrations\woocommerce\WCIntegrationOptions;

class WCCheckoutModule
{
    public const CHECKOUT_FIELDS_FILTER = MLMSoftPlugin::PLUGIN_PREFIX . 'checkout_fields';

    public function __construct()
    {
        add_filter('woocommerce_checkout_get_value', array($this, 'checkoutGetValue'), 10, 2);
        add_filter('woocommerce_checkout_fields', array($this, 'addSponsorIdField'), 10, 2);
    }

    public function checkoutGetValue($null, $input)
    {
        if ($input == WCIntegration::SPONSOR_ID_FIELD_NAME) {
            $referral = MLMSoftReferral::loadFromSession();
			
            if ($referral) {
                return $referral->getAccountField('id', null);
            }
        }

        return null;
    }

    public function addSponsorIdField($fields)
    {
        $resultField = self::getSponsorIdField();

        if (empty($resultField)) {
            return $fields;
        }

        $fields['account'][$resultField['field_name']] = $resultField;

        return $fields;
    }

    public static function getSponsorIdField()
    {
        
        /**
         * Check `registration_sponsor_field_status` option.
         * @since 3.6.2
         */        
        $sponsorFieldStatus = WCIntegrationOptions::getInstance()->registrationSponsorFieldStatus;
        
        if ( empty($sponsorFieldStatus) || 'disabled' === $sponsorFieldStatus ) 
        {
            return null;
        }
        
        $checkSponsorField = WCIntegrationOptions::getInstance()->registrationSponsorField;
        
        $resultField = [];
        switch ($checkSponsorField) {
            case 'id':
                $resultField = [
                    'type' => 'number',
                    'label' => MLMSoftPlugin::translate('Sponsor ID'),
                    'required' => false,
                    'placeholder' => MLMSoftPlugin::translate('Sponsor ID'),
                    'field_name' => WCIntegration::SPONSOR_ID_FIELD_NAME
                ];
                break;
            case 'ref_code':
                $resultField = [
                    'type' => 'text',
                    'label' => MLMSoftPlugin::translate('Invite code'),
                    'required' => false,
                    'placeholder' => MLMSoftPlugin::translate('Invite code'),
                    'field_name' => WCIntegration::SPONSOR_REF_CODE_FIELD_NAME
                ];
                break;
        }
		
        /**
         * @since 3.6.2
         */
        if ( 'required' === $sponsorFieldStatus ) 
        {
            $resultField['required'] = true;
        }

        /**
         * @since 3.6.4
         */
        $sponsor_id = (new self)->checkoutGetValue(null, WCIntegration::SPONSOR_ID_FIELD_NAME);

        if ( ! is_null($sponsor_id) && (int) $sponsor_id > 0 ) 
        {
            
            /**
             * Check `registration_sponsor_field_attribute` option.
             *
             * Option has 2 values:
             *	- None: empty string (by default)
             *	- Readonly if cookie `referral` is set: readonly
             */        
            $sponsorFieldAttribute = WCIntegrationOptions::getInstance()->registrationSponsorFieldAttribute;

            if ( ! empty($sponsorFieldAttribute) )
            {
                $sponsorFieldAttribute = strtolower($sponsorFieldAttribute);
            }

            if ( in_array($sponsorFieldAttribute, ['readonly']) ) 
            {
                $resultField['custom_attributes'] = [$sponsorFieldAttribute=>$sponsorFieldAttribute];
            }
            
        }
		
        $resultField = apply_filters(self::CHECKOUT_FIELDS_FILTER, $resultField);

        if (empty($resultField)) {
            return null;
        }

        return $resultField;
    }
}