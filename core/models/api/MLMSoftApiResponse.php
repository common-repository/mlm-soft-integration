<?php

namespace MLMSoft\core\models\api;

class MLMSoftApiResponse
{
    /**
     * @var boolean
     */
    public $success;

    /**
     * @var object
     */
    public $data;

    /**
     * @var string
     */
    public $error;

    /**
     * @param object | array $data
     * @return MLMSoftApiResponse
     */
    public static function getSuccess($data)
    {
        $res = new MLMSoftApiResponse();
        $res->success = true;
        $res->error = null;
        $res->data = $data;
        return $res;
    }

    /**
     * @param string $description
     * @return MLMSoftApiResponse
     */
    public static function getError($description)
    {
        $res = new MLMSoftApiResponse();
        $res->success = false;
        $res->error = $description;
        $res->data = null;
        return $res;
    }

}