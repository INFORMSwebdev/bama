<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/6/2019
 * Time: 3:45 PM
 */
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../../init.php';

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

# ToDo: update this to display specific user info from their profile (i.e. stuff in the DB)

$user = '';
if(isset($_SESSION['username'])){
    $user = htmlspecialchars($_SESSION['username']);
} else {
    $user = 'Username not set...';
}

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

//set page parameters up
$page_params['loggedIn'] = TRUE;
$page_params['content'] = $content;
$page_params['page_title'] = $page_title;
$page_params['site_title'] = "Analytics Education Admin";
$page_params['site_url'] = 'https://bama-dev.informs.org/index.php';
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
$page_params['active_menu_item'] = 'register';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();