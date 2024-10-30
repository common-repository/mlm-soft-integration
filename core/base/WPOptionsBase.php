<?php

namespace MLMSoft\core\base;

use MLMSoft\admin\MLMSoftAdminApi;
use MLMSoft\lib\helpers\PhpDocHelper;
use MLMSoft\traits\SingletonTrait;
use ReflectionClass;

abstract class WPOptionsBase
{
    use SingletonTrait;

    private static $editableOptions = [];

    private static $isMultisite = false;

    private $optionsInfo = [];

    public function __construct()
    {
        self::$isMultisite = function_exists('get_network');

        $this->initOptions();

        MLMSoftAdminApi::registerEditableOptions($this);
    }

    protected abstract function getOptionPrefix();

    private function initOptions()
    {
        $properties = PhpDocHelper::getClassProperties(new ReflectionClass(static::class));
        foreach ($properties as $name => $data) {
            $propertyName = ltrim($name, '$');
            $propertyInfo = [
                'editable' => !str_starts_with($propertyName, '_')
            ];
            $optionName = '';
            $defaultValue = '';
            $description = $data['description'];
            if (preg_match('#\[([A-z0-9 .:,\[\]]+)\]+(.*)#s', $description, $optionNameMatches)) {
                $optionName = $this->getOptionPrefix() . $optionNameMatches[1];
            }
            if (preg_match('#^\(([A-z0-9\[\]]+)\)+(.*)#s', $description, $defaultValueMatches)) {
                $defaultValue = $defaultValueMatches[1];
            }
            if (empty($optionName)) {
                $optionName = $this->createOptionNameFromProperty($propertyName);
            }
            $propertyInfo['name'] = $optionName;
            $propertyInfo['defaultValue'] = $this->getTypeValue($defaultValue);
            $this->optionsInfo[$propertyName] = $propertyInfo;
        }
    }

    public function getOptionValue($key, $default = null)
    {
        if (self::$isMultisite) {
            $mainNetworkId = get_main_network_id();
            return get_network_option($mainNetworkId, $key, $default);
        } else {
            $val = get_option($key);
        }
        if ($val !== false) {
            return $val;
        } else {
            return $default;
        }
    }

    public function updateOptionValue($key, $value)
    {
        if (self::$isMultisite) {
            $mainNetworkId = get_main_network_id();
            return update_network_option($mainNetworkId, $key, $value);
        }
        return update_option($key, $value);
    }

    public function getEditableOptions()
    {
        $res = [];
        foreach ($this->optionsInfo as $option) {
            if ($option['editable']) {
                $res[$option['name']] = $this->getOptionValue($option['name'], '');
            }
        }
        return $res;
    }

    public function updateAllOptions($data)
    {
        foreach ($this->optionsInfo as $option) {
            $optionName = $option['name'];
            if ($option['editable'] && isset($data[$optionName])) {
                $this->updateOptionValue($optionName, $data[$optionName]);
            }
        }
    }

    public function __get($name)
    {
        $optionName = $this->getOptionName($name);
        if (empty($optionName)) {
            return null;
        }

        return $this->getOptionValue($optionName, $this->getDefaultValue($name));
    }

    public function __set($name, $value)
    {
        $optionName = $this->getOptionName($name);
        if (!empty($optionName)) {
            $this->updateOptionValue($optionName, $value);
        }
    }

    protected function getOptionName($property)
    {
        if (isset($this->optionsInfo[$property])) {
            return $this->optionsInfo[$property]['name'];
        }
        return '';
    }

    protected function getDefaultValue($property)
    {
        if (isset($this->optionsInfo[$property])) {
            return $this->optionsInfo[$property]['defaultValue'];
        }
        return null;
    }

    protected function createOptionNameFromProperty($property)
    {
        $rawOption = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property));
        return $this->getOptionPrefix() . $rawOption;
    }

    private function getTypeValue($raw)
    {
        if ($raw == 'true' || $raw == 'false') {
            return $raw == 'true';
        }
        if (ctype_digit($raw)) {
            return intval($raw);
        }
        if (is_numeric($raw)) {
            return floatval($raw);
        }
        return $raw;
    }
}