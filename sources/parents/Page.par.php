<?php

/*
 * RC_TODO:
 * OPTI-FUC***N-MISE!
 */

abstract class Page extends CoreLib implements BasicPage {

    const ACCESS_ALL = PAGE_ACCESS_ALL;
    const ACCESS_LOGIN = PAGE_ACCESS_LOGIN;
    const ACCESS_ADMIN = PAGE_ACCESS_ADMIN;
    const ASSET_TYPE_JS = 'js';
    const ASSET_TYPE_CSS = 'css';
    const ASSET_PLACEMENT_TOP = 'top';
    const ASSET_PLACEMENT_BOTTOM = 'bot';
    const MODULE_DELIMITER = <<<EOS

   <!-- MODULE -->
                
EOS;

    public $iAccess = self::ACCESS_ALL;
    
    protected $sLayoutName = null;
    protected $oGlobalLib = null;
    protected $oAuth = null;
    protected $aLayoutConfig = [];
    protected $oTemplatePlugin = null;
    protected $aAssets = [];
    protected $aAssetsTemplate = [];
    
    private $aModules = [];
    private $aTypedModules = [];
    private $bLayout = false;
    private $aPageTemplateData = [];

    /*
     * PAGE TRIGGERS
     */

    public function constructPage() {
        $this->constructElements();
    }

    private function constructElements() {
        $this->oGlobalLib = $this->getLib('globalUtil');
        $this->oAuth = $this->getLib('auth');

        $this->aAssets = [
            self::ASSET_TYPE_JS => [
                Page::ASSET_PLACEMENT_TOP   =>  []
                ,Page::ASSET_PLACEMENT_BOTTOM   =>  []
            ]
            , self::ASSET_TYPE_CSS => [
                Page::ASSET_PLACEMENT_TOP   =>  []
                ,Page::ASSET_PLACEMENT_BOTTOM   =>  []
            ]
        ];
        
        $this->aAssetsTemplate = [
            self::ASSET_TYPE_JS => '<script type="text/javascript" src="%s" data-inject="system_inject" data-inject_name="%s"></script>'
            , self::ASSET_TYPE_CSS => '<link href="%s" type="text/css" rel="stylesheet" media="%s" data-inject="system_inject" data-inject_name="%s" />'
        ];
    }

    public abstract function onInit();

    public abstract function beforeRender();

    public abstract function afterRender();

    public abstract function onXHR();

    public abstract function onEnd();

    protected function setData() {
        
    }

    public function onSubmit($aPost, $aGet) {
        if (!empty($this->aTypedModules['form'])) {
            foreach ($this->aTypedModules['form'] as $oModule) {
                if ($oModule->onValidate($aPost, $aGet)) {
                    $oModule->onSuccess($aPost, $aGet);
                } else {
                    $oModule->onFailure();
                }
            }
        }
    }

    /*
     * PAGE TRIGGERS END
     * 
     * ASSETS HANDLE
     */

    private function loadJs($sPath, $aParams, $sPlacement) {
        $sParams = json_encode($aParams, JSON_FORCE_OBJECT);
        $sName = basename($sPath);

        $sPath .= '?v=' . APP_VERSION;
        
        if(STAGE == APP_STAGE_DEVELOPEMENT){
            $sPath .= '&t='.time();
        }
        
        $this->aAssets[self::ASSET_TYPE_JS][$sPlacement][] = [
            'path' => $sPath
            , 'name' => $sName
            , 'params' => $sParams
        ];
    }

    private function loadCss($sPath, $aParams, $sPlacement) {
        $sMedia = !empty($aParams['media']) ? $aParams['media'] : 'all';
        $sParams = http_build_query($aParams);
        
        $sParams = empty($sParams) ? '?v=' . APP_VERSION : '&v=' . APP_VERSION;
        
        if(STAGE == APP_STAGE_DEVELOPEMENT){
            $sParams .= '&t='.time();
        }
        
        $sName = basename($sPath);

        $this->aAssets[self::ASSET_TYPE_CSS][$sPlacement][] = [
            'path' => $sPath
            , 'name' => $sName
            , 'params' => $sParams
            , 'media'   =>  $sMedia
        ];
    }

    public function loadAsset($sPath, $sUrl, $sPlacement, $sType, $aParams) {
        switch ($sType) {
            case self::ASSET_TYPE_CSS :
                if (file_exists($sPath)) {
                    $this->loadCss($sUrl, $aParams, $sPlacement);
                } else {
                    /*
                     * RC_TODO:
                     * should throw an exception
                     */
                    die('css file "' . $sPath . '" does not exist');
                }
                break;

            case self::ASSET_TYPE_JS :
                if (file_exists($sPath)) {
                    $this->loadJs($sUrl, $aParams, $sPlacement);
                } else {
                    /*
                     * RC_TODO:
                     * should throw an exception
                     */
                    die('js file "' . $sPath . '" does not exist');
                }
                break;
        }
    }

