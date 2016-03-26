<?php

    class bootstrap_loginModule extends FormModule {
        private $oAuth = null;
        private $oDic = null;
        private $oUtil = null;
        
        public function onInit() {
            $this->oAuth = $this->getLib('auth');
            $this->oDic = $this->getLib('dic');
            $this->oUtil = $this->getLib('globalUtil');
            
            if(
                !empty($this->getSessionParam('rurl'))
                && $this->oAuth->getIsLogged()
                ){
                $sRurl = $this->getSessionParam('rurl');
                $this->deleteSessionParam('rurl');
                $this->redirect($sRurl);
            }
            elseif(empty($this->getSessionParam('rurl'))
               && $this->oAuth->getIsLogged()){
                $this->redirect(BASE_URL);
            }
        }
        
        public function setData() {
            $this->setTemplateData([
                'action'    =>  CURRENT_URL
                ,'errors'   =>  $this->getFormErrors()
                ,'collective_errors'    =>  $this->getFormErrors(true)
            ]);
        }
        
        public function onFailure() {
            $this->setData();
            //exit('failure');
        }

        public function onValidate($aPost, $aGet) {
            if(empty($aPost['submit'])){
                return false;
            }
            
            $bValid = true;
            
            if(empty($aPost['email'])){
                $this->addFormError('email', 'empty_email');
                $bValid = false;
            }
            
            if(!$this->oUtil->validateEmail($aPost['email'])){
                $this->addFormError('email', 'invalid_email');
                $bValid = false;
            }
            
            if(empty($aPost['password'])){
                $this->addFormError('password', 'empty_password');
                $bValid = false;
            }
            
            return $bValid;
        }
        
        public function onSuccess($aPost, $aGet) {
            $bRemember = empty($aPost['remember']) ? false : true;
            
            if($this->oAuth->login($aPost['email'], $aPost['password'], $bRemember)){
                /*
                 * TODO:
                 * redirect to RURL
                 */
                $this->redirect(BASE_URL);
                exit;
            }
            else{
                $this->addFormError('form', 'login_failed');
                $this->onFailure();
            }
        }
    }
?>
