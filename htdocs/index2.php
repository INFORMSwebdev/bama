<?php
# my first attempt at changing my code to fit with Dave's style!

//display all errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

//initialize the session
session_start();

//get the settings for sites
//parse the ini file for all site settings
$ini = parse_ini_file("/common/settings/common.ini", TRUE);
//autoload common classes, we want that wrapperBama class!
require_once("/common/classes/autoload.php");

# ToDo: figure out how to get the PDO wrapper class in here or at least how to use the settings that were just parsed
//I think it's done as below, but I'm unsure at this time
$host = $ini['analytics_education_settings']['db_hostname'];
//so I would pass the variables as above to the PDO wrapper class, OR in the meantime, I can just use them to make a new PDO object
//for now, I am just using the /scripts/conn.php file since it was already created

//check if user is logged in, if not then redirect them to the login page; GET string is only used for testing purposes
# ToDo: remove the GET string from this test before actual use
if((!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true) && !isset($_GET['testing'])){
	header("Location: users/login.php");
	//stop execution of this script after redirect
	die;
}
if(isset($_GET['testing'])){
	$_SESSION['username'] = "foo";
}

//user is logged in, get their username and info
//don't want any XSS so we put the variable through the htmlspecialchars() function
$user = htmlspecialchars($_SESSION['username']);

//set up utility links?
# ToDo: Ask Dave what these are
//$util_links = '<a href="/index.php">Home</a>';

$content = <<<EOT
	<div class="jumbotron">
		<h1 class="display-4">Welcome $user!</h1>
		<p class="lead">Message can go here about system</p>
		<hr class="my-4" />
		<a class="btn btn-primary" href="#" role="button">View all programs</a>
		<a class="btn btn-primary" href="#" role="button">View my programs</a>
	</div>
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['loggedIn'] = TRUE;
$page_params['content'] = $content;
$page_params['page_title'] = "Program Administrator Dashboard";
$page_params['site_title'] = "Analytics Education Admin";
$page_params['site_url'] = 'https://bama-dev.informs.org/index.php';
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
$page_params['active_menu_item'] = 'home';
//$page_params['root_path'] = $ini['analytics_education_settings']['root_dir'];
//$page_params['users_path'] = $ini['analytics_education_settings']['user_dir'];
//$page_params['scripts_path'] = $ini['analytics_education_settings']['scripts_dir'];
//$page_params['images_path'] = $ini['analytics_education_settings']['images_dir'];
//$page_params['settings_path'] = $ini['analytics_education_settings']['settings_dir'];
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();
?>
