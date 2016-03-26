<?php

class userPrivileges extends CoreLib {
  
    private $oDB = null;
    
    public function __construct() {
        $this->oDB = $this->getLib('phpPdo');
    }
    
    public function getAll(){
        $sSql = "SELECT "
                . "id, name, value "
                . "FROM "
                . " dic_privileges";
        
        return $this->oDB->execute($sSql, phpPdo::RESULT_HASH);
    }
    
}

?>