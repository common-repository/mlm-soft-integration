<?php

namespace MLMSoft\admin;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\traits\SignedFrontendLoadingTrait;
use MLMSoft\traits\SingletonTrait;

class MLMSoftAdminFrontend
{
    use SignedFrontendLoadingTrait;
    use SingletonTrait;

    public function __construct()
    {
        $this->initFrontend();
        $this->setModuleScripts();
        $this->addScriptParams('localeParams', [
            'locale' => get_locale()
        ]);
    }

    protected function getBaseDir()
    {
        return MLMSOFT_V3_PLUGIN_FILE;
    }

    protected function getPrefix()
    {
        return MLMSoftPlugin::PLUGIN_PREFIX . 'admin_';
    }

    protected function getPathToStyles()
    {
        return 'admin/frontend/dist/assets';
    }

    protected function getPathToEntries()
    {
        return 'admin/frontend/dist/assets/entry';
    }
}