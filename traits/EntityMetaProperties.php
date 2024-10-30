<?php

namespace MLMSoft\traits;

use MLMSoft\lib\helpers\PhpDocHelper;
use ReflectionClass;

trait EntityMetaProperties
{
    protected $metaFieldInfo = [];

    protected $dataValues = [];

    protected $isInitialized = false;

    public function __get($name)
    {
        $this->initMetaFields();
        if (isset($this->metaFieldInfo[$name])) {
            $metaKey = $this->metaFieldInfo[$name];
            if (isset($this->dataValues[$name])) {
                return $this->dataValues[$name];
            }
            $value = $this->get_meta($metaKey, true);
            $this->dataValues[$name] = $value;
            return $value;
        }
        return null;
    }

    public function __set($name, $value)
    {
        $this->initMetaFields();
        if (isset($this->metaFieldInfo[$name])) {
            $metaKey = $this->metaFieldInfo[$name];
            $this->update_meta_data($metaKey, $value);
            $this->dataValues[$name] = $value;
        }
    }

    public abstract function update_meta_data($key, $value);

    public abstract function get_meta($key, $single);

    protected abstract function getMetaPrefix();

    private function initMetaFields()
    {
        if (!$this->isInitialized) {
            $this->prepareProperties();
        }
    }

    private function prepareProperties()
    {
        $properties = PhpDocHelper::getClassProperties(new ReflectionClass(static::class));
        foreach ($properties as $name => $data) {
            $propertyName = ltrim($name, '$');
            $metaName = '';
            $description = $data['description'];
            if (preg_match('#^\[(\w+)\]+(.*)#s', $description, $optionNameMatches)) {
                $metaName = $this->getMetaPrefix() . $optionNameMatches[1];
            }
            if (empty($metaName)) {
                $metaName = $this->createMetaNameFromProperty($propertyName);
            }
            $this->metaFieldInfo[$propertyName] = $metaName;
        }
    }

    private function createMetaNameFromProperty($property)
    {
        $rawOption = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $property));
        return $this->getMetaPrefix() . $rawOption;
    }
}