    public function getCssAssets($sPlacement = null) {
        $aAssets = !empty($sPlacement) ? $this->aAssets[self::ASSET_TYPE_CSS][$sPlacement] : $this->aAssets[self::ASSET_TYPE_CSS][self::ASSET_PLACEMENT_TOP] + $this->aAssets[self::ASSET_TYPE_CSS][self::ASSET_PLACEMENT_BOTTOM];
        $sAssets = "\r\n";
        $sTemplate = $this->aAssetsTemplate[self::ASSET_TYPE_CSS];
        
        if(!empty($aAssets)){
            foreach($aAssets as $asset){
                $sAssets .= sprintf($sTemplate, $asset['path'].$asset['params'], $asset['media'], $asset['name']) . "\r\n";
            }
        }
        
        return $sAssets;
    }

    public function getJsAssets($sPlacement = null) {
        $aAssets = !empty($sPlacement) ? $this->aAssets[self::ASSET_TYPE_JS][$sPlacement] : $this->aAssets[self::ASSET_TYPE_JS][self::ASSET_PLACEMENT_TOP] + $this->aAssets[self::ASSET_TYPE_JS][self::ASSET_PLACEMENT_BOTTOM];
        $sAssets = "\r\n";
        $sTemplate = $this->aAssetsTemplate[self::ASSET_TYPE_JS];
        
        if(!empty($aAssets)){
            foreach($aAssets as $asset){
                $sAssets .= sprintf($sTemplate, $asset['path'], $asset['name']) . "\r\n";
            }
        }
        
        return $sAssets;
    }

    private function loadTopAssets(BasicModule $oModule) {
        $oModule->modifyTemplate(
                /* inject param */ $this->getCssAssets(self::ASSET_PLACEMENT_TOP) . "\n\r" . $this->getJsAssets(self::ASSET_PLACEMENT_TOP)
                /* placement param */, Module::MODIFY_PLACEMENT_AFTER
                /* anchor param */, '</title>'
        );
    }

    /*
     * RC_TODO:
     * DOESN'T WORK!
     */
    private function loadBottomAssets(BasicModule $oModule) {
        $oModule->modifyTemplate(
                /* inject param */ $this->getCssAssets(self::ASSET_PLACEMENT_BOTTOM) . "\n\r" . $this->getJsAssets(self::ASSET_PLACEMENT_BOTTOM)
                /* placement param */, Module::MODIFY_PLACEMENT_BEFORE
                /* anchor param */, '</body>'
        );
    }

    /*
     * ASSETS HANDLE END
     * 
     * 
     * INJECT MODULES
     */

    private function injectModule($aInterfaces, $aParents, $sControler, $sName) {
        foreach ($aInterfaces as $interface) {
            $sInterfacePath = INT_DIR . $interface . FILE_INTERFACE_POSTFIX;

            if (file_exists($sInterfacePath)) {
                require_once($sInterfacePath);
            } else {
                /*
                 * RC_TODO:
                 * should throw an exception
                 */
                die('Interface "' . $interface . '" was not found as specified path ("' . $sInterfacePath . '")');
            }
        }

        foreach ($aParents as $parent) {
            $sParentPath = PAR_DIR . $parent . FILE_PARENT_POSTFIX;

            if (file_exists($sParentPath)) {
                require_once($sParentPath);
            } else {
                /*
                 * RC_TODO:
                 * should throw an exception
                 */
                die('Parent class "' . $parent . '" was not found as specified path ("' . $sParentPath . '")');
            }
        }

        if (file_exists(SRC_DIR . $sName . '.php')) {
            require_once(SRC_DIR . $sName . '.php');

            $sModuleName = $sName . CLASS_MODULE_POSTFIX;
            $oModule = new $sModuleName;

            if ($oModule instanceof $sControler) {
                $oModule->setParentPage($this);
                $sDefaultTplName = str_replace(CLASS_MODULE_POSTFIX, '', $sModuleName);
                $oModule->constructModule($sDefaultTplName);
                $oModule->onInit();
                $oModule->setData();

                return $oModule;
            } else {
                throw new Exception("module \"" . $sModuleName . "\" isn't a '.$sControler.'", __LINE__);
            }
        }
    }

    protected function injectBasicModule($sModuleName) {
        $oModule = $this->injectModule(['BasicModuleInterface'], ['Module'], 'Module', $sModuleName);
        $sModuleName = $sModuleName . CLASS_MODULE_POSTFIX;
        $this->aTypedModules['basic'][$sModuleName] = $this->aModules[$sModuleName] = $oModule;
        return $oModule;
    }

    protected function injectFormModule($sModuleName) {
        $oModule = $this->injectModule(['BasicModuleInterface', 'FormModuleInterface'], ['Module', 'FormModule'], 'FormModule', $sModuleName);
        $sModuleName = $sModuleName . CLASS_MODULE_POSTFIX;
        $this->aTypedModules['form'][$sModuleName] = $this->aModules[$sModuleName] = $oModule;
        return $oModule;
    }

