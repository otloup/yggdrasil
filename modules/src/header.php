<?php

    class headerModule extends Module {
        
        public function onInit() {
            print ' onInit called from module "header"';
        }
        
        public function setData() {
            $aTplData = array(
                'a'     =>  'b'
                ,'c'    =>  'd'
            );
            
            $this->setTemplateData($aTplData);
        }
    }
?>
