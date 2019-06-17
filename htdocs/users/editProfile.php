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

$subject = 'Analytics%20and%20O.R.%20Education%20Database%20-%20Institution%20Detail%20Update%20Request';
$contentHelp = "<p class='lead'>If you want to change the institution you administrate, please email the request to <a href='mailto:educationresources@informs.org?subject=$subject'>educationresources@informs.org</a> using the provided subject line.</p>";

//get the users info
$user = new User($_SESSION['loggedIn']);
$userName = $user->Attributes['Username'];
$firstName = $user->Attributes['FirstName'];
$lastName = $user->Attributes['LastName'];
$comments = $user->Attributes['Comments'];
$inst = new Institution();
$instName = $inst->Attributes['InstitutionName'];
$insts = $user->getInstitutionAssignments();
$instList = '<ul class="list-group list-group-horizontal">';
foreach($insts as $ins){
    $helper = new Institution($ins);
    $instList .= "<li class='list-group-item'><a class='' href='../institutions/display.php?id=$ins' role='button'>{$helper->Attributes['InstitutionName']}</a></li>";
}
$instList .= '</ul>';

//set up the form with user info for display only (i.e. with readonly attribute)
$content = <<<EOT
<div class="flex-column">
	<h2>My Profile Information</h2>
	<p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="contianer-fluid">
	<form action="../scripts/processProfileEditForm.php" method="POST">
		<div class="form-group">
			<label for="Username">Email Address/Username</label><span class="text text-danger">*</span>
			<input type="text" class="form-control" name="Username" value="{$userName}" id="Username" aria-describedby="UserNameHelp" placeholder="Email address is the username." required />
		</div>
		<div class="form-group">
			<label for="FirstName">First Name</label><span class="text text-danger">*</span>
			<input type="text" class="form-control" name="FirstName" value="{$firstName}" id="FirstName" placeholder="First Name" required />
		</div>
		<div class="form-group">
			<label for="LastName">Last Name</label><span class="text text-danger">*</span>
			<input type="text" class="form-control" name="LastName" value="{$lastName}" id="LastName" placeholder="Last Name" required />
		</div>
		<div class="form-group">
		    <input type="hidden" id="userId" name="userId" value="{$user->id}" />
			<button type="submit" class="btn btn-primary" value="Submit">Submit</button>
			<!--<button class="btn btn-danger" type="submit" id="delete" name="delete" value="delete">Delete My Account</button>-->
		</div>
	</form>
</div>
<div class="flex-column">
    <h3>Administrator of Institution</h3>
    {$instList}
    {$contentHelp}
</div>
EOT;

$customJS = <<<EOT
$(function() {
    $('#delete').on( "click", function(e) {
        var conf = confirm( "Are you sure you want to delete your account?" );
        if( conf ){
            //actually delete the account
        }
        //otherwise do nothing
    });
});
EOT;


//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Profile";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();