<?php

interface BasicFormModule {
    public function onFailure();
    public function onValidate($aPost, $aGet);
    public function onSuccess($aPost, $aGet);
}

