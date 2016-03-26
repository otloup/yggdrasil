<?php


	class mysql {

		private $mMysqli;

		const RESULT_HASH = 'hash';
		const RESULT_ARRAY = 'array';
		const RESULT_ROW = 'row';
		const RESULT_SINGLE = 'single';
		const RESULT_OBJECT = 'object';
		const RESULT_PAGE = 'page';

		public function __construct(){
			if($this->checkCredentials()){
				if(!$this->connect()){
					die('couldn\'t connect');				
				}
			}
			else{
				die('wrong mysql credentials');
			}
		}

		//private methods

		private function checkCredentials(){
			if(
					DB != ''
					&& DB_HOST != ''
					&& DB_USER != ''
					&& DB_PASS != ''
				){
				return true;
			}

			return false;
		}

		private function connect(){
			if(!empty($GLOBALS['mysqli']) && is_a($GLOBALS['mysqli'], 'mysqli')){
				$this->mMysqli = $GLOBALS['mysqli'];
				return true;
			}

			if(empty($this->mMysqli) && !is_a($this->mMysqli,'mysqli')){
				$this->mMysqli = new mysqli(DB_HOST,DB_USER,DB_PASS,DB);
				
				if ($this->mMysqli->connect_error) {
			    die('Connect Error ('.$this->mMysqli->connect_errno.')'.$this->mMysqli->connect_error);
				}

				if (mysqli_connect_error()) {
			    die('Connect Error ('.mysqli_connect_errno().')'.mysqli_connect_error());
				}

				$GLOBALS['mysqli'] = $this->mMysqli;

				return true;
			}
		}

		private function getExecutionType($sQuery){
			$sQuery = strtolower(trim($sQuery));

			switch(true){
				case (stripos($sQuery,'select')===0):
					return 'getRecords';
				break;

				case (stripos($sQuery,'insert')===0):
				case (stripos($sQuery,'delete')===0):
				case (stripos($sQuery,'update')===0):
				default:
					return 'executeQuery';
				break;
			}
		}

		private function getRecords($sQuery,$sFormat = self::RESULT_HASH,$aConf = array()){

			$mReturn = false;
			$mRes = $this->mMysqli->query($sQuery);
                        
			if($mRes){
				$iNum = $mRes->num_rows;

				if($iNum>0){
					switch($sFormat){
						case self::RESULT_HASH:
							while ($row = $mRes->fetch_array(MYSQLI_ASSOC)) {
								$mReturn[] = $row;
							}
						break;

						case self::RESULT_PAGE:
							$mReturn = array();
							$mReturn['data'] = array();

							while ($row = $mRes->fetch_array(MYSQLI_ASSOC)) {
								$mReturn['data'][] = $row;
							}

							$mReturn['results'] = count($mReturn['data']);

							$sCountQuery = substr($sQuery, 0, strrpos(strtolower($sQuery), 'limit'));
							$mReturn['all_results'] = $this->mMysqli->query($sCountQuery);
							$mReturn['all_results'] = $this->mMysqli->affected_rows;
						break;

						case self::RESULT_ARRAY:
							while ($row = $mRes->fetch_array(MYSQLI_NUM)) {
								$mReturn[] = $row;
							}
						break;
	
						case self::RESULT_ROW:
							$mReturn = $mRes->fetch_array(MYSQLI_ASSOC);
						break;
	
						case self::RESULT_SINGLE:
							$mReturn = $mRes->fetch_row();
							$mReturn = $mReturn[0];
						break;
					}
				}

				$mRes->close();
			}
			return $mReturn;
		}

		private function executeQuery($sQuery,$aConf = array()){
			
			if(preg_match('#^(INSERT|insert) #', $sQuery)>0){
				if($this->mMysqli->query($sQuery)){
					return $this->mMysqli->insert_id;
				}
				return false;
			}
			else{
				return $this->mMysqli->query($sQuery);
			}
		}

		//public methods

		public function execute($sQuery, $sResultFormat = self::RESULT_HASH, $bDisplayQuery = false){
			if($bDisplayQuery){
				print $sQuery;
			}
			
			$mReturn = call_user_func(array($this,$this->getExecutionType($sQuery)),$sQuery, $sResultFormat);

			if($this->mMysqli->errno != 0){
				print $sQuery."\n\r";
				print "<h1>Error</h1>\n\r";
				print "<pre>".$this->mMysqli->error."</pre>\n\r";
				exit;
			}

			return $mReturn;
		}

		public function escape($sValue){
			$sValue = htmlspecialchars($sValue);
			return $this->mMysqli->real_escape_string($sValue);
		}

		public function __destruct(){
			if(empty($this->mMysqli) && !is_a($this->mMysqli,'mysqli')){
				$this->mMysqli->close();
			}

			if(empty($GLOBALS['mysqli']) && !is_a($GLOBALS['mysqli'],'mysqli')){
				$GLOBALS['mysqli']->close();
			}
		}

	}


?>
