<?php

namespace MLMSoft\admin\models;

class MLMSoftAdminPanelResponse
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
     * @param $data object | array
     * @return MLMSoftAdminPanelResponse
     */
    public static function getSuccess($data)
    {
        $res = new MLMSoftAdminPanelResponse();
        $res->success = true;
        $res->error = null;
        $res->data = $data;
        return $res;
    }

    public static function getError($description)
    {
        $res = new MLMSoftAdminPanelResponse();
        $res->success = false;
        $res->error = $description;
        $res->data = null;
        return $res;
    }
}