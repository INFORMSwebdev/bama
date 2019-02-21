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
    //set up a message to display on the login page
    $_SESSION['logoutMessage'] = 'Please log in to view your profile page.';
    //redirect to login page so user can log in
    header('Location: login.php');
    //don't want the script to keep executing after a redirect
    die;
}
if (isset($_GET['testing'])) {
    $_SESSION['loggedIn'] = 41;
}

$user = new User($_SESSION['loggedIn']);
$userName = $user->Attributes['Username'];
$firstName = $user->Attributes['FirstName'];
$lastName = $user->Attributes['LastName'];
$joinDate = $user->Attributes['CreateDate'];
$comments = $user->Attributes['Comments'];
$insts = $user->getInstitutionAssignments();
$instList = '<ul class="list-group list-group-horizontal">';
foreach($insts as $ins){
    $helper = new Institution($ins);
    $instList .= "<li class='list-group-item'><a class='btn btn-info' href='../institutions/display?id=$ins' role='button'>{$helper->Attributes['InstitutionName']}</a></li>";
}
$instList .= '</ul>';

//set the page content to be displayed by the wrapper class
$content = <<<EOT
<div class="jumbotron">
	<h1 class="display-4">Welcome, $firstName!</h1>
	<p class="lead">My Profile Info</p>
	<p class=""
	<hr class="my-4" />
	<div class="row">
       	<form action="">
       		<div class="form-group">
       			<label for="Username">Email Address/Username</label>
       			<input type="text" class="form-control" name="Username" value="{$userName}" id="Username" aria-describedby="UserNameHelp" placeholder="Email address is the username." readonly />
       		</div>
       		<div class="form-group">
       			<label for="FirstName">First Name</label>
       			<input type="text" class="form-control" name="FirstName" value="{$firstName}" id="FirstName" placeholder="First Name" readonly />
       		</div>
       		<div class="form-group">
       			<label for="LastName">Last Name</label>
       			<input type="text" class="form-control" name="LastName" value="{$lastName}" id="LastName" placeholder="Last Name" readonly />
       		</div>
       		<div class="form-group">
       		    <label for="Institution">Administrator of Institution</label>
       		    $instList
            </div>
       	</form>
    </div>
    <hr class="my-4" />
    <a class="btn btn-primary" href="editProfile.php" role="button">Edit My Info</a>
</div>
EOT;

//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = "My Profile";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
//$page_params['active_menu_item'] = 'register';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();