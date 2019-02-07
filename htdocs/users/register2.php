<?php
//todo Don't forget to update the production servers /common/settings/common.ini file's analytics_education section
# This page is for anonymous users to request access to the system
# so an INFORMS admin will be notified of the request and have to
# approve or deny it. If approved, the user will be added to the
# users table and the UserId will be assigned to the requested
# institutions' AdminId column

//display all errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

//initialize the session
session_start();

//parse the ini file for all site settings
$ini = parse_ini_file("/common/settings/common.ini", TRUE);

// ToDo: if needed, uncomment the line below to use the PDO wrapper object
//$db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings" );


//set up variables in case user actually needs them
$content = '';
$page_params = array();

//check if user is logged in and set the content displayed
# TODO: if a user IS logged in, we only want INFORMS admin to be able to add other requests? If so, we will have to
# TODO derp
# redirect logged in users to a different location or display something other than the register form.
// TODO help with todo stuff
if((isset($_SESSION['admin']) && $_SESSION['admin'] != TRUE) || isset($_GET['testing'])){
    //user is an INFORMS admin OR the system is being tested
    //autoload common classes
    require_once("/common/classes/autoload.php");
    $content = <<<EOT
<div class="row">
	<h1>Register a Program Administrator</h1>
</div>
<div class="row">
	<p>Please fill this form to create a Program Administrator account.</p>
	<p class="text-warning">Submitting this form will create a user record and assign it to the specified institution as the institution administrator account.</p>
</div>
<div class="row">
	<form action="{$ini['analytics_education_settings']['user_dir']}processRegisterForm.php" method="post">
		<div class="form-group">
			<label for="Username">Email Address</label>
			<input type="text" class="form-control" name="Username" id="UsernameInput" aria-describedby="userNameHelp" placeholder="Email address is the username." required>
			<small id="userNameHelp" class="form-text text-muted">This is a separate login from an INFORMS account.</small>
		</div>
		<div class="form-group">
			<label for="Password">Password</label>
			<input type="password" class="form-control" name="Password" id="PasswordInput" aria-describedby="passwordHelp" placeholder="Password" required>
			<small id="passwordHelp" class="form-text text-muted">Password must be at least 6 characters.</small>
		</div>
		<div class="form-group">
			<label for="confirmPasswordInput">Confirm Password</label>
			<input type="password" class="form-control" name="confirm_password" id="confirmPasswordInput" placeholder="Confirm password" required>
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary" value="Submit">Submit</button>
		</div>
	</form>
</div>
EOT;
# todo add fields
}
else if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true){
    //user is an institution admin who already has a user account
    header('Location: ' .  $ini['analytics_education_settings']['root_dir'] . 'index.php');
    die;
}
else{
    //user is anonymous
    //autoload common classes
    require_once("/common/classes/autoload.php");
    $content = <<<EOT
<div class="row">
	<h1>Request for Program Administrator</h1>
</div>
<div class="row">
	<p>Please fill this form to submit a request to become an Institution Administrator.</p>
	<p class="text-warning">Submitting this form will create a user record and assign it to the specified institution as the institution administrator account.</p>
</div>
<div class="row">
	<form action="{$ini['analytics_education_settings']['user_dir']}processRegisterForm.php" method="post">
		<div class="form-group">
			<label for="Username">Email Address</label>
			<input type="text" class="form-control" name="Username" id="UsernameInput" aria-describedby="userNameHelp" placeholder="Email address is the username." required>
			<small id="userNameHelp" class="form-text text-muted">This is a separate login from an INFORMS account.</small>
		</div>
		<div class="form-group">
			<label for="FirstName">First Name</label>
			<input type="text" class="form-control" name="FirstName" id="FirstNameInput" aria-describedby="passwordHelp" placeholder="Password" required>
		</div>
		<div class="form-group">
			<label for="LastName">Last Name</label>
			<input type="text" class="form-control" name="LastName" id="LastNameInput" placeholder="Confirm password" required>
		</div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary" value="Submit">Submit</button>
		</div>
	</form>
</div>
EOT;
# TODO switch up the fields in the anonymous section to indicate they will be requesting access and to give the INFORMS admin info to decide on whether
 # or not to approve the admin request
}
//todo: change POST variable names to sync w/ the updated fields for this page
