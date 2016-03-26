<?php

	class dic extends CoreLib {

                const DIC_ABIDE_STRICT = false;
            
		private static $oDB;
		private static $sDefaultLangCode = DEFAULT_LANGUAGE;
                private static $sDefaultLangTablesFormat = DEFAULT_LANG_TABLES_FORMAT;
                private static $sDefaultLangTableName = DEFAULT_LANG_TABLE_NAME;
                private static $sDefaultLangFilePath = DEFAULT_LANG_FILE_PATH;
                private static $sDefaultLangTableRequest = DEFAULT_LANG_DB_REQUEST;
                private static $aLangArray = [];

		private static function getDB(){
			if(empty($oDB)){
				self::$oDB = renderer::getLib('phpPdo');
			}
		}

		public static function get($sLineName, $sLangCode = '', $sLangTableFormat = '', $sLangTableName = ''){
			$sLangCode = empty($sLangCode) ? self::$sDefaultLangCode : $sLangCode;
			$sLangTableFormat = empty($sLangTableFormat) ? self::$sDefaultLangTablesFormat : $sLangTableFormat;
			$sLangTableName = empty($sLangTableName) ? self::$sDefaultLangTableName : $sLangTableName;

			switch($sLangTableFormat) {
				case 'static' :
					return self::getStaticLine($sLineName, $sLangCode, $sLangTableName);
				break;

				case 'dynamic' :
					return self::getDynamicLine($sLineName, $sLangCode, $sLangTableName);
				break;
			}
		}

		private static function getStaticLine($sLineName, $sLangCode, $sLangTableName){
			$sFilePath = self::getTableFile($sLangCode);

			if(empty(self::$aLangArray[$sLangCode][$sLineName])){
                            if(self::DIC_ABIDE_STRICT){
				return 'String You Are Requesting Has Not Been Found In <pre>static:'.$sFilePath.'</pre>';
                            }
                            else{
                                return $sLineName;
                            }
			}

			return self::$aLangArray[$sLangCode][$sLineName];
		}

		private static function getTableFile($sLangCode){
                    /*
                     * TODO:
                     * PUT THIS INTO SOME KIND OF GLOBAL VARIABLE OR MEMCACHE, FOR FRELL SAKE!!!
                     */
                        $sLangFilePath = LANG_DIR . $sLangCode . FILE_DIC_POSTFIX;
                    
                        /*
                         *TODO:
                         * should throw an error, when field is not available
                         * file itself should be initialized at startup and kept in memcache 
                         */
                        
			if(file_exists($sLangFilePath) 
                           && empty(self::$aLangArray[$sLangCode])){
                           //&& !in_array($sLangFilePath, get_included_files())){
				require($sLangFilePath);
                                self::$aLangArray[$sLangCode] = $aDicTable;
			}

			return $sLangFilePath;
		}

		private static function getDynamicLine($sLineName, $sLangCode, $sLangTableName){
//			$sRequest = sprintf(DEFAULT_LANG_DB_REQUEST, )
		}

	}

?>
