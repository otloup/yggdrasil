<?php

class table extends basicEntity {

    private $iId = null;
    private $sLogin = null;
    private $sEmail = null;
    private $sPassword = null;
    private $iStatus = null;
    private $iRegisterDate = null;
    private $sName = null;
    private $sSurname = null;
    private $iPhone = null;
    private $iLevel = null;
    
    const USER_PRIVILEGE_ALL = 0;
    const USER_PRIVILEGE_LOGGED = 1;
    const USER_PRIVILEGE_ADMIN = 3;
    
    public function __construct() {}
    
    public function update(){}
    
    public function save(){}
    
    /*
     * Getters
     */

    public function getId(){
        return $this->iId;
    }
    
    public function getLogin(){
        return $this->sLogin;
    }
    
    public function getPassword(){
        return $this->sPassword;
    }
    
    public function getEmail(){
        return $this->sEmail;
    }
    
    public function getStatus(){
        return $this->iStatus;
    }
    
    public function getRegisterDate(){
        return $this->iRegisterDate;
    }
    
    public function getName(){
        return $this->sName;
    }
    
    public function getSurname(){
        return $this->sSurname;
    }
    
    public function getPhone(){
        return $this->iPhone;
    }
    
    public function getLevel(){
        return $this->iLevel;
    }
    
    /*
     * Setters
     */
    
    public function setId($iId){
        $this->iId = $iId;
    }
    
    public function setLogin($sLogin){
        $this->sLogin = $sLogin;
    }
    
    public function setEmail($sEmail){
        $this->sEmail = $sEmail;
    }
    
    public function setStatus($iStatus){
        $this->iStatus = $iStatus;
    }
    
    public function setRegisterDate($iRegisterDate){
        $this->iRegisterDate = $iRegisterDate;
    }
    
    public function setName($sName){
        $this->sName = $sName;
    }
    
    public function setSurname($sSurname){
        $this->sSurname = $sSurname;
    }
    
    public function setPhone($iPhone){
        $this->iPhone = $iPhone;
    }
    
    public function setLevel($iLevel){
        $this->iLevel = $iLevel;
    }
    
}

?>
