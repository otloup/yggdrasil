<?php

define('BASE_URL', 'http://' . SERVER_NAME . '/' . RENDER_PREFIX);

$sRequest = $_SERVER['REQUEST_URI'];
define('CURRENT_URL', substr(BASE_URL, 0, -1) . $sRequest);

define('DEFAULT_PAGE', 'index');

define('SESSION_NAME', 'ragnacode');
define('SESSION_USER_DATA_NAME', 'user_data');
define('CONTACT_MINIMUM_MAILING_INTERVAL', 1 * 60 * 5); //5min

define('DEFAULT_LANGUAGE', 'pl_PL');

define('DEFAULT_PAGE_LIMIT', 5);

define('APP_VERSION', '2');
define('APP_NAME', 'Aneks Warehouse Management System');


define('SALT', md5(APP_NAME.'6894568966+998++2'));
define('DEFAULT_USER_STATUS', 1);
/**
 * Project Url's
 */

define('PAGE_LOGIN',BASE_URL.'login/');
define('PAGE_LOGOUT', BASE_URL.'logout/');
define('PAGE_ADD_NEW_TABLE',BASE_URL.'add/table/');
define('PAGE_VIEW_TABLES',BASE_URL.'view/tables/');
define('PAGE_VIEW_USERS',BASE_URL.'view/users/');
define('PAGE_MANAGE_TABLES',BASE_URL.'manage/tables/');
define('PAGE_MANAGE_USERS',BASE_URL.'manage/users/');
define('PAGE_EDIT_USER',BASE_URL.'edit/user/?user=');
define('PAGE_EDIT_TABLE',BASE_URL.'edit/table/?table=');
define('PAGE_ADD_USER',BASE_URL.'add/user/');