<?php

	class mail {
		private $oDB = null;
		private $sMailer = 'mini-drasil mailer';
		private $sReceiver = MAIN_EMAIL_ADDRESS;
		private $sSender = null;
		private $sEmail = null;
		private $sMimeBoundry = null;
		private $sContent = null;
		private $sHeaders = null;
		private $bMailStatus = true;

		public function __construct(){
			$this->oDB = renderer::getLib('phpPdo');
			renderer::getLib('log', 'static');
			renderer::getLib('globalUtil', 'static');
		}

		private function generateMimeBoundry(){
			$this->sMimeBoundry = '----'.$this->sMailer.'----'.md5(time());
		}

		private function checkParams($aMailParams){
			//RC_TODO: 
			// - regexp check of mail address
			// - check for subject length
			// - check for unwanted characters in subject
			// - check for blocked mails in db ?
			// - check for content length
			// - check for other stuff
			return true;
		}

		private function cleanMailParams($aMailParams){
			array_walk($aMailParams, create_function('&$item', '$item = globalUtil::clearString($item);'));
			return $aMailParams;
		}

		private function assignParams($aMailParams){
			$aMailParams = $this->cleanMailParams($aMailParams);
			
			if(!empty($aMailParams)){
				list($this->sReceiver, $this->sSender, $this->sEmail, $this->sSubject, $this->sContent) = $aMailParams;
				$this->generateMimeBoundry();
			}
		}

		private function prepareHeaders(){
			$this->sHeaders = "From: \"$this->sSender\" <".$this->sEmail."> \r\n";
			$this->sHeaders .= "Reply-To: ".$this->sEmail."\r\n";
			$this->sHeaders .= "MIME-Version: 1.0 \r\n";
			$this->sHeaders .= "Content-Type: text/html; charset=UTF-8 \r\n";
		}

		private function prepareMessage(){
/*			$this->sContent = <<<EOS
				--$this->sMimeBoundry
				Content-Type: text/html; charset=UTF-8
				Content-Transfer-Encoding: 8bit
				$this->sContent
				--$this->sMimeBoundry--
EOS;*/
		}

		private function setLogMessage(){
			$sLogMessageTemplate = "%s \n\r sender: \n\r \t ".$this->sSender." <".$this->sEmail."> \n\r receiver: \r\n \t ".$this->sReceiver." \n\r message: \n\r \t ".$this->sContent." \n\r";

			switch($this->bMailStatus){
				case true:
					$sLogType = log::INFO;
					$sMessage = 'The message has been sent';
				break;

				case false:
					$sLogType = log::ERROR;
					$sMessage = 'The message had not been sent';
				break;

				default:
					$sLogType = log::WARNING;
					$sMessage = 'I feel great disturbance in the force...';
				break;
			}

			log::write(sprintf($sLogMessageTemplate, $sMessage), __FILE__, __LINE__, $sLogType);

			$this->oDB->execute('INSERT INTO widget_contact SET mail = "'.$this->sEmail.'", name = "'.$this->sSender.'", subject = "'.$this->sSubject.'", content = "'.$this->sContent.'", timestamp = NOW()');
		}

		private function sendMail($sAddress, $sSender, $sEmail, $sSubject, $sContent){
			$aParams = func_get_args();
			$this->assignParams($aParams);

			$this->prepareMessage();

			if($this->bMailStatus){
				$this->prepareHeaders();
			}

			if($this->bMailStatus){
				$this->bMailStatus = mail($this->sReceiver, $this->sSubject, nl2br($this->sContent), $this->sHeaders);
				$this->setLogMessage();
			}

			return $this->bMailStatus;
		}

		public function sendContactMail($sSender, $sEmail, $sSubject, $sContent){
			$sAddress = CONTACT_FORM_EMAIL_ADDRESS;
			return $this->sendMail($sAddress, $sSender, $sEmail, $sSubject, $sContent);
		}
	}
?>
