<?php

class phpPdo extends CoreLib{

    private $mPDO;
    private $sConnectionUrl;
    private $sQuery;
    private $oStatement;
    private $sEntityClass;
    private $sEntityCaller;

    const RESULT_HASH = 'hash';
    const RESULT_ARRAY = 'array';
    const RESULT_ROW = 'row';
    const RESULT_SINGLE = 'single';
    const RESULT_OBJECT = 'object';
    const RESULT_PAGE = 'page';
    const RESULT_ENTITY = 'entity';
    const RESULT_ENTITIES = 'entities';

    const DB_TYPE_PSQL = 'postgres';
    const DB_TYPE_MYSQL = 'mysql';
    
    public function __construct() {
        if ($this->checkCredentials()) {
            $this->constructConnectionUrl();
            if (!$this->connect()) {
                die('couldn\'t connect');
            }
        } else {
            die('wrong psql credentials');
        }
    }

    //private methods

    private function checkCredentials() {
        if (
                DB != '' 
                && DB_HOST != '' 
                && DB_USER != '' 
                && DB_PASS != ''
                && DB_TYPE != ''
        ) {
            return true;
        }

        return false;
    }

    private function constructConnectionUrl() {
        $sDriverName = '';
        
        switch(DB_TYPE){
            case self::DB_TYPE_PSQL: 
                $sDriverName = 'pgsql';
            break;
        
            case self::DB_TYPE_MYSQL:
                $sDriverName = 'mysql';
            break;
        }
        
        $this->sConnectionUrl = $sDriverName.':host='.DB_HOST.';port='.DB_PORT.';dbname='.DB;
    }

    private function connect() {
        try {
            $this->mPDO = new PDO($this->sConnectionUrl, DB_USER, DB_PASS);
            return true;
        } catch (PDOException $e) {
            echo $e->getMessage();
            return false;
        }
    }

    private function getExecutionType() {
        $sQuery = strtolower(trim($this->sQuery));

        switch (true) {
            case (stripos($this->sQuery, 'select') === 0):
                return 'getRecords';
                break;

            case (stripos($this->sQuery, 'insert') === 0):
            case (stripos($this->sQuery, 'delete') === 0):
            case (stripos($this->sQuery, 'update') === 0):
            default:
                return 'executeQuery';
                break;
        }
    }

    private function parseHashResult(){
        $aReturn = [];
        
        while ($row = $this->oStatement->fetch(PDO::FETCH_ASSOC)) {
            $aReturn[] = $row;
        }
        
        return $aReturn;
    }
    
    private function parsePageResult(){
        $aReturn = $this->parseHashResult();

        $aReturn['results'] = count($aReturn['data']);

        $sCountQuery = substr($sQuery, 0, strrpos(strtolower($sQuery), 'limit'));
        
        $aReturn['all_results'] = $this->oStatement->rowCount();
        
        return $aReturn;
    }
    
    private function parseArrayResult(){
        $aReturn = [];
        
        while ($row = $this->oStatement->fetch(PDO::FETCH_BOTH)) {
            $aReturn[] = $row;
        }

        return $aReturn;
    }
    
    private function parseRowResult(){
        return $this->oStatement->fetch(PDO::FETCH_ASSOC);
    }
    
    private function parseSingleResult(){
        $aReturn = $this->oStatement->fetch(PDO::FETCH_ASSOC);
        return reset($aReturn);
    }
    
    private function parseEntityResult($aData = null){
        $oEntity = $this->getEntity($this->sEntityClass, $this->sEntityCaller);
        
        if(empty($aData)){
            $aData = $this->parseRowResult();
        }
        
        $oEntity->load($aData);
        return $oEntity;
    }
    
    private function parseEntitiesResult(){
        $aData = $this->parseHashResult();
        $aEntities = [];
        
        foreach($aData as $aResult){
            $aEntities[] = $this->parseEntityResult($aResult);
        }
        
        return $aEntities;
    }
    
    private function getRecords($aParams, $sFormat = self::RESULT_HASH) {
        $this->oStatement->execute();
        $mReturn = call_user_func(array($this, 'parse' . ucfirst($sFormat) . 'Result'));
        
        return $mReturn;
    }

    private function executeQuery() {
        $this->oStatement->execute();
        return $this->mPDO->lastInsertId();
    }

    //public methods

    public function getLastInsertId($sSequenceName = null){
        return $this->mPDO->lastInsertId($sSequenceName);
    }
    
    public function execute($sQuery, $sResultFormat = self::RESULT_HASH, $aParams = [], $sEntityClass = null, $bDisplayQuery = false) {
        $this->sQuery = $sQuery;
        $this->sEntityClass = $sEntityClass;
        
        if(!empty($sEntityClass)){
            list(,$aCallerData) = debug_backtrace(false);
            
            if(!empty($aCallerData['class']
               && $aCallerData['class'] != __CLASS__)){
                $this->sEntityCaller = $aCallerData['class'];
            }
            else{
                $this->sEntityCaller = null;
            }
        }
        
        $bDisplay = end(func_get_args());
        if (gettype($bDisplay) == 'boolean'
                && $bDisplay) {
            print $this->sQuery . "\r\n<br />" . print_r($aParams, true);
        }
        
        try {
            $this->oStatement = $this->mPDO->prepare($this->sQuery);
            $this->mPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            $this->mPDO->setAttribute(PDO::ATTR_EMULATE_PREPARES,TRUE);
            
            if(!empty($aParams)){
                foreach($aParams as $sParameter=>$mValue){
                    if(is_array($mValue)){
                        $this->oStatement->bindValue($sParameter, $mValue[0], $mValue[1]);
                    }
                    else{
                        $this->oStatement->bindValue($sParameter, $mValue);
                    }
                    
                }
            }
            
        } catch (PDOException $ePdoException){
            print $this->sQuery . "\n\r";
            print "<pre>" . print_r($aParams, true) . "</pre>\n\r";
            print "<h1>Error</h1>\n\r";
            print "<pre>" . $ePdoException->getMessage() . "</pre>\n\r";
            print "<pre>" . print_r($ePdoException->getTrace(), true) . "</pre>\n\r";
            exit;
        }
        

        $mReturn = call_user_func(array($this, $this->getExecutionType()), $aParams, $sResultFormat);
        
        
        return $mReturn;
    }

    public function escape($sValue) {
        return htmlspecialchars(trim($sValue));
    }

    public function __destruct() {
        $this->mPDO = null;
    }

    
}
