<?php
	renderer::getLib('globalUtil', 'static');

	$oDB = renderer::getLib('phpPdo');

	$iPage = empty($_POST['page']) ? 1 : intval($_POST['page']);
	$iLimit = empty($_POST['limit']) ? DEFAULT_PAGE_LIMIT : intval($_POST['limit']);
	$sAction = $oDB->escape($_POST['action']);

	switch($sAction){
		case 'getPage' :
			renderer::printModule('news', array(
				'page'	=>	$iPage
				,'limit'	=>	$iLimit
			));
		break;

		case 'getNewsCount' :
			$sFunc = 'getNewsCount';
		break;
	}

	if(!empty($sFunc)){
		echo globalUtil::forgeJSONResponse(call_user_func($sFunc, $iPage, $iLimit));
	}

	function getNewsCount(){
		$oDB = renderer::getLib('phpPdo');

		return array(
					'count'	=>	$oDB->execute('SELECT COUNT(id) FROM widget_news WHERE lang_code = "'.CURRENT_LANG.'"', phpPdo::RESULT_SINGLE)
				);
	}

?>
