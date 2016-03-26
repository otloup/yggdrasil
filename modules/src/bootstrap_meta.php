<?php

    class bootstrap_metaModule extends Module {
        
        public function onInit() {
            //    <!-- Bootstrap Core CSS -->
            $this->loadCss('bootstrap.min', Page::ASSET_PLACEMENT_TOP);
            //    <!-- MetisMenu CSS -->
            $this->loadCss('plugins/metisMenu/metisMenu.min', Page::ASSET_PLACEMENT_TOP);
            //    <!-- Timeline CSS -->
            $this->loadCss('plugins/timeline', Page::ASSET_PLACEMENT_TOP);
            //    <!-- Custom CSS -->
            $this->loadCss('sb-admin-2', Page::ASSET_PLACEMENT_TOP);
            //    <!-- Morris Charts CSS -->
            $this->loadCss('plugins/morris', Page::ASSET_PLACEMENT_TOP);
            //    <!-- Custom Fonts -->
            $this->loadCss('font-awesome-4.1.0/font-awesome.min', Page::ASSET_PLACEMENT_TOP);

            //    <!-- jQuery Version 1.11.0 -->
            $this->loadJs('jquery-1.11.0', Page::ASSET_PLACEMENT_TOP); 
        }
        
        public function setData() {}
    }
?>
