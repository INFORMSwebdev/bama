<?php
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../../init.php';
	
//check if user is already logged in
if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true){
    //redirect to the regular index page
    header("Location: /index.php");
    //don't want the script to keep executing after a redirect
    die;
}
# ToDo: Add in checks for session variables that contain error input from a previously submitted login page!!
$content = <<<EOT
<div class="row">
	<h1>Program Administrator Login</h1>
</div>
<div class="row">
	<p>Log in to administrate your program's information.</p>
</div>
<form class="needs-validation" action="../scripts/processLoginForm.php" method="post" novalidate>
	<div class="form-row">
		<label for="validationUsername">Username</label>
		<input type="text" class="form-control" id="validationUsername" name="username" placeholder="Username" required>
		<div class="valid-feedback">
			Looks good!
		</div>
		<div class="invalid-feedback">
			Please enter your username.
		</div>
	</div>
	<div class="form-row">
		<label for="validationPassword">Password</label>
		<input type="password" class="form-control" id="validationPassword" name="password" placeholder="Password" required>
		<div class="valid-feedback">
			Looks good!
		</div>
		<div class="invalid-feedback">
			Please enter your password.
		</div>
	</div>
	<button class="btn btn-primary" type="submit">Log in</button>
</form>
EOT;
$page_params['js']['text'] = <<<EOT
<script type="text/javascript">
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
</script>
EOT;

//set page parameters up
$page_params['loggedIn'] = TRUE;
$page_params['content'] = $content;
$page_params['page_title'] = 'Program Administrator Login';
$page_params['site_title'] = "Analytics Education Admin";
$page_params['site_url'] = 'https://bama-dev.informs.org/profile.php';
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