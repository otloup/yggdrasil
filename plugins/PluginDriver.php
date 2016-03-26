<?php

class PluginDriver extends CoreLib {
    
    public $oPlugin = null;
    private $aPluginInfo = [];
    private $aPluginConfig = [];
    private $sPluginVersion = "";
    private $sPluginInstalationDate = "";
    
    public function __construct($sPluginName = null) {
        require_once(CONF_DIR . 'plugin_config.php');
        
        /*
         * RC_TODO:
         * integrate memcache
         * add plugin configuration to memcache if $_GLOBAL is too laggy
         */
        if(empty($GLOBALS['plugin_config'])){
            $GLOBALS['plugin_config'] = $aPluginConfig;
        }
        if(empty($this->aPluginConfig)){
            $this->aPluginConfig = $GLOBALS['plugin_config'];
        }
        
        /**
         * RC_TODO:
         * SHOULD THROW AN EXCEPTION!!!
         */
        
        if(
                $sPluginName != null
                && isset($this->aPluginConfig[$sPluginName])
                && empty($this->oPlugin)
            ){
            $this->aPluginConfig = $this->aPluginConfig[$sPluginName];
            $this->sPluginVersion = $this->aPluginConfig['version'];
            $this->sPluginInstalationDate = $this->aPluginConfig['installed'];
            
            $this->initPlugin();
        }
        
    }
    
    private function initPlugin(){
        if(file_exists($this->aPluginConfig['require'])){
            require_once($this->aPluginConfig['require']);
            
            $this->oPlugin = new $this->aPluginConfig['initialize'];
            
            if(!empty($this->aPluginConfig['config'])){
                foreach ($this->aPluginConfig['config'] as $sValueName => $mValue) {
                    $this->oPlugin->$sValueName = $mValue;
                }
            }
        }
    }
    
    public function fetchPlugin(){
        return $this->oPlugin;
    }
    
    public function getPuginVersion(){
        return $this->sPluginVersion;
    }
    
    public function getPluginInstalationDate(){
        return $this->sPluginInstalationDate;
    }
}
