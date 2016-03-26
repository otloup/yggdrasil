<?php

class bootstrap_tables_viewModule extends Module {
    private $oDic = null;
    private $oTables = null;
    
    public function onInit() {
        $this->oDic = $this->getLib('dic');
        $this->oTables = $this->getLib('tables');
        
    }

    public function setData() {
        $aTables = $this->oTables->getTables();

        $this->setTemplateData([
            'tables' =>  $aTables
        ]);
    }
}
