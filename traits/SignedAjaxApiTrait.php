<?php

namespace MLMSoft\traits;

use MLMSoft\lib\helpers\SignHelper;

trait SignedAjaxApiTrait
{
    use AjaxApiTrait;

    protected function validate($body)
    {
        return SignHelper::validate($body);
    }
}