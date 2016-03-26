<?php

$aAdditionalJS = [];
$aAdditionalCSS = [];

$bEditable = !!CONTENT_EDITABLE;

switch($aModuleConf['params']['section']) {
/*	case 'about':
	case 'services':
	case 'contact':
		
	break;
 */
	case 'index':
		$aAdditionalJS[] = 'slider.js';
	break;

	case 'news':
		$aAdditionalJS[] = 'news.js';
		$aAdditionalJS[] = 'navi2.js';

		if($bEditable){
			$aAdditionalJS[] = 'newsAdmin.js';
		}
	break;
}

if($bEditable){
	$aAdditionalJS[] = 'debug.js';
	$aAdditionalJS[] = 'tinymce/tinymce.min.js';
	$aAdditionalJS[] = 'editor.js';

	$aAdditionalCSS[] = 'edition.css';
}

?>
