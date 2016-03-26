<?php
    $mId = session_id();
    session_start();

    if(!empty($mId)){
        session_id($mId);
    }
    
    require_once(getcwd().'/conf/config.php');
    require_once(WEBROOT_DIR.'/controler.php');
?>
