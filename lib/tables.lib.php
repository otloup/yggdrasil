<?php

class tables extends CoreLib {
  
    private $oDB = null;
    
    public function __construct() {
        $this->oDB = $this->getLib('phpPdo');
    }
    
    public function getAllTableNames(){
        $sSql = "SELECT id, name FROM tables_dic";
        
        return $this->oDB->execute($sSql, phpPdo::RESULT_HASH);
    }
    
    public function addNewTable($iParent=1, $sName, $aColumns, $aColumnTypes){
        //add new table
        $this->oDB->execute(""
                . "INSERT INTO "
                . " tables_dic ("
                . "parent_id, name"
                . ") VALUES (:parent, :name)", null, [
                    ':parent'   =>  [$iParent, PDO::PARAM_INT]
                    ,':name'    =>  [$sName, PDO::PARAM_STR]
                ]);
        
        $iTableId = $this->oDB->getLastInsertId('tables_dic_id_seq');
        
        if(!$iTableId){
            return false;
        }
        
        //add new columns
        
        foreach($aColumns as $index => $name){
            $this->oDB->execute(""
                    . "INSERT INTO "
                    . " tables_columns_dic ("
                    . "     col_name, col_type, table_id"
                    . ") VALUES ("
                    . " :name, :type, :table"
                    . ")", null, [
                        ':name' =>  $name
                        ,':type'    =>  $aColumnTypes[$index]
                        ,':table'   =>  $iTableId
                    ]);
            
            $iColumnId = $this->oDB->getLastInsertId('tables_setup_id_seq');
            if(!$iColumnId){
                return false;
            }
        }
        
        return true;
    }
    
    public function checkIfExists($sName){
        return (bool) $this->oDB->execute('SELECT COUNT(id) FROM tables_dic WHERE name = :name', phpPdo::RESULT_SINGLE, [':name' =>  [$sName, PDO::PARAM_STR]]);
    }
    
    public function getIdByName($sName){
        return $this->oDB->execute('SELECT id FROM tables_dic WHERE name = :name', phpPdo::RESULT_SINGLE, [':name' =>  [$sName, PDO::PARAM_STR]]);
    }
    
    public function getTableData($iId){
        //get parent name
        $aParent = $this->oDB->execute(""
                . "SELECT"
                . " td.name, td.id"
                . " FROM"
                . " tables_dic td"
                . " WHERE"
                . " td.id = (SELECT t.parent_id FROM tables_dic t WHERE t.id = :id)", phpPdo::RESULT_ROW, [':id' => [$iId, PDO::PARAM_INT]]);
        
        //get table columns
        $aColumns = $this->oDB->execute(""
                . "SELECT"
                . " col_name, col_type"
                . " FROM"
                . " tables_columns_dic"
                . " WHERE"
                . " table_id = :id", phpPdo::RESULT_HASH, [':id'    =>  [$iId, PDO::PARAM_INT]]);
        
        //construct array
        $iParentId = empty($aParent['id']) ? 0 : $aParent['id'];
        return ['parent_id'    =>  $iParentId, 'parent_name'    =>  $aParent['name'], 'columns' =>  $aColumns];
    }
 
    public function getTables(){
        //name, parent, number of columns
        $aTables = $this->oDB->execute(""
                . "SELECT"
                . " td.name, "
                . " (SELECT tp.name FROM tables_dic tp WHERE tp.id = td.parent_id) as parent, "
                . " (SELECT COUNT(tc.id) FROM tables_columns_dic tc WHERE tc.table_id = td.id) as columns"
                . " FROM"
                . " tables_dic td");
        
        //RC_TODO: add limits, and status!!!
        return $aTables;
    }
}

?>