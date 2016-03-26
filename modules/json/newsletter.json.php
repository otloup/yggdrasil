<?php

	renderer::getLib('globalUtil');
	renderer::getLib('dic');
	$oDB = renderer::getLib('phpPdo');

	function addError($sName, $bFlag, $sType = '', $sMsg = ''){
		globalUtil::addError(__FILE__, $sName, $bFlag, $sType, $sMsg);	
	}

	function checkError($sName){
		globalUtil::checkError(__FILE__, $sName);
	}

	function getErrors(){
		return globalUtil::getErrors(__FILE__);
	}

	if(!empty($_POST['email'])){
		$bEmailValidity = globalUtil::validateEmail($_POST['email']) ? false : true;

		($bEmailValidity ? addError('email', $bEmailValidity) : addError('email', false, 'invalid', 'supplied string is not a valid email address'));

		if($bEmailValidity){
			$sEmailAddress = $oDB->escape($sEmailAddress);
			$oDB->execute('INSERT INTO widget_newsletter SET email = "'.$sEmailAddress.'"');
		}
	}

	echo globalUtil::forgeJSONResponse(
			getErrors()
		);

?>
