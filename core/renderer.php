<?php

class renderer extends setup {

    private $aUsedModules = array();
    private $oDB = null;
    private $bPageExists = false;

    public function __construct(requestParser $oRequest) {
        $this->start();
     
        $oAuth = $this->getLib('auth');
        $this->bPageExists = $this->checkPageExistence($oRequest->sParsedRequest);
        
        if($this->bPageExists === false){
            $this->addErrorMessage("page \"".$oRequest->sParsedRequest."\" doesn't exist");
            throw new Exception("page \"".$oRequest->sParsedRequest."\" doesn't exist", __LINE__);
        }
                
        $this->loadPage();
        
        $iPageAccess = $this->getPageAccess();
        
        if($iPageAccess != Page::ACCESS_ALL){
            if(!$oAuth->getIsLogged()
               || !($iPageAccess & $oAuth->getAccessLevel())){
                
                $this->setSessionParam('rurl', CURRENT_URL);
                $this->setSessionParam('access', $iPageAccess);
                $this->redirect(PAGE_LOGIN);
            }
        }
        
        ob_start();
        $this->executePageLoad();
        ob_end_flush();
        
        $this->end();
    }

}

?>
