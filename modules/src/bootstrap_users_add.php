<?php

    class bootstrap_users_addModule extends FormModule {
        private $oDic = null;
        private $oUtil = null;
        private $oUsers = null;
        private $oUserPrivileges = null;
        
        public function onInit() {
            $this->oDic = $this->getLib('dic');
            $this->oUtil = $this->getLib('globalUtil');
            
            $this->oUsers = $this->getLib('users');
            $this->oUserPrivileges = $this->getLib('userPrivileges');
        }
        
        public function setData() {
            $aUsersPrivileges = $this->oUserPrivileges->getAll();
            
            $aSuccessMessages = $this->getSessionMessages(CoreLib::MESSAGE_TYPE_SUCCESS);
            $aErrorMessages = $this->getSessionMessages(CoreLib::MESSAGE_TYPE_ERROR);
            
            $this->setTemplateData([
                'privileges'    =>  $aUsersPrivileges
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
            
            if($this->oUsers->findByEmail($aPost['email'])){
                $this->addErrorMessage('podany adres email jest już zarejestrowany',  CoreLib::MESSAGE_PERSIST_RELOAD);
                $this->reload();
            }
            
            if($this->oUsers->addNewUser($aPost['email'], $aPost['password'], $aPost['privileges'], [
                'name'  =>  $aPost['name']
                ,'surname'   =>  $aPost['surname']
                ,'phone'    =>  $aPost['phone']
            ])){
                $this->addSuccessMessage('Użytkownik "'.$aPost['user_name'].'" został dodany', CoreLib::MESSAGE_PERSIST_RELOAD);
                $this->redirect(PAGE_MANAGE_USERS);
            }
            else{
                $this->addErrorMessage('Użytkownik "'.$aPost['user_name'].'" nie został dodany. Spróbuj ponownie później',  CoreLib::MESSAGE_PERSIST_RELOAD);
                $this->redirect(PAGE_MANAGE_USERS);
            }
        }
        
        public function onSuccess($aPost, $aGet) {
            /*
             * RC_TODO:
             * dependant from outcome from onValidate, onSuccess, or onFailure should be called automatically
             */
        }
    }

    ?>


