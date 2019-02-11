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
        $filename = CLASSES_DIR . $classname . ".php";
        include_once($filename);
    }
}

spl_autoload_register( 'bama_autoload' );