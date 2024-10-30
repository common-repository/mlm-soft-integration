<?php

namespace MLMSoft\core\base;

use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\traits\SingletonTrait;

abstract class MLMSoftIntegrationBase
{
    use SingletonTrait;

    /** @var MLMSoftPlugin */
    protected $mlmsoftPlugin;

    public $dependencies = [];

    public $dependentIntegrations = [];

    /**
     * @return boolean
     */
    public abstract function isEnabled();

    /**
     * @param MLMSoftPlugin $mlmsoftPlugin
     */
    public function setPlugin($mlmsoftPlugin)
    {
        $this->mlmsoftPlugin = $mlmsoftPlugin;
    }

    /**
     * @return void
     */
    public abstract function init();
}