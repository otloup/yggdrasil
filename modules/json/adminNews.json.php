<?php
	renderer::getLib('globalUtil', 'static');
	
	if(
			(
			 (
				!empty($_POST['content']) 
			 	&& !empty($_POST['name'])
			 )
			 || !empty($_POST['id'])
			) 
			&& !empty($_POST['action'])
	){
		$oDB = renderer::getLib('phpPdo');

		$sName = empty($_POST['name']) ? '' : $oDB->escape($_POST['name']);
		$sContent = empty($_POST['content']) ? '' : $oDB->escape(rawurldecode($_POST['content']));
		$sAction = $_POST['action'];
		$iId = empty($_POST['id']) ? 0 : intval($_POST['id']);

		switch($sAction){
			case 'add':
				if($sName != '' && $sContent != ''){
					$sFunc = 'addNews';
				}
			break;

			case 'remove':
				if(!empty($iId)){
					$sFunc = 'remNews';
				}
			break;

			case 'update':
				if($sName != '' && $sContent != ''){
					$sFunc = 'updateNews';
				}
			break;
		}

		if(!empty($sFunc)){
			echo globalUtil::forgeJSONResponse(call_user_func($sFunc, $sName, $sContent, $iId));
		}
	}

	function addNews($sName, $sContent){
		$oDB = renderer::getLib('phpPdo');	

		$mStatus = $oDB->execute('INSERT INTO widget_news (title, content, lang_code, publication_time) VALUES ("'.$sName.'", "'.$sContent.'", "'.CURRENT_LANG.'", NOW())');

		return array(
					'status'	=>	$mStatus
				);
	}

	function remNews($sName, $sContent, $iId){
		if($iId <= 0 ){
			return false;
		}

		$oDB = renderer::getLib('phpPdo');
		$mStatus = $oDB->execute('DELETE FROM widget_news WHERE id = '.$iId);

		return array(
					'status'	=>	$mStatus
				);
	}

	function updateNews(){}

?>
