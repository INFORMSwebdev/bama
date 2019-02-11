<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/8/2019
 * Time: 10:14 AM
 */

function bama_autoload($classname) {

    $filename = "/common/classes/" . $classname . ".php";
    if (file_exists( $filename )) include_once($filename);
    else {
        $ini = parse_ini_file( "/common/settings/common.ini", TRUE );
        $filename = $ini['ecommerce_settings']['app_root'] . "/classes/" . $classname . ".php";
        include_once($filename);
    }
}

spl_autoload_register( 'bama_autoload' );