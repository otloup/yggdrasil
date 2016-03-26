<?php
if(CONTENT_EDITABLE != true){
	exit;
}

	renderer::getLib('globalUtil', 'static');
$oDB = renderer::getLib('phpPdo');
	if(!empty($_POST['content']) && !empty($_POST['name'])){
//data-name = [element]_[group_id]_[name]_[type] ie subpage__2_main_content
		list($sElement, $sGroupName, $iContentId, $sElementName, $sChangeType) = explode('_', $_POST['name']);
		$sContent = $_POST['content'];

		$sElement = $oDB->escape($sElement);
		$sGroupName = $oDB->escape($sGroupName);
		$iContentId = intval($iContentId);
		$sElementName = $oDB->escape($sElementName);
		$sChangeType = $oDB->escape($sChangeType);
		$sContent = $oDB->escape($sContent);

		switch($sElement){
			case 'slider':
				$sUpdateFunc = $sChangeType == 'header' ? 'updateSliderHeader' : 'updateSliderContent';
				echo globalUtil::forgeJSONResponse(call_user_func($sUpdateFunc, $sGroupName, $iContentId, $sElementName, $sContent));
			break;

			case 'subpage':
				$sUpdateFunc = $sChangeType == 'header' ? 'updateSubpageHeader' : 'updateSubpageContent';
				echo globalUtil::forgeJSONResponse(call_user_func($sUpdateFunc, $iContentId, $sElementName, $sContent));
			break;

			case 'news':
				$sUpdateFunc = $sChangeType == 'header' ? 'updateNewsHeader' : 'updateNewsContent';			
				echo globalUtil::forgeJSONResponse(call_user_func($sUpdateFunc, $iContentId, $sContent));
			break;

			default: 
				print 'no case found for element '.$sElement.' and type '.$sChangeType;
			break;
		}

	}

	function slideExists($sGroupName, $sName){
		$oDB = renderer::getLib('phpPdo');
		$iSlideId = $oDB->execute('SELECT id FROM widget_slider WHERE group_name = "'.$sGroupName.'" AND name = "'.$sName.'" AND lang_code = "'.CURRENT_LANG.'"', phpPdo::RESULT_SINGLE);

		return !!($iSlideId > 0);
	}

	function subpageExists($sName, $iContentId){
		$oDB = renderer::getLib('phpPdo');
		$iSubpageId = $oDB->execute('SELECT id FROM pages_content WHERE name = "'.$sName.'" AND id = '.$iContentId, phpPdo::RESULT_SINGLE);

		return !!($iSubpageId > 0);
	}

	function newsExists($iNewsId){
		$oDB = renderer::getLib('phpPdo');
		$iNewsId = $oDB->execute('SELECT id FROM widget_news WHERE id = '.$iNewsId.' AND lang_code = "'.CURRENT_LANG.'"', phpPdo::RESULT_SINGLE);

		return !!($iNewsId > 0);
	}

	function updateSliderHeader($sSlideGroup, $sSlideName, $sContent){
		$oDB = renderer::getLib('phpPdo');
		$mStatus = false;

		if(slideExists($sSlideGroup, $sSlideName)){
			$mStatus = $oDB->execute('UPDATE widget_slider SET header = "'.$sContent.'" WHERE group_name = "'.$sSlideGroup.'" AND name = "'.$sSlideName.'" AND lang_code = "'.CURRENT_LANG.'"');
		}
		else{
			$mStatus = 'No Slide Found With Name '.$sSlideName.' And Group '.$sSlideGroup;
		}

		return array(
				'status'=>$mStatus
			);
	}

	function updateSliderContent($sSlideGroup, $sSlideName, $sContent){
		$oDB = renderer::getLib('phpPdo');
		$mStatus = false;

		if(slideExists($sSlideGroup, $sSlideName)){
			$mStatus = $oDB->execute('UPDATE widget_slider SET content = "'.$sContent.'" WHERE group_name = "'.$sSlideGroup.'" AND name = "'.$sSlideName.'" AND lang_code = "'.CURRENT_LANG.'"');
		}
		else{
			$mStatus = 'No Slide Found With Name '.$sSlideName.' And Group '.$sSlideGroup;
		}

		return array(
				'status'=>$mStatus
			);
	}

	function updateSubpageHeader($iContentId, $sSubpageName, $sContent){
		$oDB = renderer::getLib('phpPdo');
		$mStatus = false;

		if(subpageExists($sSubpageName, $iContentId)){
			$mStatus = $oDB->execute('UPDATE pages_content SET header = "'.$sContent.'" WHERE id = '.$iContent);
		}
		else{
			if(CREATE_IF_NONE){
				$mStatus = addSubpage($sSubpageName, $sContent, 'header');
			}
			else{
				$mStatus = 'No Subpage Found With Name "'.$sSubpageName.'"';
			}
		}

		return array(
				'status'=>$mStatus
			);
	}

	function updateSubpageContent($iContentId, $sSubpageName, $sContent){
		$oDB = renderer::getLib('phpPdo');
		$mStatus = false;

		if(subpageExists($sSubpageName, $iContentId)){
			$mStatus = $oDB->execute('UPDATE pages_content SET content = "'.$sContent.'" WHERE id = '.$iContentId.' AND lang_code = "'.CURRENT_LANG.'"');
		}
		else{
			if(CREATE_IF_NONE){
				$mStatus = addSubpage($sSubpageName, $sContent, 'content');
			}
			else{
				$mStatus = 'No Subpage Found With Name "'.$sSubpageName.'"';
			}
		}

		return array(
				'status'=>$mStatus
			);
	}

	function addSlide(){}

	function addSubpage($sSubpageName, $sContent, $sType){
		$oDB = renderer::getLib('phpPdo');
		$mStatus = false;

		switch($sType){
			case 'header':
				$sSql = 'INSERT INTO pages_content (name, header, content, lang_code) values ("'.$sSubpageName.'", "'.$sContent.'", "(TEXT GOES HERE)", "'.CURRENT_LANG.'")';
			break;
			default:
				$sSql = 'INSERT INTO pages_content (name, content, lang_code) values ("'.$sSubpageName.'", "'.$sContent.'", "'.CURRENT_LANG.'")';
			break;
		}

		$mStatus = $oDB->execute($sSql);

		return array(
				'status'=>$mStatus
		);
	}

	function updateNewsHeader($iNewsId, $sContent){
		$oDB = renderer::getLib('phpPdo');
		$mStatus = false;

		if(newsExists($iNewsId)){
			$mStatus = $oDB->execute('UPDATE widget_news SET title = "'.$sContent.'" WHERE id = '.$iNewsId.' AND lang_code = "'.CURRENT_LANG.'"');
		}
		else{
			$mStatus = 'No News Found With Id "'.$iNewsId.'"';
		}

		return array(
				'status'=>$mStatus
			);
	}

	function updateNewsContent($iNewsId, $sContent){
		$oDB = renderer::getLib('phpPdo');
		$mStatus = false;

		if(newsExists($iNewsId)){
			$mStatus = $oDB->execute('UPDATE widget_news SET content = "'.$sContent.'" WHERE id = '.$iNewsId.' AND lang_code = "'.CURRENT_LANG.'"');
		}
		else{
			$mStatus = 'No News Found With Id "'.$iNewsId.'"';
		}

		return array(
				'status'=>$mStatus
			);
	}

?>
