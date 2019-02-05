<?php
# my first attempt at changing my code to fit with Dave's style!

//display all errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

//initialize the session
session_start();

//get the settings for sites
//parse the ini file for all sites
$ini = parse_ini_file("/common/settings/common.ini", TRUE);
//autoload common classes, we want that wrapper3 class!
require_once("/common/classes/autoload.php");

# ToDo: figure out how to get the PDO wrapper class in here or at least how to use the settings that were just parsed
//I think it's done as below, but I'm unsure at this time
$host = $ini['analytics_education_settings']['db_hostname'];
//so I would pass the variables as above to the PDO wrapper class, OR in the meantime, I can just use them to make a new PDO object
//for now, I am just using the /scripts/conn.php file since it was already created

//check if user is logged in, if not then redirect them to the login page
if(!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] !== true){
	header("Location: users/login.php");
	//stop execution of this script after redirect
	die;
}

//user is logged in, get their username and info
//don't want any XSS so we put the variable through the htmlspecialchars() function
$user = htmlspecialchars($_SESSION['username']);

//set up utility links?
# ToDo: Ask Dave what these are
$util_links = '<a href="/index.php">Home</a>';

$content = <<<EOT
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<a class="navbar-brand" href="https://www.informs.org" target="_blank">
				<img src="/images/nav/logo_125x30.png" height="30" alt="INFORMS logo" />
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon" />
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<div class="navbar-nav">
					<a class="nav-item nav-link active" href="/index.php">Home <span class="sr-only">(current)</span></a>
					<a class="nav-item nav-link" href="/users/register.php">Register Program Admin</a>
				</div>
				<!--<ul class="navbar-nav mr-auto">
					<li class="nav-item active">
						<a class="nav-link" href="/index.php">Home <span class="sr-only">(current)</span></a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="/users/register.php">Register Program Admin</a>
					</li>
				</ul>-->
			</div>
			<div class="navbar-nav">
				<a class="nav-item btn btn-sm btn-outline-danger" href="/users/logout.php" role="button">Log out</a>
			</div>
		</nav>
		<div class="container">
			<div class="jumbotron">
				<h1 class="display-4">Welcome $user!</h1>
				<p class="lead">Message can go here about system</p>
				<hr class="my-4" />
				<a class="btn btn-primary" href="#" role="button">View all programs</a>
				<a class="btn btn-primary" href="#" role="button">View my programs</a>
			</div>
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
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapper3($page_params);
//display the content
$wrapper->html();
?>
