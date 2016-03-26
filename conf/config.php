<?php

date_default_timezone_set('Europe/Berlin');

define('VERSION', 'Yggdrasil 0.4.1-aneks');

$sHostname = empty(getenv('ENV_HOSTNAME')) ? trim(`hostname`) : getenv('ENV_HOSTNAME');

define('HOSTNAME', $sHostname);

$aServer = $_SERVER;
$sServerName = !empty($aServer['SERVER_NAME']) ? $aServer['SERVER_NAME'] : null;

require_once(getcwd().'/conf/server_conf_modifier.php');
require_once(getcwd().'/conf/project_config.php');

if (!empty($aServer['STAGE']) && !defined('STAGE')) {
    define('STAGE', strtoupper($aServer['STAGE']));
}

if (!empty($aServer['CONTENT_EDITABLE']) && !defined('CONTENT_EDITABLE')) {
    define('CONTENT_EDITABLE', !!$aServer['CONTENT_EDITABLE']);
}

/**
 * Page Access Levels
 */

define('PAGE_ACCESS_ALL', 0);
define('PAGE_ACCESS_LOGIN', PAGE_ACCESS_ALL | 1);
define('PAGE_ACCESS_ADMIN', PAGE_ACCESS_ALL | PAGE_ACCESS_LOGIN | 2);

#-----------

/**
 * Page Types
 */

define('PAGE_TYPE_BASIC', 'basic');
define('PAGE_TYPE_JSON', 'json');

#------------

define('APP_STAGE_DEVELOPEMENT', 'dev');
define('APP_STAGE_PRODUCTION', 'prod');

define('CREATE_IF_NONE', true);

define('URL_BASE', BASE_URL . '/%s');
define('HREF_BASE', RENDER_PREFIX . '/%s');

define('URL_CREATE_NEW_PAGE', sprintf(URL_BASE, 'new'));
define('HREF_CREATE_NEW_PAGE', sprintf(HREF_BASE, 'new'));

define('URL_CSS', '/css/');
define('URL_JS', '/js/');

$sBaseDir = substr(dirname(__FILE__), 0, strrpos(dirname(__FILE__), '/'));
define('BASE_DIR', $sBaseDir . '/');
define('CONF_DIR', dirname(__FILE__) . '/');


define('MODULES_DIR', BASE_DIR . 'modules/');
define('SRC_DIR', MODULES_DIR . 'src/');
define('TPL_DIR', MODULES_DIR . 'tpl/');
define('JSON_DIR', MODULES_DIR . 'json/');

define('WEBROOT_DIR', BASE_DIR . 'webroot/');
define('JS_DIR', WEBROOT_DIR . 'js/');
define('CSS_DIR', WEBROOT_DIR . 'css/');
define('IMG_DIR', WEBROOT_DIR . 'img/');

define('TMP_DIR', BASE_DIR . 'tmp/');
define('UPLOAD_DIR', TMP_DIR . 'upload/');

define('CORE_DIR', BASE_DIR . 'core/');
define('LIB_DIR', BASE_DIR . 'lib/');
define('CRON_DIR', BASE_DIR . 'cron/');
define('LANG_DIR', BASE_DIR . 'i18n/');
define('ENT_DIR', BASE_DIR . 'entities/');
define('EXC_DIR', BASE_DIR . 'exceptions/');

define('PAGES_DIR', BASE_DIR . 'pages/');

define('SOURCE_DIR', BASE_DIR . 'sources/');
define('INT_DIR', SOURCE_DIR . 'interfaces/');
define('PAR_DIR', SOURCE_DIR . 'parents/');

define('LAY_DIR', BASE_DIR . 'layouts/');

define('FILE_LIB_POSTFIX', '.lib.php');
define('FILE_ENTITY_POSTFIX', '.ent.php');
define('FILE_INTERFACE_POSTFIX', '.int.php');
define('FILE_PARENT_POSTFIX', '.par.php');
define('FILE_EXCEPTION_POSTFIX', '.exc.php');
define('FILE_PAGE_POSTFIX', '.page');
define('FILE_TPL_POSTFIX', '.html');
define('FILE_LAY_POSTFIX', '.html');
define('FILE_DIC_POSTFIX', '.dic.php');

define('FILE_JS_POSTFIX', '.js');
define('FILE_CSS_POSTFIX', '.css');

define('CLASS_PAGE_POSTFIX', 'Page');
define('CLASS_MODULE_POSTFIX', 'Module');

define('USER_TIMEOUT', (1 * 60 * 60 * 24)); //one day
define('USER_COOKIE_NAME', 'user');

define('SETUP_BUNDLE_TYPE_TEMPLATE', 'template');
define('SETUP_BUNDLE_TYPE_PAGE', 'page');

/**
 * plugins constants
 */

define('PLUGINS_DIR' ,BASE_DIR . 'plugins/');
define('PLUGINS_TMP_DIR' ,PLUGINS_DIR . 'tmp/');
define('PLUGINS_CONFIG_DIR' ,PLUGINS_DIR . 'config/');
#---------SMARTY 3----------
define('PLUGINS_SMARTY3_NAME' , 'smarty3');
define('PLUGINS_SMARTY3_DIR' ,PLUGINS_DIR . PLUGINS_SMARTY3_NAME . '/');
define('PLUGINS_SMARTY3_TMP_DIR' ,PLUGINS_TMP_DIR . PLUGINS_SMARTY3_NAME . '/');
define('PLUGINS_SMARTY3_TEMPLATE_DIR' ,PLUGINS_SMARTY3_TMP_DIR . 'templates/');
define('PLUGINS_SMARTY3_COMPILE_DIR' ,PLUGINS_SMARTY3_TMP_DIR . 'templates_c/');
define('PLUGINS_SMARTY3_CACHE_DIR' ,PLUGINS_SMARTY3_TMP_DIR . 'cache/');
define('PLUGINS_SMARTY3_CONFIG_DIR' ,PLUGINS_CONFIG_DIR . PLUGINS_SMARTY3_NAME . '/');

#---------------------------------------------

define('LANG_TABLES_FORMAT_STATIC', 'static');
define('LANG_TABLES_FORMAT_DYNAMIC', 'dynamic');
define('DEFAULT_LANG_TABLES_FORMAT', LANG_TABLES_FORMAT_STATIC);
define('LANG_TABLE_NAME_STATIC', '%s.dic.php');
define('LANG_TABLE_NAME_DYNAMIC', '%s_dic');
define('DEFAULT_LANG_TABLE_NAME', 'i18n');
define('DEFAULT_LANG_FILE_PATH', LANG_DIR . '%s');
define('DEFAULT_LANG_DB_REQUEST', "SELECT content FROM %s WHERE lang = '%s' AND name = '%s'");

?>
