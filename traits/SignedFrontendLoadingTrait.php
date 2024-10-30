<?php

namespace MLMSoft\traits;

use MLMSoft\lib\helpers\SignHelper;

trait SignedFrontendLoadingTrait
{
    use FrontendLoadingTrait;

    public function addSignKey()
    {
        $key = SignHelper::createSignKey();
        $this->addScriptParams('$s', [
            '_k' => $key
        ]);
    }

    public function enqueue()
    {
        $this->addSignKey();
        $this->registerAssets();
        $this->processEnqueue();
    }
}