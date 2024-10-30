<?php

namespace MLMSoft\integrations\woocommerce;

use MLMSoft\core\base\WPOptionsBase;
use MLMSoft\core\MLMSoftPlugin;

/**
 * @since 3.6.6 Added properties: `use_checkout_css`, `checkout_css`.
 */

/**
 * @property boolean $enabled [enabled]
 * @property string $sendVolumesOrderStatus (wc-completed) [send_volumes_order_status]
 * @property string $registrationSponsorField [registration_sponsor_field]
 * @property string $registrationSponsorFieldStatus [registration_sponsor_field_status]
 * @property string $registrationSponsorFieldAttribute [registration_sponsor_field_attribute]
 * @property boolean $useCheckoutCss (false) [use_checkout_css]
 * @property string $checkoutCss [checkout_css]
 * @property array $volumeProductFields [volume_product_fields]
 * @property boolean $useDocuments (false) [use_documents]
 * @property string $createOrderDocument [create_order_doc]
 * @property string $updateOrderDocument [update_order_doc]
 * @property string $volumeChangeDocument (VolumeChange) [volume_change_doc]
 * @property integer $walletOperationIdToPayWithBonuses [wallet_operation_id_to_pay_bonuses]
 * @property integer $walletOperationIdToCancelPayWithBonuses [wallet_operation_id_to_cancel_pay_bonuses]
 * @property float $maxPercentToPayBonuses (100) [max_percent_to_pay_bonuses]
 * @property array $currencyWalletMatch [currency_wallet_match]
 */
class WCIntegrationOptions extends WPOptionsBase
{
    public const OPTIONS_PREFIX = MLMSoftPlugin::PLUGIN_PREFIX . 'wc_integration_';

    protected function getOptionPrefix()
    {
        return self::OPTIONS_PREFIX;
    }
}