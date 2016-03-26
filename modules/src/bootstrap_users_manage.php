<?php

    class bootstrap_users_manageModule extends FormModule {
        private $oAuth = null;
        private $oDic = null;
        private $oUtil = null;
        private $oUsers = null;
        
        public function onInit() {
            $this->oAuth = $this->getLib('auth');
            $this->oDic = $this->getLib('dic');
            $this->oUtil = $this->getLib('globalUtil');
            $this->oUsers = $this->getLib('users');
        }
        
        public function setData() {
            $aUsers = $this->oUsers->getAll();
            
            $this->setTemplateData([
                'users' =>  $aUsers
                ,'pageEditUser'  =>  PAGE_EDIT_USER
                ,'pageAddUser'  =>  PAGE_ADD_USER
            ]);
        }
        
        public function onFailure() {
            
        }
        
        public function onValidate($aPost, $aGet) {
        }
        
        public function onSuccess($aPost, $aGet) {
        }
    }
?>
