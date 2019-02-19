<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/8/2019
 * Time: 10:48 AM
 */

ini_set('display_errors',1);
error_reporting(E_ALL);
session_start();
$ini = parse_ini_file( "/common/settings/common.ini", TRUE );
$aes = $ini['analytics_education_settings'];
define( "ROOT_DIR", $aes['root_dir'] );
define( "HTML_DIR", ROOT_DIR . $aes['html_dir'] );
define( "LOG_DIR", ROOT_DIR . $aes['log_dir'] );
define( "USER_DIR", ROOT_DIR . $aes['user_dir'] );
define( "SCRIPTS_DIR", ROOT_DIR . $aes['scripts_dir'] );
define( "IMAGES_DIR", ROOT_DIR . $aes['images_dir'] );
define( "SETTINGS_DIR", ROOT_DIR . $aes['settings_dir'] );
define( "CLASSES_DIR", ROOT_DIR . $aes['classes_dir'] );
define( "WEB_ROOT", ROOT_DIR . $aes['web_root'] );
require_once( CLASSES_DIR . "autoload.php");