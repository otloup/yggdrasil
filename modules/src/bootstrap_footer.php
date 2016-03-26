<?php

    class bootstrap_footerModule extends Module {
        
        public function onInit() {
//            <!-- Bootstrap Core JavaScript -->
//    <script src="/js/bootstrap.min.js"></script>
            $this->loadJs('bootstrap.min', Page::ASSET_PLACEMENT_BOTTOM);

    //<!-- Metis Menu Plugin JavaScript -->
//    <script src="/js/plugins/metisMenu/metisMenu.min.js"></script>
$this->loadJs('plugins/metisMenu/metisMenu.min', Page::ASSET_PLACEMENT_BOTTOM);
    //<!-- Morris Charts JavaScript -->
//    <script src="/js/plugins/morris/raphael.min.js"></script>
$this->loadJs('plugins/morris/raphael.min', Page::ASSET_PLACEMENT_BOTTOM);
    //<script src="/js/plugins/morris/morris.min.js"></script>
$this->loadJs('plugins/morris/morris.min', Page::ASSET_PLACEMENT_BOTTOM);
    //<script src="/js/plugins/morris/morris-data.js"></script>
$this->loadJs('plugins/morris/morris-data', Page::ASSET_PLACEMENT_BOTTOM);

    //<!-- Custom Theme JavaScript -->
    //<script src="/js/sb-admin-2.js"></script>
$this->loadJs('sb-admin-2', Page::ASSET_PLACEMENT_BOTTOM);
        }
        
        public function setData() {}
    }
?>
