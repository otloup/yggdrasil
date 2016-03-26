<?php

class tableTypes extends CoreLib {
  
    private $oDB = null;
    
    public function __construct() {
        $this->oDB = $this->getLib('phpPdo');
    }
    
    public function getAll(){
        $sSql = "SELECT "
                . "id, name, properties "
                . "FROM "
                . " tables_types_dic";
        
        return $this->oDB->execute($sSql, phpPdo::RESULT_ENTITIES, null, 'tableDataTypes');
    }
    
}

?>