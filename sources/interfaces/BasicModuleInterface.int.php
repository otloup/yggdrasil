<?php

interface BasicModule {
    public function constructModule($sTplName);
    
    public function onInit();
    public function getContents();
    public function printContents();
    public function fetchContents();
}
