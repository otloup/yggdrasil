<?php

class EntityDriver extends CoreLib {
    
    private $oEntity = null;
    private $sEntityName = '';
    private $sEntityCaller = '';
    
    public function __construct($sEntityName = null, $sEntityCaller = null) {
        
        /**
         * RC_TODO:
         * SHOULD THROW AN EXCEPTION!!!
         */
        
        if($sEntityName != null){
            $this->sEntityName = $sEntityName;
            $this->initEntity();
        }
        
        if($sEntityCaller != null
           && !empty($this->oEntity)){
            $this->oEntity->setBaseCaller($sEntityCaller);
        }
        
    }
    
    private function initEntity(){
        $sEntityPath = ENT_DIR . $this->sEntityName . FILE_ENTITY_POSTFIX;
        if(file_exists($sEntityPath)){
            require_once($sEntityPath);
            
            $this->oEntity = new $this->sEntityName;
        }
    }
    
    public function fetchEntity(){
        return $this->oEntity;
    }
    
    public function getAll(){
        /*
         * entity name == table name
         */
    }
}
