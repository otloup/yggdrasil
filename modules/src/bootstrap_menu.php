<?php
    class bootstrap_menuModule extends Module {
        private $oMenu = null;
        private $oAuth = null;
        private $aMenu = [];
        private $aLoggedData = [];
        
        public function onInit() {
            $this->oMenu = $this->getLib('menu');
            $this->oAuth = $this->getLib('auth');
            
            $this->aMenu = $this->oMenu->getOptions();
            $this->aLoggedData = $this->oAuth->getLoggedUserData();
        }
        
        public function setData(){
            $this->setTemplateData([
                'menu'  =>  $this->aMenu
                ,'loggedAs' =>  $this->aLoggedData['login']
                ,'pageLogout'    =>  PAGE_LOGOUT
            ]);
        }
    }