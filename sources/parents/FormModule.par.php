<?php

/*
 * RC_TODO:
 * should extend basic module
 */

abstract class FormModule extends Module implements BasicFormModule {
    protected $sTemplatePath;
    protected $oTemplatePlugin;
    protected $aFormErrors = [];
    
    private $aTemplateData = [];
    private $oDic = null;
    

    public function constructModule($sTplName) {
        if(!isset($this->sTemplatePath) || empty($this->sTemplatePath)){
            $this->sTemplatePath = TPL_DIR . $sTplName . FILE_TPL_POSTFIX;
        }
        
        $this->oTemplatePlugin = $this->getPlugin('smarty3');
        $this->oDic = $this->getLib('dic');
    }
    
    public function getContents(){
        return $this->aTemplateData;
    }
    
    public function printContents(){
        $this->oTemplatePlugin->display($this->sTemplatePath);
    }
    
    public function fetchContents(){
        return $this->oTemplatePlugin->fetch($this->sTemplatePath);
    }
    
    protected function setTemplateData($aData){
        $this->aTemplateData = array_merge($this->aTemplateData, $aData);
        $this->oTemplatePlugin->assign($this->aTemplateData);
    }
    
    public function setForm() {
        
    }
    
    public function onFailure() {
        ;
    }
    
    public function onSuccess($aPost, $aGet) {
        
    }
    
    public function onValidate($aPost, $aGet) {
        /*
         * RC_TODO:
         * this method musn't contain failsafe for get parameters, which checks if post has submit field
         * possible solution might be to disallow form submit via get, so the form triggers won't be fired when $_GET is present
         */
    }
    
    protected function addFormError($sField, $sErrorType){
        $this->aFormErrors[$sField][] = $this->oDic->get(__CLASS__. '_' .$sErrorType);
    }

    protected function getFormErrors($bFlat = false){
        if($bFlat){
            $aErrors = [];

            $aErrorsIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($this->aFormErrors));
            foreach($aErrorsIterator as $aError) {
              $aErrors[] = $aError;
            }

            return $aErrors;
        }
        else{
            return $this->aFormErrors;
        }
    }
}
