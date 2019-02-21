<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/20/2019
 * Time: 2:46 PM
 */
//include the init file
require_once '../../init.php';

if ((!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true)){
    //user is not logged in
    //set up a message to display on the login page
    $_SESSION['logoutMessage'] = 'Please log in to edit your profile information.';
    //redirect to login page so user can log in
    header('Location: login.php');
    //don't want the script to keep executing after a redirect
    die;
}

//get the users info
$user = new User($_SESSION['loggedIn']);
$userName = $user->Attributes['Username'];
$firstName = $user->Attributes['FirstName'];
$lastName = $user->Attributes['LastName'];
$comments = $user->Attributes['Comments'];
$inst = new Institution();
$instName = $inst->Attributes['InstitutionName'];

//set up the form with user info for display only (i.e. with readonly attribute)
$content = <<<EOT
<div class="row">
	<p>My Profile Information</p>
</div>
<div class="row">
	<form action="">
		<div class="form-group">
			<label for="Username">Email Address/Username</label>
			<input type="text" class="form-control" name="Username" value="{$userName}" id="Username" aria-describedby="UserNameHelp" placeholder="Email address is the username." required />
		</div>
		<div class="form-group">
			<label for="FirstName">First Name</label>
			<input type="text" class="form-control" name="FirstName" value="{$firstName}" id="FirstName" placeholder="First Name" required />
		</div>
		<div class="form-group">
			<label for="LastName">Last Name</label>
			<input type="text" class="form-control" name="LastName" value="{$lastName}" id="LastName" placeholder="Last Name" required />
		</div>
		<div class="form-group">
		    <label for="Institution">Administrator of Institution</label>
		    <!-- ToDo: put a link to view the institution info page in href when it is created -->
		     <!-- also, figure out how to display this info in a good way, the profile page uses a button -->
		    <a href="#" id="Institution">$instName</a>
            <small id="InstitutionHelp" class="form-text text-muted">Select the institution that you wish to be an administrator for.</small>
            <small id="InstitutionOther" class="form-text text-warning">If you do not see your institution in the list, please select the 'Other' option and specify your institution in the Justification box below.</small>
        </div>
		<div class="form-group">
		    <input type="hidden" id="Comments" name="Comments" value="{$comments}" />
			<button type="submit" class="btn btn-primary" value="Submit">Submit</button>
		</div>
	</form>
</div>
EOT;

//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Profile";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
$page_params['active_menu_item'] = 'users';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();