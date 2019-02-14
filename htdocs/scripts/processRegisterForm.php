<?php
//include the init.php file
require_once '../../init.php';

//ensure we are processing only on a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //check session variables to see what kind of register form was submitted
    // if admin set then it was a create; not set then it was an access request
    if ((isset($_SESSION['admin']) && $_SESSION['admin'] != TRUE) || isset($_GET['testing'])) {
        //INFORMS admin has set up a new user account, go ahead and make it and associate the new account as the admin of the specified institution
        # required inputs = Email (Username), Password, Confirm Password, Institution
        # optional inputs = Comments

        //check the validity of the admin input; if the input is valid
        $results = validateAdminInputs(trim($_POST['Username']), trim($_POST['Password']), trim($_POST['ConfirmPassword']), trim($_POST['Institution']));

        # ToDo: don't forget that we also want to pass the user input back to the form page so that the user will not
         # have to type things in again (excluding password fields)
        //if there were no errors, proceed with user account creation and link it to the institution specified
        if($results['errors'] == false){

        } else {
            //set up the error message and pass the input (not passwords tho) back to the form page so the INFORMS admin doesn't have to re-input everything
        }
    } else {
        //anonymous user has requested to become and institution admin
        # required inputs = Email (Username), First Name, Last Name, Institution
        # optional inputs = Justification (Comments)

        //check the validity of the anon input
        $results = validateAnonInputs(trim($_POST['Username']), trim($_POST['FirstName']), trim($_POST['LastName']), trim($_POST['Institution']));

        # ToDo: figure out how to send a notification and store the information input for use if admin request is approved
        //if there were no errors, proceed with notifying the INFORMS admin that a request to join has been submitted
        if($results['errors'] == false){

        } else {
            //set up the error message and pass the input back to the form page so the anon user doesn't have to re-input everything
        }
    }
}
//process form data when the form is submitted
# Dave said this part wasn't necessary since we KNOW that the request will be coming in via POST; Maybe we should keep it in in case we want to add functionality for when
# a request comes in through other methods?
# ToDo: Should we check for session variables in this script? Figure it out and put checks in if needed
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //validate the inputs and return an array of length 2 (successfull validation) or more (exact # depending on potential errors to be thrown; invalid data was passed somewhere)
    $results = validateAnonInputs(trim($_POST["username"]), trim($_POST["password"]), trim($_POST["confirm_password"]));

    if (count($results) == 2) {

        //the input is valid, check if username exists already or not
        if (checkUsernameExists($results[0]) == false) {
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
        } else {
            //input was valid, but username already exists

            //put results of account creation in a session variable
            $_SESSION['userCreationErrors'] = "Username $results[0] already exists in system.";

            //redirect back to the register page
            header("Location: " . USERSDIR . "register.php");
            //It's good practice to kill a page after a redirect so that this script stops execution
            die();
        }
    } //if there are more than 2 things in the array, there was some invalid input and errors populate the array
    else if (count($results) > 2) {
        //something in the user input was invalid

        //create array in a session variable to hold the errors
        $_SESSION['userCreationErrors'] = array();
        foreach ($results as $err) {
            if (!empty($err)) {
                //echo $err;
                //add errors to the session variable
                array_push($_SESSION['userCreationErrors'], $err);
            }
        }

        //redirect back to the register page
        header("Location: " . USERSDIR . "register.php");
        //It's good practice to kill a page after a redirect so that this script stops execution
        die();
    } else {
        //unknown issue encountered where the results were not returned as expected
        $_SESSION['userCreationErrors'] = "<p class='text-danger'>Unkown results returned from validation function.</p>";

        //redirect back to the register page
        header("Location: " . USERSDIR . "register.php");
        //It's good practice to kill a page after a redirect so that this script stops execution
        die();
    }
}

function processResults($formType, $results){
    if($formType == 'admin'){
        //admin form submission processing
        if($results['errors'] == true){
            //pass the errors to a session variable so they can be displayed on the register page
            $_SESSION['registerErrors'] = $results;
        } else {
            //no errors in inputs
            $_SESSION['registerValid'] = $results;
        }
    } else if ($formType == 'anon') {
        //anon form submission processing
        if($results['errors'] == true){
            //there were errors in the input passed
            //$_SESSION[''];
        } else {
            //no errors in inputs
        }
    } else {
        //unknown type submission encountered
    }
}


