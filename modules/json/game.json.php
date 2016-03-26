<?php

  $sAction = '';
  $aData = array();

  if(!empty($_POST['action'])){
    $sAction = $_POST['action'];
  }

  if(empty($sAction)){
    exit;
  }
  
  require_once(LIB_DIR.'game.php');
  
  switch($sAction){
    case 'getPlayer':
      
      $aData = Game::getPlayer($_POST['role']);
      
    break;
  }
  
  function returnData(){
    global $aData;
    
    print json_encode($aData);
    exit;
  }
?>