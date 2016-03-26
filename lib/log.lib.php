<?php
	class log {

		private static $oDb = null;
		private static $sTargetLogDir = TMP_DIR;
		private static $sTargetLogFile = 'log';
		private static $sTargetLogTable = 'log';
		private static $bWriteToFile = true;
		private static $bWriteToDb = true;
		private static $sLogEntryFooter = 'END LOG ENTRY';

		const ERROR = 'error';
		const WARNING = 'warning';
		const INFO = 'info';

		//private methods

		private static function getDb(){
			if(empty(self::$oDb)){
				self::$oDb = renderer::getLib('phpPdo');
			}

			return self::$oDb;
		}

		private static function writeToFile($sContent, $sLogType, $sFile = '', $sLine = ''){
			if(self::$bWriteToFile){
				renderer::getLib('file', 'static');
				$sTime = date('Y/m/d H:i:s');
				$sContent = "type: $sLogType \t | \t file: $sFile \t | \t line: $sLine \t | \t date: $sTime \t | \r\n \t message: \r\n \t $sContent \r\n ".self::$sLogEntryFooter." \r\n";

				return (bool) new file(self::$sTargetLogDir.DIRECTORY_SEPARATOR.self::$sTargetLogFile, file::WRITE_END_CREATE, $sContent);
			}

			return true;
		}

		private static function writeToDb($sContent, $sLogType, $sFile = '', $sLine = ''){
			if(self::$bWriteToDb){
				self::getDb();

				$sSql = "
					INSERT INTO
						".self::$sTargetLogTable."
					SET
						type			=	'".$sLogType."'
						,file			=	'".$sFile."'
						,line			=	'".$sLine."'
						,date			=	NOW()
						,message	=	'".$sContent."'
				";

				return (bool) self::$oDb->execute($sSql);
			}

			return true;
		}

		//public methods

		public static function write($sLogMsg, $sFile = '', $sLine = '', $sLogType = self::INFO){
			if(in_array($sLogType, array(self::INFO,self::WARNING,self::ERROR))){
				return self::writeToFile($sLogMsg, $sLogType, $sFile, $sLine) & self::writeToDb($sLogMsg, $sLogType, $sFile, $sLine);
			}
			return false;
		}

	}

?>
