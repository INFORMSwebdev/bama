<?php
	//start the session
	session_start();
	
	//check if user is already logged in
	if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true){
		//redirect to ?
		# ToDo: update this when the program admin dashboard is created
		header("Location: /index.php");
		//don't want the script to keep executing after a redirect
		die;
	}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">

    <title>Program Administrator Login</title>
  </head>
  <body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="https://www.informs.org" target="_blank">
			<img src="/images/nav/logo_125x30.png" height="30" alt="INFORMS logo" />
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon" />
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav mr-auto">
				<li class="nav-item">
					<a class="nav-link" href="/index.php">Home</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="/users/register.php">Register Program Admin</a>
				</li>
			</ul>
		</div>
		<div class="navbar-nav">
			<a class="nav-item btn btn-sm btn-outline-danger" href="/users/logout.php" role="button">Log out</a>
		</div>
	</nav>
	<div class="container">
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
	</div>

    <!-- Optional JavaScript -->
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
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>