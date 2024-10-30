<?php

namespace MLMSoft\integrations\pos;

use MLMSoft\core\base\WPOptionsBase;
use MLMSoft\core\MLMSoftPlugin;

/**
 * @property boolean $enabled [enabled]
 * @property array $posWcStatusMatch [pos_wc_order_status_match]
 */
class PosIntegrationOptions extends WPOptionsBase
{
    public const OPTIONS_PREFIX = MLMSoftPlugin::PLUGIN_PREFIX . 'pos_integration_';

    protected function getOptionPrefix()
    {
        return self::OPTIONS_PREFIX;
    }
}