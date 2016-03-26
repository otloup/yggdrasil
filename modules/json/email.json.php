<?php

	renderer::getLib('globalUtil');
	renderer::getLib('dic');

	$oRecaptcha = renderer::getLib('recaptcha');

	function addError($sName, $bFlag, $sType = '', $sMsg = ''){
		globalUtil::addError(__FILE__, $sName, $bFlag, $sType, $sMsg);	
	}

	function checkError($sName){
		globalUtil::checkError(__FILE__, $sName);
	}

	function getErrors(){
		return globalUtil::getErrors(__FILE__);
	}

	function checkMailingAvailability($sEmailAddress){
		$oDB = renderer::getLib('phpPdo');
		$sEmailAddress = $oDB->escape($sEmailAddress);

		$sTimestamp = $oDB->execute('SELECT UNIX_TIMESTAMP(timestamp) FROM widget_contact WHERE mail = "'.$sEmailAddress.'"', phpPdo::RESULT_SINGLE);

		return !!((time() - intval($sTimestamp)) > CONTACT_MINIMUM_MAILING_INTERVAL);
	}

//	addError('name', false, 'empty', 'this field cannot be empty');
//	addError('email', false, 'empty', 'this field cannot be empty');
//	addError('subject', false, 'empty', 'this field cannot be empty');
//	addError('content', false, 'empty', 'this field cannot be empty');
//	addError('recaptcha', false, 'empty', 'this field cannot be empty');
//	addError('mail', false, 'not_send', 'your mail message was not sent');
//	addError('mail', false, 'unavailable', 'currently you are unable to send any more emails');

	if(!empty($_POST['form'])){
		parse_str($_POST['form'], $aPost);
		//array_walk($aPost, create_function('&$val', '$val = urldecode($val);'));

		//print_r($aPost);

//		$bNameValidity = empty($aPost['name']) ? false : true;
		$bEmailValidity = empty($aPost['email']) ? false : true;
		$bSubjectValidity = empty($aPost['subject']) ? false : true;
		$bContentValidity = empty($aPost['content']) ? false : true;
		$bRecaptchaValidity = $oRecaptcha->validate($aPost['recaptcha_response_field'], $aPost['recaptcha_challenge_field']);
		$bMailValidity = false;

//		addError('name', $bNameValidity);
		($bEmailValidity ? addError('email', $bEmailValidity) : addError('email', false, 'empty', 'this field cannot be empty'));
		($bSubjectValidity ? addError('subject', $bSubjectValidity) : addError('subject', false, 'empty', 'this field cannot be empty'));
		($bContentValidity ? addError('content', $bContentValidity) : addError('content', false, 'empty', 'this field cannot be empty'));
		($bRecaptchaValidity ? addError('recaptcha', $bRecaptchaValidity) : addError('recaptcha', $bRecaptchaValidity, 'invalid', 'code is invalid'));

		if(!empty($aPost['email'])){
			$bMailingAvailability = checkMailingAvailability($aPost['email']);
			addError('mail', $bMailingAvailability, 'unavailable', 'currently you are unable to send any more emails');
		}

		if(/*$bNameValidity && */$bSubjectValidity && $bContentValidity && $bRecaptchaValidity && $bMailingAvailability){
			$oMail = renderer::getLib('mail');

			$bMailValidity = $oMail->sendContactMail($aPost['name'], $aPost['email'], $aPost['subject'], $aPost['content']);

			if($bMailValidity){
				$sMailConfirmation = 'Thank You For Your Message! We Will Respond as Soon as Possible!';
			}
			else{
				$sMailConfirmation = 'Unfortunatelly, something went wrong. Please, try again later';
			}

			addError('mail', $bMailValidity, 'delivery', $sMailConfirmation);
		}
	}

	echo globalUtil::forgeJSONResponse(
			getErrors()
		);

?>
