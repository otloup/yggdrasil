<?php

    class bootstrap_tables_addModule extends FormModule {
        private $oDic = null;
        private $oUtil = null;
        private $oTables = null;
        private $oTableTypes = null;
        
        public function onInit() {
            $this->oDic = $this->getLib('dic');
            $this->oUtil = $this->getLib('globalUtil');
            
            $this->oTables = $this->getLib('tables');
            $this->oTableTypes = $this->getLib('tableTypes');
        }
        
        public function setData() {
            $aTables = $this->oTables->getAllTableNames();
            $aTablesTypes = $this->oTableTypes->getAll();
            
            $aSuccessMessages = $this->getSessionMessages(CoreLib::MESSAGE_TYPE_SUCCESS);
            $aErrorMessages = $this->getSessionMessages(CoreLib::MESSAGE_TYPE_ERROR);
            
            $this->setTemplateData([
                'tables' =>  $aTables
                ,'types'    =>  $aTablesTypes  
                ,'action'    =>  CURRENT_URL
                ,'errors'   =>  $this->getFormErrors()
                ,'collective_errors'    =>  $this->getFormErrors(true)
                ,'successMessages' =>  $aSuccessMessages
                ,'errorMessages' =>  $aErrorMessages
            ]);
        }
        
        public function onFailure() {
            
        }
        
        public function onValidate($aPost, $aGet) {
            if(empty($aPost['submit'])){
                return false;
            }
            
            $aColumns = array_filter($aPost['column_name']);
            $aColumnTypes = array_filter($aPost['column_type']);
            
            if(preg_match('/[^a-z_0-9]/i', $aPost['table_name'])){
                $this->addErrorMessage('nazwa tabeli może zawierać wyłącznie znaki alfabetu łacińskiego, cyfry, oraz pokreślenia');
                $this->reload();
            }
            
            if($this->oTables->checkIfExists($aPost['table_name'])){
                $this->addErrorMessage('tabela o podanej nazwie już istnieje');
                $this->reload();
            }
            
            $aColumnsCopy = $aColumns;
            
            foreach($aColumns as $key => $sName){
                unset($aColumnsCopy[$key]);
                
                if(preg_match('/[^a-z_\-0-9]/i', $sName)){
                    $this->addErrorMessage('nazwa kolumny może zawierać wyłącznie znaki alfabetu łacińskiego, cyfry, myślniki, oraz pokreślenia');
                    $this->reload();
                }
                
                if(array_search(strtolower($sName), array_map('strtolower', $aColumnsCopy))){
                    $this->addErrorMessage('nazwy kolumn w obrębie tej samej tablicy nie mogą się powtarzać');
                    $this->reload();
                }
            }
            
            if($this->oTables->addNewTable(intval($aPost['parent_table']), $aPost['table_name'], $aColumns, $aColumnTypes)){
                $this->addSuccessMessage('Tabela "'.$aPost['table_name'].'" została dodana', CoreLib::MESSAGE_PERSIST_RELOAD);
                $this->reload();
            }
            else{
                $this->addErrorMessage('Tabela "'.$aPost['table_name'].'" nie została dodana. Spróbuj ponownie później');
                $this->reload();
            }
        }
        
        public function onSuccess($aPost, $aGet) {
            /*
             * RC_TODO:
             * dependinf from outcome from onValidate, onSuccess, or onFailure should be called automatically
             */
        }
    }

    ?>


