<?php
	//set up a session to pass the results of the form processing to a different page
	session_start();
	
	//get the global DB object set up by conn.php
	require_once "conn.php";
	
	//process form data when the form is submitted
	# Dave said this part wasn't necessary since we KNOW that the request will be coming in via POST; Maybe we should keep it in in case we want to add functionality for when
	# a request comes in through other methods?
	# ToDo: Should we check for session variables in this script? Figure it out and put checks in if needed
	if($_SERVER["REQUEST_METHOD"] == "POST"){
		//validate the inputs and return an array of length 2 (successfull validation) or more (exact # depending on potential errors to be thrown; invalid data was passed somewhere)
		$results = validateInputs(trim($_POST["username"]), trim($_POST["password"]), trim($_POST["confirm_password"]));
		
		if(count($results) === 2){
			
			//the input is valid, check if username exists already or not
			if(checkUsernameExists($results[0]) == false){
				$userAccount = createUser($results[0], $results[1]);
				//put results of account creation in a session variable
				$_SESSION['userCreated'] = $userAccount;
				
				//redirect to login screen?
				# Depending on how users are added to the system, this would redirect differently
				# i.e. if users make their own accounts -> login page; if INFORMS admin makes accounts -> add user page? back to dashboard?
				//this will use a constant that was defined in conn.php
				header("Location: " . USERSDIR . "login.php");
				//It's good practice to kill a page after a redirect so that this script stops execution
				die();
			}
			else {
				//input was valid, but username already exists
				
				//put results of account creation in a session variable
				$_SESSION['userCreationErrors'] = "Username $results[0] already exists in system.";
				
				//redirect back to the register page
				header("Location: " . USERSDIR . "register.php");
				//It's good practice to kill a page after a redirect so that this script stops execution
				die();
			}
		}
		//if there are more than 2 things in the array, there was some invalid input and errors populate the array
		else if(count($results) > 2){
			//something in the user input was invalid
			
			//create array in a session variable to hold the errors
			$_SESSION['userCreationErrors'] = array();
			foreach($results as $err){
				if(!empty($err)){
					//echo $err;
					//add errors to the session variable
					array_push($_SESSION['userCreationErrors'], $err);
				}
			}
			
			//redirect back to the register page
			header("Location: " . USERSDIR . "register.php");
			//It's good practice to kill a page after a redirect so that this script stops execution
			die();
		}
		else {
			//unknown issue encountered where the results were not returned as expected
			$_SESSION['userCreationErrors'] = "<p class='text-danger'>Unkown results returned from validation function.</p>";
			
			//redirect back to the register page
			header("Location: " . USERSDIR . "register.php");
			//It's good practice to kill a page after a redirect so that this script stops execution
			die();
		}
	}
	
	/**
	 * Query the DB and check whether a proposed username is already taken
	 *
	 * @param string $username the proposed username
	 * @return true if query returns results, false username is not already in system
	 */
	function checkUsernameExists($username){
		//get the global DB object
		global $g_db;
		
		$stmt = $g_db->prepare("SELECT * FROM users WHERE Username = ?");
		//only 1 results, if any, should be returned
		$stmt->execute([$username]);
		
		# for some reason, even though the PHP documentation states that execute returns boolean AND I don't change the
		# stmt variable to the results of the execute() function, calling fetch() on $stmt always encountered a BS
		# fatal error of attempting to fetch on a boolean. I have changed the functionality around
		# so that the row count is used to check if a username actually exists because I couldn't figure out
		# why this was happening or how to fix it.
		$count = $stmt->rowCount();
		if($count == 1){
			return true;
		}
		else {
			return false;
		}
		# Below is the original (flawed) method of checking whether a user exists in the system.
		# It was flawed because it would always return true, even if the username wasn't in the system.
		# I think this is because the SELECT statement was executed successfully, even though no results were returned.
		/*
		$foo = $stmt->fetch();
		
		if($foo){
			//username is already in use
			return true;
		}
		else {
			//username is not currently in use
			return false;
		}
		*/
	}

	/**
	 * Create a new user record in the DB
	 *
	 * @param string $user the username for the new account
	 * @param string $pass the password for the new account
	 * @return string HTML containing results of executing the insertion query
	 */
	function createUser($user, $pass){
		//will need to access the global DB object
		global $g_db;
		
		//prepare the statement to execute, since we will be passing inputs
		$stmt = $g_db->prepare("INSERT INTO users (Username, Password) VALUES (?, ?)");
		//execute the statement, pass in the username and a hashed password 
		$stmt->execute([$user, password_hash($pass, PASSWORD_DEFAULT)]);
		$count = $stmt->rowCount();
		
		//if the row count is greater than 0, it means the statement was executed successfully and the user was added to the DB
		# Technically, we are only expected 1 row to be added to the DB, so this COULD be if($count === 1). We could add in more error messages,
		# but I don't think that's necessary at this time.
		if($count == 1){
			//user successfully added to the DB
			return "User created successfully.";
			# ToDo: add in/set this as a session variable instead
		}
		else {
			//something went wrong
			return "Error inserting user in DB.";
			# ToDo: add in/set this as a session variable instead
		}
	}

	/**
	 * This function is called to ensure users enter valid inputs on this page
	 *
	 * This function will set global variables that are outside of it's scope, but are only used on this page.
	 *
	 * @author Dan Herold
	 * @param string $user the user's desired username
	 * @param string $pass the user's desired password
	 * @param string $confPass the user's confirm password field entry, to ensure the password is correct
	 * @return array of length 2 containing [username, password] if valid; if invalid, will return array of longer length depending on number of possible errors in input validation
	 */
	# maybe this can be updated to return an array containing the validated input? if input is invalid, how to tell? check first value of array? length of array?
	function validateInputs($user, $pass, $confPass){
		//will want to access the global DB object created in con.php in this local context; and also other globals
		global $g_db;

		//ensure that no previous errors or values are used in the validation process
		$username = $password = $confirm_password = "";
		$username_err = $password_err = $confirm_password_err = "";

		//validate inputs, starting with username
		if(empty($user)){
			//no user name was supplied
			$username_err = 'Please enter a username.';
		}
		else {
			//see if the username is already taken
			$sql = "SELECT UserId FROM users WHERE Username = ?";
			//prepare the statement
			$stmt = $g_db->prepare($sql);
			//execute the prepared statement with the proposed username that was passed in the POST call
			$stmt->execute($user);
			$userExists = $stmt->fetch();
			//if something was returned, then the username is already taken
			if($userExists){
				//need to have the global context so these values (or errors) get passed around all necessary functions
				$username_err = 'This username is already taken.';
				//return false;
			} else {
				$username = $user;
			}
		}
		
		//validate password
		if(empty($pass)){
			$password_err = 'Please enter a password.';
		}
		//password needs to be more than x characters
		else if(strlen($pass) < 6){
			$password_err = 'Password must have at least 6 characters.';
		}
		##check for symbols/other password requirements go here if needed
		//password is OK to go
		else {
			$password = $pass;
		}
		
		//validate confirm password
		if(empty($confPass)){
			$confirm_password_err = 'Please confirm password.';
		}
		else {
			//as long as there are no password errors set, and also that the password and confirm password values match, everything is valid
			if(empty($g_password_err) && ($pass != $confPass)){
				$confirm_password_err = 'Password did not match.';
			}
			else {
				$confirm_password = $confPass;
			}
		}
		
		//now we have to check the error variables and determine whether everything was valid or if there was an invalid input passed
		if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
			//no error messages set, return the inputs passed were valid
			return array($username, $password);
		}
		else {
			//something in the passed values was invalid
			return array($username_err, $password_err, $confirm_password_err);
		}
	}
?>