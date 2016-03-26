<?php

class setup extends CoreLib {
    
    private $aPageConfig = [];
    private $aRenderConf = [];
    private $oPage = null;
    private $iPageAccess = 0;
    private $sPageType = null;
    private $sPageName = "";
    private $sPagePath = "";
    
    public function __construct() {
        parent::__construct();
        
        require_once(CONF_DIR . 'render_conf.php');
        
        $this->aRenderConf = $aRenderConf;
    }
    
    /**
     * Check if path leads to a directory. If so, execute default page;
     * If not, execute page with name corresponding to the last string of path
     * i.e.
     * $sPath = 'a/b/c'
     * DEFAULT_PAGE_NAME = 'index'
     * check if "c" is a directory. If so, execute DEFAULT_PAGE_NAME.page at path pages/a/b/c/DEFAULT_PAGE_NAME.page
     * otherwise, execute page at path pages/a/b/c.page
     * 
     * @param String $sPath
     */
    protected function checkPageExistence($sPath){
        $aPageConfig = $this->getPageConfig($sPath);
        
        if(!empty($aPageConfig)){
            /**
             * RC_TODO:
             * add functions responsible for alternative actions, when available
             * in page configuration
             */
        }
        
        /**
         * RC_TODO:
         * currently all pages have basic type
         */
        
        if(empty($aPageConfig['type'])){
            $this->setPageType(PAGE_TYPE_BASIC);
        }
        
        if(is_dir(PAGES_DIR . $sPath)){
            $this->setPageName(DEFAULT_PAGE);
            $this->setPagePath(PAGES_DIR . $sPath . '/' . DEFAULT_PAGE . FILE_PAGE_POSTFIX);
            return true;
        }
        elseif(file_exists(PAGES_DIR . $sPath . FILE_PAGE_POSTFIX)){
            $this->setPageName(basename($sPath));
            $this->setPagePath(PAGES_DIR . $sPath . FILE_PAGE_POSTFIX);
            return true;
        }
        else{
            throw new Exception('page ' . PAGES_DIR . $sPath . DEFAULT_PAGE . FILE_PAGE_POSTFIX . ', or '.PAGES_DIR . $sPath . FILE_PAGE_POSTFIX.' doesn\'t exist', __LINE__);
        }
    }
    
    
    protected function loadPage(){
        switch($this->getPageType()){
            case PAGE_TYPE_BASIC:
                require_once(INT_DIR . 'BasicPageInterface' . FILE_INTERFACE_POSTFIX);
                require_once(PAR_DIR . 'Page' . FILE_PARENT_POSTFIX);
                
                $this->loadBasicPage($this->getPagePath(), $this->getPageName());
            break;
        
            case PAGE_TYPE_JSON:
                $this->loadJsonPage($this->getPagePath(), $this->getPageName());
            break;
        
            default:
                throw new Exception("unsupported page type \"".$this->getPageType()."\"", __LINE__);
            break;
        }
    }
    
    private function loadBasicPage($sPath, $sPageName){
        require_once($sPath);
        $sPageName = $sPageName . CLASS_PAGE_POSTFIX;
        $this->oPage = new $sPageName;
        
        if($this->oPage instanceof Page){
            $this->setPageAccess($this->oPage->iAccess);
        }
        else{
            unset($this->oPage);
            throw new Exception("page ".$sPageName." is not an instance of a basic page", __LINE__);
        }
    }
    
    private function loadJsonPage(){}
    
    public function executePageLoad(){
        switch($this->getPageType()){
            case PAGE_TYPE_BASIC:
                $this->executeBasicPageLoad();
            break;
        
            case PAGE_TYPE_JSON:
                $this->executeJsonPageLoad();
            break;
        
            default:
                throw new Exception("unsupported page type \"".$this->getPageType()."\"", __LINE__);
            break;
        }
    }
    
    private function executeBasicPageLoad(){
        $this->oPage->constructPage();
        $this->oPage->onInit();
        $this->oPage->beforeRender();
//        $this->oPage->setData();
        
        if(!empty($_POST) || !empty($_GET)){
            $this->oPage->onSubmit($this->getPost(), $this->getGet());
        }
        
        $this->oPage->renderPage();
        $this->oPage->afterRender();
        
        
        
//        $this->oPage->onXHR();
    }
    
    private function executeJsonPageLoad(){}
    
    /**
     * Getters & setters
     */
    
    private function setPageConfig($aConfig){
        $this->aPageConfig = $aConfig;
    }
    
    public function getPageConfig($sPath = null){
        if($sPath == NULL
           && !empty($this->aPageConfig)){
           return $this->aPageConfig;
        }
        else{
            if(isset($this->aPageConfig[$sPath])){
                $this->setPageConfig($this->aPageConfig[$sPath]);
            }
        }
    }
    
    private function setPageName($sName){
        $this->sPageName = $sName;
    }
    
    public function getPageName(){
        return $this->sPageName;
    }

    private function setPagePath($sPath){
        $this->sPagePath = $sPath;
    }
    
    public function getPagePath(){
        return $this->sPagePath;
    }
    
    private function setPageAccess($iAccessLevel){
        $this->iPageAccess = $iAccessLevel;
    }
    
    public function getPageAccess(){
        return $this->iPageAccess;
    }
    
    private function setPageType($sType){
        $this->sPageType = $sType;
    }
    
    public function getPageType() {
        return $this->sPageType;
    }
}
