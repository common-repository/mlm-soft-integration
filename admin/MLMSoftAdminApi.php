<?php

namespace MLMSoft\admin;

use MLMSoft\core\base\WPOptionsBase;
use MLMSoft\core\MLMSoftPlugin;
use MLMSoft\traits\SignedAjaxApiTrait;
use MLMSoft\traits\SingletonTrait;

class MLMSoftAdminApi
{
    use SignedAjaxApiTrait;
    use SingletonTrait;

    public const ADMIN_API_ENDPOINT = MLMSoftPlugin::PLUGIN_PREFIX . 'admin_ajax';

    /** @var array */
    private static $editableOptions = [];

    /** @var WPOptionsBase[] */
    private static $optionClasses = [];

    public function __construct()
    {
        $this->addHandler('get-options', [$this, 'getOptions']);
        $this->addHandler('update-options', [$this, 'updateOptions']);
        $this->addHandler('get-admin-users', [$this, 'getAdminUsers']);

        $this->initAdmin(self::ADMIN_API_ENDPOINT, true);
    }

    /**
     * @param WPOptionsBase $class
     */
    public static function registerEditableOptions($class)
    {
        $options = $class->getEditableOptions();
        self::$optionClasses[] = $class;
        foreach ($options as $key => $option) {
            self::$editableOptions[$key] = $option;
        }
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return self::$editableOptions;
    }

    /**
     * @param $data object
     * @return null
     */
    public function updateOptions($data)
    {
        foreach (self::$optionClasses as $class) {
            $class->updateAllOptions($data);
        }
        return null;
    }

    /**
     * @return array
     */
    public function getAdminUsers()
    {
        return get_users([
            'roles' => ['administrator'],
            'fields' => ['ID', 'user_nicename']
        ]);
    }
}