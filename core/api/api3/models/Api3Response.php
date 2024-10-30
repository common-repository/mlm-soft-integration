<?php

namespace MLMSoft\core\api\api3\models;

class Api3Response
{
    /**
     * @var boolean
     */
    public $success;

    /**
     * @var array | object
     */
    public $payload;

    /**
     * @var array | object
     */
    public $error;

    /**
     * MLMSoftApi3Response constructor.
     * @param $data array | object
     */
    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}