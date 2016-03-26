<?php

/*
 * RC_TODO:
 * rewrite from scratch
 */

class auth extends CoreLib {
    
    private $oCookie = null;
    private $aUserCookie = null;
    private $oDb = null;
    private $bLogged = false;
    
    const USER_STATUS_INACTIVE = 0;
    const USER_STATUS_ACTIVE = 1;
    const USER_STATUS_SUSPENDED = 2;
    
    const USER_COOKIE_NAME = 'login';
    
    public function __construct() {
        $this->oCookie = $this->getLib('cookie');
        $this->oDb = $this->getLib('phpPdo');
    }
    
    private function setCookieData(){
        $sUserCookieData = $this->oCookie->getCookie(self::USER_COOKIE_NAME);
        $sUserSessionData = $this->getSessionParam(self::USER_COOKIE_NAME);
    
        if(empty($sUserCookieData)
           && !empty($sUserSessionData)){
           $sSerializedData = $sUserSessionData; 
        }
        elseif(empty($sUserCookieData)
                && empty($sUserSessionData)){
            return false;
        }
        elseif(!empty($sUserCookieData)
                && !empty($sUserSessionData)){
            $sSerializedData = $sUserCookieData; 
            $this->deleteSessionParam(self::USER_COOKIE_NAME);
        }
        elseif(!empty($sUserCookieData)
                && empty($sUserSessionData)){
            $sSerializedData = $sUserCookieData; 
        }
        
        $this->aUserCookie = json_decode($sSerializedData, true);
    }
    
    public function getIsLogged() {
        $this->setCookieData();
        
        if(!empty($this->aUserCookie)
           && $this->aUserCookie['time'] - time() <= 0){
            
            $this->setUserLoginStatus(true);
            return true;
        }
        
        if(!empty($this->aUserCookie)
           && $this->aUserCookie['lifetime'] <= time()){
            $this->logout();
        }
        
        return false;
    }

    public function getLoggedUserData(){
        $this->setCookieData();
        return $this->aUserCookie;
    }
    
    public function getAccessLevel() {
        if($this->getIsLogged()){
            return $this->aUserCookie['level'];
        }
        
        return 0;
    }
    
    private function setUserLoginStatus($bStatus){
        if($bStatus === false){
            $this->bLogged = false;
        }
        elseif($bStatus === true){
            $this->bLogged = true;
            $sUserData = json_encode($this->aUserCookie);
            
            $this->oCookie->setCookie(self::USER_COOKIE_NAME, $sUserData, [
                'time'  =>  $this->aUserCookie['lifetime']
            ]);
            
            $this->setSessionParam(self::USER_COOKIE_NAME, $sUserData);
        }
    }
    
    public function login($sLogin, $sPassword, $bRemember = false) {
        $aUserLoginData = $this->getUserLoginData($sLogin, $sPassword);
        
        if($bRemember){
            $aUserLoginData['remember'] = true;
        }
        
        if(!empty($aUserLoginData)){
            $this->setUserCookie($aUserLoginData);
            $this->setUserLoginStatus(true);
        }
        else{
            $this->setUserLoginStatus(false);
        }
        
        return $this->getIsLogged();
    }

    private function prepareLogin($sLogin){
        return $this->clearString(trim($sLogin));
    }
    
    private function preparePassword($sPassword){
        return md5(SALT.$this->clearString(trim($sPassword)));
    }
    
    private function getUserLoginData($sLogin, $sPassword){
        $sLogin = $this->prepareLogin($sLogin);
        $sPassword = $this->preparePassword($sPassword);
        
        $sSql = "SELECT "
                . "u.id, u.login, u.status, "
                . "up.level "
                . "FROM users u "
                . "LEFT JOIN user_privileges up ON (up.user_id = u.id)"
                . "WHERE (u.login = :login OR u.email = :login ) "
                . "AND u.password = :password ";
        return $this->oDb->execute($sSql, phpPdo::RESULT_ROW, [
            ':login'    =>  $sLogin
            ,':password'    =>  $sPassword
        ]);
    }
    
    private function setUserCookie($aData){
        $aData['time'] = time();
        
        $iStandardLifetime = cookie::TIME_MINUTE * 5;
        $iRememberLifetime = cookie::TIME_WEEK;

        if($aData['remember']){
          $iTime = $iRememberLifetime;  
        }
        else{
            $iTime = $iStandardLifetime;
        }
        
        $aData['lifetime'] = time() + $iTime;
        $this->aUserCookie = $aData;
    }
    
    public function logout() {
        print 'logout';
        $this->setUserCookie(null);
        $this->oCookie->deleteCookie(self::USER_COOKIE_NAME);
        $this->deleteSessionParam(self::USER_COOKIE_NAME);
    }


}

?>
