<?php

$aPluginConfig = array(
    'smarty3'   =>  [
        'version'       =>  '3.1.19'
        ,'installed'    =>  '09.11.2014'
        ,'path'         =>  PLUGINS_SMARTY3_DIR
        ,'require'      =>  PLUGINS_SMARTY3_DIR . 'Smarty.class.php'
        ,'initialize'   =>  'Smarty'
        ,'config'       =>  [
            'force_compile' => true
            ,'debugging' => false
            ,'caching' => true
            ,'cache_lifetime' => 120
        ]
    ]
);