    protected function injectDataModule() {
        
    }

    /**
     * inject module without source - only html
     */
    protected function injectHtmlModule() {
        
    }

    /*
     * INJECT MODULES END
     * 
     * HANDLE MODULES
     */

    public function getModule($sModuleName) {
        $sModuleName = $sModuleName . CLASS_MODULE_POSTFIX;
        if (empty($this->aModules[$sModuleName])) {
            /*
             * RC_TODO:
             * SHOULD THROW AN EXCEPTION!!!
             */
            print 'module '.$sModuleName.' is not implementeda';
            exit();
            return null;
        } else {
            return $this->aModules[$sModuleName];
        }

        /*
         * RC_TODO:
         * SHOULD THROW AN EXCEPTION!!!
         */
    }

    private function getModuleContents($mModuleName) {
        if (is_array($mModuleName)) {
            $aModulesData = [];

            foreach ($mModuleName as $sModuleName) {
                $aModulesData[$sModuleName] = $this->getModule($sModuleName)->fetchContents();
            }
            return $aModulesData;
        } else {
            return $this->getModule($sModuleName)->fetchContents();
        }
    }

    /*
     * HANDLE MODULES END
     * 
     * HANDLE / RENDER PAGE
     */

    public function assignLayout($sLayoutName, $aLayoutConfig = null) {
        /**
         * RC_TODO:
         * 1) check if layout exists (or use some core lib inject method)
         * 2) validate layout configuration (or use some core lib inject method)
         */
        $this->bLayout = true;
        $this->sLayoutName = $sLayoutName;
        $this->aLayoutConfig = $aLayoutConfig;

        $this->oTemplatePlugin = $this->getPlugin('smarty3');
    }

    public function renderPage() {
        if ($this->bLayout) {
            $this->printPageToLayout();
        } else {
            $this->printPage();
        }
    }

    private function printModule($sModuleName) {
        $this->getModule($sModuleName)->printContents();
    }

    private function printPage() {
        foreach ($this->aModules as $oModule) {
            if ($oModule === reset($this->aModules)) {
                $this->loadTopAssets($oModule);
            }

            if ($oModule === end($this->aModules)) {
                $this->loadBottomAssets($oModule);
            }

            $oModule->printContents();
        }
    }

    private function preparePageForLayout() {
        /*
         * this is a hogwash!
         * if layouts would be implemented, they probably wouldn't implement render spaces as names of modules... or not
         * if there would be one layout for one page, this is possible. If there would be one layout for several pages, this would be troublesome... 
         * each module woud have to be assigned to a correct slot in layout , such as header, footer, left column, or content...
         * Theoreticaly, at page init, or in page config, there could be some kind of translator array, containing laout of modules according to 
         * slots available in layout file, eg. 
         * aray(
         *  'header'    =>  'header'
         *  'content'   =>  'user_data_grid,user_detailed_info'
         *  'right_col' =>  'menu'
         *  'footer'    =>  'footer'
         * )
         */

        /*
         * RC_TODO:
         * if a module is to be included in layout, it has to be injected first
         * it may be a bit unnecesary, since there is a possibility of simply injecting modules when a layout is rendering, 
         * but for now i have decided it is a good way to continue
         * 
         * ...
         * 
         * It can also be engeneered in such matter, that only modules that has to be initiaded and configured by the page driver
         * will be injected outside of layout configuration...
         */
        if (empty($this->sLayoutName) || empty($this->aModules)) {
            /*
             * RC_TODO:
             * should throw an exception!!!
             */
            return false;
        }
        $aPageData = [];

        if (!empty($this->aLayoutConfig)) {
            foreach ($this->aLayoutConfig as $sSlotName => $sModule) {
                $aModules = explode(',', $sModule);
                if ($sModule === reset($this->aLayoutConfig)) {
                    $oModule = $this->getModule($aModules[0]);
                    if($oModule instanceof BasicModule){
                        $this->loadTopAssets($oModule);
                    }
                }

                if ($sModule === end($this->aLayoutConfig)) {
                    $oModule = $this->getModule(end($aModules));
                    if($oModule instanceof BasicModule){
                        $this->loadBottomAssets($oModule);
                    }
                }

                $aPageData[$sSlotName] = implode(self::MODULE_DELIMITER, $this->getModuleContents($aModules));
            }
        } else {
            foreach ($this->aModules as $sModuleName => $oModule) {
                $aPageData[$sModuleName] = $oModule->fetchContents();
            }
        }

        return $this->aPageTemplateData = $aPageData;
    }

    private function printPageToLayout() {
        if ($this->preparePageForLayout()) {
            $this->oTemplatePlugin->assign($this->aPageTemplateData);
            $this->oTemplatePlugin->display(LAY_DIR . $this->sLayoutName . FILE_LAY_POSTFIX);
        }

        return false;
    }

    /*
     * HANDLE / RENDER PAGE END
     */
}
