<?php

	$sAction = $_POST['action'];
	
	$aReturnData = array();

	switch($sAction){
		case 'logout':
			require_once(LIB_DIR.'user.php');
			$oUser = new user();
			
			$aReturnData['status'] = $oUser->logout();
			$aReturnData['next'] = $aReturnData['status'] ? BASE_URL : null;
		break;
	}

passResults($aReturnData);

function passResults($aResults){
    print json_encode($aResults);
    exit;
}

?>
