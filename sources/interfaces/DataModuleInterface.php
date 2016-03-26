<?php

interface DataModuleInterface {
    public function onInit();
    public function getData();
    public function setData();
}
