<?php


	class pgsql {

		private $mPgsql;
		private $sConnectionUrl;

		const RESULT_HASH = 'hash';
		const RESULT_ARRAY = 'array';
		const RESULT_ROW = 'row';
		const RESULT_SINGLE = 'single';
		const RESULT_OBJECT = 'object';
		const RESULT_PAGE = 'page';

		public function __construct(){
			if($this->checkCredentials()){
				$this->constructConnectionUrl();
				if(!$this->connect()){
					die('couldn\'t connect');				
				}
			}
			else{
				die('wrong psql credentials');
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

		private function constructConnectionUrl(){
			$this->sConnectionUrl = "host=".DB_HOST." dbname=".DB." user=".DB_USER." password=".DB_PASS;
		}

		private function connect(){
				$this->mPgsql = pg_pconnect($this->sConnectionUrl);
				
				if (pg_connection_status($this->mPgsql) === PGSQL_CONNECTION_BAD) {
			    die('Connect Error '.pg_last_error());
				}

				return true;
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
			$mRes = pg_query($sQuery);
                        
			if($mRes){
				$iNum = pg_num_rows($mRes);

				if($iNum>0){
					switch($sFormat){
						case self::RESULT_HASH:
							while ($row = pg_fetch_assoc($mRes)) {
								$mReturn[] = $row;
							}
						break;

						case self::RESULT_PAGE:
							$mReturn = array();
							$mReturn['data'] = array();

							while ($row = pg_fetch_assoc($mRes)) {
								$mReturn['data'][] = $row;
							}

							$mReturn['results'] = count($mReturn['data']);

							$sCountQuery = substr($sQuery, 0, strrpos(strtolower($sQuery), 'limit'));
							$mReturn['all_results'] = pg_affected_rows($mRes);
						break;

						case self::RESULT_ARRAY:
							while ($row = pg_fetch_array($mRes)) {
								$mReturn[] = $row;
							}
						break;
	
						case self::RESULT_ROW:
							$mReturn = pg_fetch_assoc($mRes);
						break;
	
						case self::RESULT_SINGLE:
							$mReturn = pg_fetch_row($mRes);
							var_dump($mReturn);exit;
							$mReturn = $mReturn[0];
						break;
					}
				}

				pg_free_result($mRes);
			}
			return $mReturn;
		}

		private function executeQuery($sQuery,$aConf = array()){
			
			if(preg_match('#^(INSERT|insert) #', $sQuery)>0){
				$mRes = pg_query($sQuery);
				if($mRes){
					return pg_last_oid($mRes);
				}
				return false;
			}
			else{
				return pg_query($sQuery);
			}
		}

		//public methods

		public function execute($sQuery, $sResultFormat = self::RESULT_HASH, $bDisplayQuery = false){
			if($bDisplayQuery){
				print $sQuery;
			}
			
			$mReturn = call_user_func(array($this,$this->getExecutionType($sQuery)),$sQuery, $sResultFormat);
			$mError = pg_last_error();

			if($mError != null){
				print $sQuery."\n\r";
				print "<h1>Error</h1>\n\r";
				print "<pre>".$mError."</pre>\n\r";
				exit;
			}

			return $mReturn;
		}

		public function escape($sValue){
			$sValue = htmlspecialchars($sValue);
			return pg_escape_literal($sValue);
		}

		public function __destruct(){
			pg_close($this->mPgsql);
		}

	}


?>
