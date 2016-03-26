<?php

interface BasicPage {
    
    public function constructPage();
    
    public function onInit();
    public function beforeRender();
    public function afterRender();
    public function onSubmit($aPost, $aGet);
    public function onXHR();
    
//    public function setData();
    public function renderPage();
    public function onEnd();
//    public function printData();
//    public function injectBasicModule();
//    public function injectFormModule();
//    public function injectDataModule();
//    public function printModule();
//    public function getModule();
    
}