<?php

abstract class Module extends CoreLib implements BasicModule {
    const MODIFY_PLACEMENT_BEFORE = 'before';
    const MODIFY_PLACEMENT_AFTER = 'after';
    
    protected $sTemplatePath;
    protected $oTemplatePlugin;
    protected $oParentPage;

    private $aTemplateData = [];
    private $bModifyTemplate = false;
    private $aModifyParams = [];

    public function constructModule($sTplName) {
        if(!isset($this->sTemplatePath) || empty($this->sTemplatePath)){
            $this->sTemplatePath = TPL_DIR . $sTplName . FILE_TPL_POSTFIX;
        }
        
        $this->oTemplatePlugin = $this->getPlugin('smarty3');
    }
    
    public function getContents(){
        return $this->aTemplateData;
    }
    
    public function printContents(){
        if($this->bModifyTemplate){
            print $this->fetchContents();
        }
        else{
            $this->oTemplatePlugin->display($this->sTemplatePath);
        }
    }
    
    public function fetchContents(){
        $sContent = $this->oTemplatePlugin->fetch($this->sTemplatePath);
        
        if($this->bModifyTemplate){
            $sContent = $this->modifyContents($sContent);
        }
        
        return $sContent;
    }
    
    public function setParentPage(Page $oParent){
        $this->oParentPage = $oParent;
    }
    
    public function getParentPage(){
        return $this->oParentPage;
    }
    
    protected function setTemplateData($aData){
        $this->aTemplateData = array_merge($this->aTemplateData, $aData);
        $this->oTemplatePlugin->assign($this->aTemplateData);
    }
    
    private function addAsset($sPath, $sUrl, $sPlacement, $sType, $aParams){
        if(!empty($this->oParentPage)){
            $this->oParentPage->loadAsset($sPath, $sUrl, $sPlacement, $sType, $aParams);
        }
        /*
         * RC_TODO:
         * should throw an exception
         */
    }
    
    protected function loadJs($sPartialPath, $sPlacement = Page::ASSET_PLACEMENT_BOTTOM, $aParams = []){
        $sPath = JS_DIR . $sPartialPath . FILE_JS_POSTFIX;
        $sUrl = URL_JS . $sPartialPath . FILE_JS_POSTFIX;
        $this->addAsset($sPath, $sUrl, $sPlacement, Page::ASSET_TYPE_JS, $aParams);
    }
    
    protected function loadCss($sPartialPath, $sPlacement = Page::ASSET_PLACEMENT_BOTTOM, $aParams = []){
        $sPath = CSS_DIR . $sPartialPath . FILE_CSS_POSTFIX;
        $sUrl = URL_CSS . $sPartialPath . FILE_CSS_POSTFIX;
        $this->addAsset($sPath, $sUrl, $sPlacement, Page::ASSET_TYPE_CSS, $aParams);
    }
    
    public function modifyTemplate($sInject, $sPlacement, $sAnchor){
        $this->bModifyTemplate = true;
        $this->aModifyParams = [
            'inject'    =>  $sInject
            ,'placement'    =>  $sPlacement
            ,'anchor'   =>  $sAnchor
        ];
    }
    
    /*
     * RC_TODO:
     * currently, all css and js files are included only at the top of the page. 
     * modify contents should take under consideration only rendered module;
     */
    
    private function modifyContents($sContent){
        if(!empty($this->aModifyParams['anchor'])
           && !empty($this->aModifyParams['placement'])
        ){
          $aRawPosData = [];
          $iPosition = -1;
          
          preg_match('/('.  str_replace('/', '\/', $this->aModifyParams['anchor']).')/', $sContent, $aRawPosData, PREG_OFFSET_CAPTURE);
          
          if(!empty($aRawPosData[0]) 
             && !empty($aRawPosData[0][1])){
             $iPosition = $aRawPosData[0][1];
          }
          else{
              $iPosition = 0;
          }
          
          switch($this->aModifyParams['placement']){
              case self::MODIFY_PLACEMENT_BEFORE :
                  $iPosition = $iPosition == 0 ? 0 : $iPosition -1;
              break;
          
              case self::MODIFY_PLACEMENT_AFTER :
                  $iPosition = $iPosition == 0 ? 0 : $iPosition + strlen($aRawPosData[0][0]);
              break;
          }
          
          return substr_replace($sContent, $this->aModifyParams['inject'], $iPosition, 0);
        }
        else{
            /*
             * RC_TODO:
             * should throw an exception
             */
            
            return null;
        }
    }
    
    public abstract function setData();
    
    public abstract function onInit();
}
