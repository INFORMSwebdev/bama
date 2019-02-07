<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/6/2019
 * Time: 3:45 PM
 */
//start the session
session_start();

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true){
    //redirect to login page
    header("Location: users/index.php");
    //don't want the script to keep executing after a redirect
    die;
}

//get the settings for sites
//parse the ini file for all site settings
$ini = parse_ini_file("/common/settings/common.ini", TRUE);
//autoload common classes, we want that wrapperBama class!
require_once("/common/classes/autoload.php");