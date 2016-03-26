<?php

require_once(PAR_DIR . 'CoreLib.par.php');

require_once(CORE_DIR . 'setup.php');

require_once(EXC_DIR . 'RenderException.exc.php');

require_once(CORE_DIR . 'request_parser.php');
$oRequest = new requestParser();

$aLang = array();

require_once(LANG_DIR . CURRENT_LANG . FILE_DIC_POSTFIX);

require_once(PLUGINS_DIR . 'PluginDriver.php');
require_once(ENT_DIR . 'EntityDriver.php');

require_once(CORE_DIR . 'renderer.php');

new renderer($oRequest);

//session_destroy();
?>
