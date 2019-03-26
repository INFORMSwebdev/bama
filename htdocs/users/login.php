<?php
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../../init.php';

//check if user is already logged in
if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true) {
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'You are already logged in. Please log out if you want to re-login.';
    //redirect to the regular index page
    header("Location: /index.php");
    //don't want the script to keep executing after a redirect
    die;
}

$errors = '';
$messages = '';
$prevUsername = '';
$form = '';

if(isset($_SESSION['logoutMessage'])){
    //user has just logged out, display the message passed from the logout script
    $messages = <<<EOT
<div class="alert alert-info" role="alert">
    <p>{$_SESSION['logoutMessage']}</p>
</div>
EOT;
    //we are done with the logout message, clear out the session variable
    $_SESSION['logoutMessage'] = null;
}

//check for errors resulting from other pages/scripts
if (isset($_SESSION['loginErrors'])) {
    //get the error messages out of the the session
    $userErr = $_SESSION['loginErrors']['usernameErrors'];
    $passErr = $_SESSION['loginErrors']['passwordErrors'];
    //we are done with that session variable, clear it out
    $_SESSION['loginErrors'] = null;

    //set the HTML to display if errors are present
    $userErrHTML = '';
    if(!empty($userErr)){
        $userErrHTML = "<p>Username: $userErr</p>";
    }
    $passErrHTML = '';
    if(!empty($passErr)){
        $passErrHTML = "<p>Password: $passErr</p>";
    }

    //set the HTML to display errors
    $errors = <<<EOT
<div class="alert alert-danger" role="alert">
    <h2>Errors:</h2>
    {$userErrHTML}
    {$passErrHTML}
</div>
EOT;
}
//can add more error checks here if necessary

//check to see if previous input was passed back
if(isset($_SESSION['loginInput'])) {
    //create the form w/ the value already filled in

    //get previous input to add to the form
    $prevUsername = htmlspecialchars($_SESSION['loginInput']['username']);
    //we are done with that session variable, clear it out
    $_SESSION['loginInput'] = null;

    //define the form HTML
    $form = <<<EOT
<div class="flex-column">
	<h1>Institution Administrator Login</h1>
</div>
<div class="flex-column">
	<p>Log in to administrate your program's information. Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<form class="needs-validation" action="../scripts/processLoginForm.php" method="post" novalidate id="login_form">
	<div class="form-group">
		<label for="validationUsername">Username</label><span class="text text-danger">*</span>
		<input type="text" class="form-control" id="validationUsername" name="username" placeholder="Username" value="{$prevUsername}" required />
		<div class="valid-feedback">
			Looks good!
		</div>
		<div class="invalid-feedback">
			Please enter your username.
		</div>
	</div>
	<div class="form-group">
		<label for="validationPassword">Password</label><span class="text text-danger">*</span>
		<input type="password" class="form-control" id="validationPassword" name="password" placeholder="Password" required />
		<div class="valid-feedback">
			Looks good!
		</div>
		<div class="invalid-feedback">
			Please enter your password.
		</div>
	</div>
	<div class="form-group">
	    <button class="btn btn-primary" type="submit">Log in</button>
	</div>
</form>
<p><a class="btn btn-info" href="/users/resetPassword.php">Forgot password?</a></p>
EOT;
}
else {
    //create an empty form
    $form = <<<EOT
<div class="flex-column">
	<h1>Institution Administrator Login</h1>
</div>
<div class="flex-column">
	<p>Log in to administrate your program's information.</p>
</div>
<div class="container-fluid">
    <form class="needs-validation" action="../scripts/processLoginForm.php" method="post" novalidate>
    	<div class="form-group">
    		<label for="validationUsername">Username</label><span class="text text-danger">*</span>
    		<input type="text" class="form-control" id="validationUsername" name="username" placeholder="Username" required />
    		<div class="valid-feedback">
    			Looks good!
    		</div>
    		<div class="invalid-feedback">
    			Please enter your username.
    		</div>
    	</div>
    	<div class="form-group">
    		<label for="validationPassword">Password</label><span class="text text-danger">*</span>
    		<input type="password" class="form-control" id="validationPassword" name="password" placeholder="Password" required />
    		<div class="valid-feedback">
    			Looks good!
    		</div>
    		<div class="invalid-feedback">
    			Please enter your password.
    		</div>
    	</div>
    	<div class="form-group">
    	    <button class="btn btn-primary" type="submit">Log in</button>
    	</div>
    </form>
</div>
<p><a class="btn btn-info" href="/users/resetPassword.php">Forgot password?</a></p>
EOT;
}

//combine the errors, any potential messages, and the form
//this assumes that a message and errors will not be output at the same time, so at least one of them (either $errors or $messages) will be an empty string
$content = $errors . $messages . $form;

//add custom javascript that disables form submission on required fields being empty
$customScript = <<<EOT
	// Example starter JavaScript for disabling form submissions if there are invalid fields; from Bootstrap 4 documentation
	(function() {
		'use strict';
		window.addEventListener('load', function() {
			// Fetch all the forms we want to apply custom Bootstrap validation styles to
			var forms = document.getElementsByClassName('needs-validation');
			// Loop over them and prevent submission
			var validation = Array.prototype.filter.call(forms, function(form) {
				form.addEventListener('submit', function(event) {
					if (form.checkValidity() == false) {
						event.preventDefault();
						event.stopPropagation();
					}
				form.classList.add('was-validated');
				}, false);
			});
		}, false);
	})();
EOT;
$page_params['js'][] = array('text' => $customScript);

//set other page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = 'Program Administrator Login';
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
//$page_params['active_menu_item'] = 'users';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();
