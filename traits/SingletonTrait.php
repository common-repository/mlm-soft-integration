<?php


namespace MLMSoft\traits;


trait SingletonTrait
{
    /**
     * @var array список объектов
     */
    private static $instances = [];

    /**
     * @return self
     */
    public static function getInstance()
    {
        if (!isset(self::$instances[static::class])) {
            self::$instances[static::class] = new static;
        }

        return self::$instances[static::class];
    }
}