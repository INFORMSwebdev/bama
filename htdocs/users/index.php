<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/6/2019
 * Time: 3:45 PM
 */
//display all errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

//start the session
session_start();

//check if user is logged in
# ToDo: remove the GET string from this test before actual use
if ((!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true) && !isset($_GET['testing'])) {
    //redirect to login page so user can log in
    header("Location: users/index.php");
    //don't want the script to keep executing after a redirect
    die;
}
if (isset($_GET['testing'])) {
    $_SESSION['username'] = "foo";
    $_SESSION['id'] = 41;
    $_SESSION['loggedIn'] = true;
}

//get the settings for sites
//parse the ini file for all site settings
$ini = parse_ini_file("/common/settings/common.ini", TRUE);
//autoload common classes, we want that wrapperBama class!
require_once("/common/classes/autoload.php");

$user = htmlspecialchars($_SESSION['username']);

//

//set the page content to be displayed by the wrapper class
$content = <<<EOT
	<div class="jumbotron">
		<h1 class="display-4">Welcome $user!</h1>
		<p class="lead">Message can go here about system</p>
		<hr class="my-4" />
		<a class="btn btn-primary" href="#" role="button">View all programs</a>
		<a class="btn btn-primary" href="#" role="button">View my programs</a>
	</div>
EOT;

