<?php

/*
 * RC_TODO:
 * separate session handeling to a separate lib
 */

class CoreLib {

    private static $oIncludedLibs;
    private static $oIncludedPlugins;
    private static $oIncludedEntities;
    private static $aErrors = [];
    private static $aMessages = [];
    private static $aPersistentMessages = [];
    private static $aMessagesIndex = [];
    private static $bStarted = false;
    private static $bEnded = false;

    const MESSAGE_TYPE_INFO = 'info';
    const MESSAGE_TYPE_WARNING = 'warning';
    const MESSAGE_TYPE_ERROR = 'error';
    const MESSAGE_TYPE_SUCCESS = 'success';
    
    const MESSAGE_PERSIST_FLASH = 'flash'; //delete instance of this message after displaying it
    const MESSAGE_PERSIST_CONSTANT = 'constant'; //delete instance of this message only after removing it manually
    const MESSAGE_PERSIST_RELOAD = 'reload'; //delete instance of this message after page reload
    
    const SESSION_PARAMS_NAME = 'params';
    
    public function start() {
        if(!self::$bStarted){
            self::$oIncludedLibs = new stdClass();
            self::$oIncludedPlugins = new stdClass();
            self::$oIncludedEntities = new stdClass();
            self::$bStarted = true;
        
            self::$aPersistentMessages = empty($_SESSION['persist_messages']) ?  [
                self::MESSAGE_PERSIST_CONSTANT => []
                ,self::MESSAGE_PERSIST_RELOAD => []
            ] : $_SESSION['persist_messages'];
            
            self::$aMessagesIndex = empty($_SESSION['messages_index']) ?  [] : $_SESSION['messages_index'];
            
            self::$aMessages = empty($_SESSION['messages']) ? [
                self::MESSAGE_TYPE_ERROR => []
                ,self::MESSAGE_TYPE_INFO => []
                ,self::MESSAGE_TYPE_WARNING => []
                ,self::MESSAGE_TYPE_SUCCESS => []
            ] : $_SESSION['messages'];
            
            $this->saveSessionData();
        }
    }

    
    /**
     * get all available libs
     */
    public function getLibs(){
        
    }
    
    public function getLib($sLibName, $sConstructorType = 'new') {
        $sFileName = LIB_DIR . $sLibName . FILE_LIB_POSTFIX;

        if (empty(self::$oIncludedLibs->$sLibName)) {
            if (file_exists($sFileName)) {
                require_once $sFileName;
            } else {
                print 'file ' . $sFileName . ' does not exists';
                exit;
            }
        }

        if ($sConstructorType == 'new') {
            self::$oIncludedLibs->$sLibName = new $sLibName();
            return self::$oIncludedLibs->$sLibName;
        } else {
            return $sLibName;
        }
    }

    /**
     * get list of all available plugins
     */
    public function getPlugins() {
        
    }
    
    public function getPlugin($sPluginName) {
        if(empty(self::$oIncludedPlugins->$sPluginName)){
          self::$oIncludedPlugins->$sPluginName = new PluginDriver($sPluginName);
        }
        
        return self::$oIncludedPlugins->$sPluginName->fetchPlugin();
    }
    
    public function getEntity($sEntityName, $sCallerName = null) {
        require_once(INT_DIR . 'EntityInterface' . FILE_INTERFACE_POSTFIX);
        require_once(PAR_DIR . 'basicEntity' . FILE_PARENT_POSTFIX);
        
        $oEntity = new EntityDriver($sEntityName, $sCallerName);
        return $oEntity->fetchEntity();
    }
    
    public function clearString($sString) {
        $oDB = $this->getLib('phpPdo');
        return $oDB->escape($sString);
    }

    public function strposa($sHaystack, $aNeedles){
        if(!is_array($aNeedles)){
            return false;
        }
        
        foreach($aNeedles as $point){
            if(strpos($sHaystack, $point)){
                return true;
            }
        }
        
        return false;
    }
    
    public function redirect($sUrl, $aParams = null) {
        if(
            !empty($sUrl)
            && $sUrl != CURRENT_URL
            ){
            header('Location: ' . $sUrl);
            exit;
        }
    }
    
    public function reload(){
        header('Location: ' . CURRENT_URL);
        exit;
    }

    public function addError($sFileName, $sName, $bFlag, $sType = '', $sMsg = '') {
        $this->$aErrors[$sFileName]['flags'][$sName] = $bFlag;

        if (/* !$bFlag && */(!empty($sType) && !empty($sMsg))) {
            $this->$aErrors[$sFileName]['messages'][$sName]['type'] = $sType;
            $this->$aErrors[$sFileName]['messages'][$sName]['message'] = $sMsg;
        }
    }

    public function checkError($sFileName, $sName) {
        return $this->$aErrors[$sFileName]['flags'][$sName];
    }

    public function getErrors($sFileName) {
        return $this->$aErrors[$sFileName];
    }

    public function getPost($bFilter = false) {
        $aPost = $_POST;
        if (is_array($aPost) 
            && $bFilter) {
            $aPost = filter_input_array($aPost);
        }
        return $aPost;
    }