/**
 * Validate inputs that an INFORMS admin has entered
 *
 * @author Dan Herold
 * @param string $email The input from the email address (username) field
 * @param string $pass The input from the password field
 * @param string $confPass The input from the confirm password field
 * @param int $inst The InstitutionId that this user will be an admin of, from the institution select field
 * @return mixed[] The first item indicates if there were errors; Array full of error strings OR an array with the email address, password to use, and the institution the user will be an admin of.
 */
function validateAdminInputs($email, $pass, $confPass, $inst){
    //set up the variables to hold any possible errors that would result from the user input
    $username_err = $password_err = $confirmPassword_err = $institution_err = '';

    //validate the email address/username
    if(empty($email)){
        $username_err = 'No email address was supplied.';
    } else {
        //check if username is already taken
        if(User::checkUsernameExists($email)) {
            //username already exists
            $username_err = 'Email address already exists in the system, please use a different one.';
        }
    }

    //validate the password
    if(empty($pass)){
        $password_err = 'No password was supplied.';
    }
    # ToDo: put in more password validation checks to enforce password requirements

    //validate the confirm password
    if(empty($confPass)){
        $confirmPassword_err = 'No confirm password was supplied.';
    } else {
        //ensure the confirm password matches the password, but we don't want to do the check if there is an error with the password input
        if(empty($password_err) && ($pass != $confPass)){
            $confirmPassword_err = 'Password did not match confirm password.';
        }
    }

    //validate institution field has something selected
    if(empty($inst)){
        $institution_err = "No institution selected.";
    }
    # ToDo: put in more validation checks to see if the institution selected actually exists in the system

    //check to see if we have any errors in the inputs
    if(empty($username_err) && empty($password_err) && empty($confirmPassword_err) && empty($institution_err)){
        //inputs passed were valid
        # ToDo: figure out what should be returned here, if anything.
         # Keep in mind this function will be called from inside this script!
        return array('errors' => false, 'username' => $email, 'password' => $pass, 'institution' => $inst);
    }
    else {
        //inputs passed were not valid
        # ToDO: figure out the best way to get these error messages passed back to the script that called this script, see above todo
        return array('errors' => true, 'usernameError' => $username_err, 'passwordError' => $password_err, 'confirmError' => $confirmPassword_err, 'institutionError' => $institution_err);
    }
}

/**
 * This function is called to ensure anonymous users enter valid inputs on this page
 *
 * @author Dan Herold
 * @param string $user the user's desired username
 * @param string $pass the user's desired password
 * @param string $confPass the user's confirm password field entry, to ensure the password is correct
 * @return array of length 2 containing [username, password] if valid; if invalid, will return array of longer length depending on number of possible errors in input validation
 */
# ToDo: finish updating this function to work with the User class (as outlined above?)
function validateAnonInputs($user, $firstName, $lastName, $institution)
{

    //ensure that no previous errors or values are used in the validation process
    $username = $password = $confirm_password = "";
    $username_err = $password_err = $confirm_password_err = "";

    //validate inputs, starting with username
    if (empty($user)) {
        //no user name was supplied
        $username_err = 'Please enter a username.';
    } else {
        //see if the username is already taken
        if (User::checkUsernameExists($user)) {
            $username_err = 'This username is already taken.';
        } else {
            $username = $user;
        }
    }

    //validate password
    if (empty($pass)) {
        $password_err = 'Please enter a password.';
    } //password needs to be more than x characters
    else if (strlen($pass) < 6) {
        $password_err = 'Password must have at least 6 characters.';
    }
    ##check for symbols/other password requirements go here if needed
    //password is OK to go
    else {
        $password = $pass;
    }

    //validate confirm password
    if (empty($confPass)) {
        $confirm_password_err = 'Please confirm password.';
    } else {
        //as long as there are no password errors set, and also that the password and confirm password values match, everything is valid
        if (empty($g_password_err) && ($pass != $confPass)) {
            $confirm_password_err = 'Password did not match.';
        } else {
            $confirm_password = $confPass;
        }
    }

    //now we have to check the error variables and determine whether everything was valid or if there was an invalid input passed
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {
        //no error messages set, return the inputs passed were valid
        return array($username, $password);
    } else {
        //something in the passed values was invalid
        return array($username_err, $password_err, $confirm_password_err);
    }
}