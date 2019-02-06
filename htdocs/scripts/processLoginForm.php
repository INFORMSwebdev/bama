<?php
	//set up a session to pass the results of the form processing to a different page
	session_start();
	
	//check if user is already logged in (first checking if the session variable is set, then if it is true)
	if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] === true){
		//redirect user to their institution admin dashboard 
		# ToDo: update this location when the admin dashboard is created
		header("Location: ../index.php");
		//stop execution of this script after redirect
		die;
	}
	
	//get the global DB object set up by conn.php
	require_once "conn.php";
	
	//process the data when the form is submitted via POST
	if($_SERVER["REQUEST_METHOD"] == "POST"){		
		
		validateInput(trim($_POST["username"]), trim($_POST["password"]));
		
	}
	
	
	function validateInput($user, $pass){
		//get the PDO object in the global context to query the DB with
		global $g_db;
		
		//define variables that init to empty strings
		$username = $password = "";
		$username_err = $password_err = "";
		
		//validate username
		if(empty($user)){
			//empty username passed
			$username_err = "Please enter your username";
		}
		else {
			$username = $user;
		}
		
		//validate password
		if(empty($pass)){
			$password_err = "Please enter a password";
		}
		else {
			$password = $pass;
		}
		
		//validate credentials
		if(empty($username_err) && empty($password_err)){
			//no invalid input found, check the DB to make sure the entered username is in the system
			$stmt = $g_db->prepare("SELECT UserId, Password FROM users WHERE Username = ? AND Deleted = 0");
			$stmt->execute([$username]);
			
			//only 1 row should be returned
			if($stmt->rowCount() === 1){
				if($row = $stmt->fetch()){
					$id = $row['UserId'];
					$hashedPw = $row['Password'];
					
					//verify the password input with the stored hash value
					if(password_verify($password, $hashedPw)){
						//password is correct, start a new session
						session_start();
						
						//store user info in session variables
						$_SESSION["loggedIn"] = true;
						$_SESSION["id"] = $id;
						$_SESSION["username"] = $username;
						
						//redirect user to ?
						# ToDo: update this redirect when the program admin dashboard is created
						header("Location: ../index.php");
						//want to stop this script from executing after a redirect
						die;
					}
					else {
						//input password did not match the stored value
						$password_err = "The password entered was not valid.";
					}
				}
			}
			//the input username was not found
			else {
				//username not in system
				$username_err = "No account found with that username.";
			}
		}
		else {
			//there were errors in the user input
			$_SESSION['loginErrors'] = array($username_err, $password_err);
			# ToDo: figure out what to do now: redirect back to the login page? do nothing? I am not sure.
		}
	}
?>