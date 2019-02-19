<?php
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../init.php';

# ToDo: remove this testing stuff before actual deployment!
if (isset($_GET['testing'])) {
    $_SESSION['username'] = "foo";
    $_SESSION['id'] = 41;
    $_SESSION['loggedIn'] = true;
}

//check if user is logged in, if not then redirect them to the login page; GET string is only used for testing purposes
# ToDo: remove the GET string from this test before actual use
if ((!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] != true) && !isset($_GET['testing'])) {
    # ToDo: implement the anon display page here
} else {
    # ToDo: implement the logged in display here
}


//user is logged in, get their username and info
//don't want any XSS so we put the variable through the htmlspecialchars() function
$user = '';
if(isset($_SESSION['username'])){
    $user = htmlspecialchars($_SESSION['username']);
} else {
    $user = 'Username not set...';
}

//set up utility links?
# ToDo: Ask Dave what these are
//$util_links = '<a href="/profile.php">Home</a>';

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
$page_params['site_url'] = 'https://bama-dev.informs.org/profile.php';
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
