<?php

namespace MLMSoft\core\api\api2\models;

class Api2Response
{
    /**
     * @var integer
     */
    public $errorCode;

    /**
     * @var string
     */
    public $errorMessage;

    /**
     * @var array
     */
    public $errorExtended;

    /**
     * @var object
     */
    public $primary;

    /**
     * @var array
     */
    public $secondary;

    /**
     * @var string
     */
    public $locale;

    /**
     * MLMSoftApi2Response constructor.
     * @param $data object | array
     */
    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function getErrorCode()
    {
        return isset($this->errorCode) ? $this->errorCode : -1;
    }

    public function isPrimarySuccess()
    {
        return $this->getErrorCode() == 0 && $this->getPrimarySuccess();
    }

    public function getPrimarySuccess()
    {
        return isset($this->primary->success) ? $this->primary->success : false;
    }

    public function getPrimaryPayload()
    {
        return !empty($this->primary->payload) ? $this->primary->payload : null;
    }

    public function getErrorMessage()
    {
        return isset($this->errorMessage) ? $this->errorMessage : '';
    }

    public function getPrimaryMessage()
    {
        return isset($this->primary->message) ? $this->primary->message : '';
    }

    public function getStdErrorLogParams()
    {
        return array(
            '%ercode' => $this->getErrorCode(),
            '%ermsg' => $this->getErrorMessage(),
            '%success' => $this->getPrimarySuccess() ? 'Y' : 'N',
            '%msg' => $this->getPrimaryMessage()
        );
    }
}