    public function getGet($bFilter = false) {
        $aGet = $_GET;
        if (is_array($aGet)
            && $bFilter) {
            $aGet = filter_input_array($bFilter);
        }
        return $aGet;
    }

    public function getFiles($bFilter = false) {
        $aFiles = $_FILES;
        if (is_array($aFiles)
            && $bFilter) {
            $aFiles = filter_input_array($aFiles);
        }
        return $aFiles;
    }

    public function getCookies($bFilter = false) {
        $aCookie = $_COOKIE;
        /*
         * maybe some cleaning / proofing functionalities should be here?
         */
        return $aCookie;
    }

    /**
     * yeah... filtering that would be a b...tch
     * 
     * @return array
     */
    public function getSession() {
        return $_SESSION;
    }

    /**
     * yeah... filtering that would be a b...tch
     * 
     * @return array
     */
    public function getServer() {
        return $_SERVER;
    }

    public function addErrorMessage($sMessage, $sPersist = self::MESSAGE_PERSIST_FLASH) {
        $sType = self::MESSAGE_TYPE_ERROR;
        $this->addSessionMessage($sType.'_'.time(), $sType, $sMessage, $sPersist);
    }
    
    public function addWarningMessage($sMessage, $sPersist = self::MESSAGE_PERSIST_FLASH) {
        $sType = self::MESSAGE_TYPE_WARNING;
        $this->addSessionMessage($sType.'_'.time(), $sType, $sMessage, $sPersist);
    }
    
    public function addInfoMessage($sMessage, $sPersist = self::MESSAGE_PERSIST_FLASH) {
        $sType = self::MESSAGE_TYPE_INFO;
        $this->addSessionMessage($sType.'_'.time(), $sType, $sMessage, $sPersist);
    }
    
    public function addSuccessMessage($sMessage, $sPersist = self::MESSAGE_PERSIST_FLASH) {
        $sType = self::MESSAGE_TYPE_SUCCESS;
        $this->addSessionMessage($sType.'_'.time(), $sType, $sMessage, $sPersist);
    }
    
    private function addSessionMessage($sName, $sType, $sMessage, $sPersist){
        self::$aMessages[$sType][$sName] = $sMessage;
        
        self::$aMessagesIndex[$sName] = [
            'time'  =>  time()
            ,'name' =>  $sName
            ,'type' =>  $sType
            ,'persist'  =>  $sPersist
        ];
        
        if($sPersist == self::MESSAGE_PERSIST_CONSTANT){
            self::$aPersistentMessages[self::MESSAGE_PERSIST_CONSTANT][] = $sName;
        }
        
        if($sPersist == self::MESSAGE_PERSIST_RELOAD){
            self::$aPersistentMessages[self::MESSAGE_PERSIST_RELOAD][] = $sName;
        }
        
        $this->saveSessionData();
    }
    
    public function getSessionMessages($sType = null){
        if($sType != null
           && isset(self::$aMessages[$sType])
          ){
            return self::$aMessages[$sType];
        }
        elseif($sType == null){
            return null;
        }
        else{
            return self::$aMessages;
        }
    }
    
    private function deleteSessionMessage($sName){
        if(!empty(self::$aMessagesIndex[$sName])){
            $aConfig = self::$aMessagesIndex[$sName];
        
            unset(self::$aMessages[$aConfig['type']][$sName]);
            unset(self::$aMessagesIndex[$sName]);
        }
    }
    
    public function cleanSessionMessages(){
        foreach(array_diff(array_keys(self::$aMessagesIndex), self::$aPersistentMessages[self::MESSAGE_PERSIST_CONSTANT]) as $sMessageName){
            $this->deleteSessionMessage($sMessageName);
        }
        
        foreach(array_diff(array_keys(self::$aMessagesIndex), self::$aPersistentMessages[self::MESSAGE_PERSIST_RELOAD]) as $sMessageName){
            unset(self::$aPersistentMessages[self::MESSAGE_PERSIST_RELOAD][array_search($sMessageName, self::$aPersistentMessages[self::MESSAGE_PERSIST_RELOAD])]);
        }
    }
    
    public function setSessionParam($sName, $mValue){
        /*
         * RC_TODO:
         * should be similar to messages mechanism, with one method to assign session data to the session
         */
        $_SESSION[self::SESSION_PARAMS_NAME][$sName] = $mValue;
    }
    
    public function getSessionParam($sName){
        return @$_SESSION[self::SESSION_PARAMS_NAME][$sName];
    }
    
    public function deleteSessionParam($sName){
        unset($_SESSION[self::SESSION_PARAMS_NAME][$sName]);
    }
    
    private function saveSessionData(){
        $_SESSION['messages'] = self::$aMessages;
        $_SESSION['persist_messages'] = self::$aPersistentMessages;
        $_SESSION['messages_index'] = self::$aMessagesIndex; 
    }
    
    public function end() {
        if(self::$bEnded == false){
            self::$bEnded = true;
            $this->cleanSessionMessages();
            $this->saveSessionData();
        }
    }
}
