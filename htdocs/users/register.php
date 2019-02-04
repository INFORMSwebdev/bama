<?php
	//start the session
	session_start();
	
	function processErrors(){
		//get the errors placed in the session variable from '/scripts/processRegisterForm.php'
		$errors = $_SESSION['userCreationErrors'];
		
		//set up the error container
		echo "<div class='container'>";
		echo "<div class='row'>";
		
		//loop through all messages in error list and output them with appropriate HTML tags
		foreach($errors as $err){
			echo "<p class='text-danger'>$err</p>";
		}
		
		//close the container tags
		echo "</div>";
		echo "</div>";
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

    <title>Program Administrator Registration</title>
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
				<li class="nav-item active">
					<a class="nav-link" href="/users/register.php">Register Program Admin <span class="sr-only">(current)</span></a>
				</li>
			</ul>
		</div>
		<div class="navbar-nav">
			<a class="nav-item btn btn-sm btn-outline-danger" href="/users/logout.php" role="button">Log out</a>
		</div>
	</nav>
  
	<?php 
		//if there are things in the session variable userCreationErrors, then input was already submitted to the processing script and it returned some error(s)
		# ToDO: figure out how to get this stuff into an ajax call?
		# I have been trying to figure out why the error messages are displayed in the same line even though the errors being output contain <p> tags around each message.
		# I.e. if you try to enter a password that is too short AND also the password/confirm fields do not match, the messages are displayed in red but they are not separate paragraphs
		# I have confirmed this happens in Chrome as well as Firefox
		# Dave and I have found that this is a bootstrap thing, we don't know why it's happening though.
		if(isset($_SESSION['userCreationErrors'])){
			processErrors(); 
			//unset session variables? how do we reset stuff with the form so that we clear out errors/usernames?
			# ToDo: determine if all variables be unset at this point, or should we just modify them and blank them out
			# ToDo: should this just reset the one session variable instead of all?
			session_unset();
		} 
	?>
		
	<div class="container">
		<div class="row">
			<h1>Register Program Admin Account</h1>
		</div>
		<div class="row">
			<p>Please fill this form to create a Program Administrator account.</p>
		</div>
		<div class="row">
			<form action="../scripts/processRegisterForm.php" method="post">
				<div class="form-group">
					<label for="userNameInput">Username</label>
					<input type="text" class="form-control" name="username" id="userNameInput" aria-describedby="userNameHelp" placeholder="Enter username" required>
					<small id="userNameHelp" class="form-text text-muted">This is a separate login from an INFORMS account.</small>
				</div>
				<div class="form-group">
					<label for="passwordInput">Password</label>
					<input type="password" class="form-control" name="password" id="passwordInput" aria-describedby="passwordHelp" placeholder="Password" required>
					<small id="passwordHelp" class="form-text text-muted">Password must be at least 6 characters.</small>
				</div>
				<div class="form-group">
					<label for="confirmPasswordInput">Confirm Password</label>
					<input type="password" class="form-control" name="confirm_password" id="confirmPasswordInput" placeholder="Confirm password" required>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-primary" value="Submit">Submit</button>
					<p>Already have an account? <a href="users/login.php">Login here</a>.</p>
				</div>
			</form>
		</div>
	</div>
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
  </body>
</html>