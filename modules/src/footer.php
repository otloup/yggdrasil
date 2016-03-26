<?php

$aAdditionalJS = [];

if(CONTENT_EDITABLE==true){
	$aAdditionalJS[] = 'editable()';
}

$sAdditionalJS = join(";\r\n", $aAdditionalJS);

?>
