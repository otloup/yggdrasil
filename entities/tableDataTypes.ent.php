<?php

class tableDataTypes extends basicEntity {

    private $iId = null;
    private $sName = null;
    private $sProperties = null;
    
    public function __construct() {}
    
    public function update(){}
    
    public function save(){}
    
    /*
     * Getters
     */

    public function getId(){
        return $this->iId;
    }
    
    public function getName(){
        return $this->sName;
    }
    
    public function getProperties(){
        return json_decode($this->sProperties, true);
    }
    
    /*
     * Setters
     */
    
    public function setId($iId){
        $this->iId = $iId;
    }
    
    public function setName($sName){
        $this->sName = $sName;
    }
    
    public function setProperties($aProperties){
        if(is_array($aProperties)){
            $this->sProperties = json_encode($aProperties, JSON_FORCE_OBJECT);
        }
        /*
         * RC_TODO:
         * should throw some kind of exception
         */
    }
    
    
}

?>
