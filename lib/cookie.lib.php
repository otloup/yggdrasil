<?php

class cookie extends CoreLib {
    
    private $aCookies = [];
    
    const TIME_SECOND = 1;
    const TIME_MINUTE = 60; //TIME_SECOND * 60
    const TIME_HOUR = 360; //TIME_MINUTE * 60
    const TIME_DAY = 8640; //TIME_HOUR * 24
    const TIME_WEEK = 60480; //TIME_DAY * 7
    const TIME_MONTH = 241920; //TIME_WEEK * 4
    const TIME_YEAR = 2903040; //TIME_MONTH * 12
    
    const DEFAULT_LOCATION = '/';
    
    public function __construct() {
        $this->aCookies = $this->getCookies();
        
    }
    
    public function getCookie($sCookieName){
        if(!empty($this->aCookies[$sCookieName])){
            return $this->aCookies[$sCookieName];
        }
        return [];
    }
    
    public function setCookie($sName, $sValue, $aParams = []){
        if(empty($aParams['time']) 
                || $aParams['time'] < time()){
            $iTime = time() + self::TIME_DAY;
        }
        else{
            $iTime = $aParams['time'];
        }
        
        $sLocation = empty($aParams['location']) ? self::DEFAULT_LOCATION : $aParams['location'];
        return setcookie($sName, $sValue, $iTime, $sLocation);
    }
    
    public function deleteCookie($sName){
        return $this->setCookie($sName, null, [
            'time'  =>  time() - self::TIME_YEAR
        ]);
    }
    
    private function getTime($sTime){
        switch ($sTime) {
            case 'second':
                return self::TIME_SECOND;

            case 'minute':
                return self::TIME_MINUTE;

            case 'hour':
                return self::TIME_HOUR;

            case 'day':
                return self::TIME_DAY;

            case 'week':
                return self::TIME_WEEK;

            case 'month':
                return self::TIME_MONTH;

            case 'year':
                return self::TIME_YEAR;

        }
    }
    
}