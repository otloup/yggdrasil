<?php

	class recaptcha{

		public static function get(){
			require_once(LIB_DIR.'recaptcha_base.php');
			return recaptcha_get_html(RECAPTCHA_PUBLIC_KEY);
		}

		public static function validate($sResponse = null, $sChallenge = null){
			require_once(LIB_DIR.'recaptcha_base.php');

			$sResponse = empty($sResponse) ? @$_POST['recaptcha_response_field'] : $sResponse;
			$sChallenge = empty($sChallenge) ? @$_POST['recaptcha_challenge_field'] : $sChallenge;

			if(!empty($sResponse)){
				$resp = recaptcha_check_answer (RECAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $sChallenge, $sResponse);
				return $resp->is_valid ? true : false;	
			}

			return false;
		}

	}

?>
