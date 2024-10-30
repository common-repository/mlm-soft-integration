<?php

namespace MLMSoft\components;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\traits\SignedFrontendLoadingTrait;

abstract class ComponentLoaderBase
{
    use SignedFrontendLoadingTrait;

    /** @var string */
    protected $type;

    public function __construct()
    {
        $this->initFrontend();
        $this->setModuleScripts();
        $this->addScriptParams('localeParams', [
            'locale' => get_locale()
        ]);
        $this->type = $this->getType();
    }

    /**
     * @return string
     */
    protected abstract function getType();

    /**
     * @param string $name
     * @param array $props
     */
    public function showComponent($name, $props = [])
    {
        echo $this->getComponentHtml($name, $props);
    }

    public function getComponentHtml($name, $props = [])
    {
        $this->addSignKey();
        $this->enqueue();
        $propsArray = [];
        foreach ($props as $key => $value) {
            $propsArray[] = $key . '="' . $value . '"';
        }
        $propsString = implode(' ', $propsArray);
        return "<$name $propsString></$name>";
    }

    protected function getPrefix()
    {
        return MLMSoftPlugin::PLUGIN_PREFIX . 'components_' . $this->type;
    }

    protected function getBaseDir()
    {
        return MLMSOFT_V3_PLUGIN_FILE;
    }

    protected function getPathToStyles()
    {
        return 'components/' . $this->type . '/frontend/assets';
    }

    protected function getPathToEntries()
    {
        return 'components/' . $this->type . '/frontend/assets/entry';
    }
}