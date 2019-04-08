<?php
//include the init.php file
require_once '../../init.php';

//check if user is already logged in (first checking if the session variable is set, then if it is true)
if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] == true) {
    //redirect user to their institution admin dashboard
    header("Location: /index.php");
    //stop execution of this script after redirect
    die;
}

//process the data when the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validateInput(trim($_POST["username"]), trim($_POST["password"]));
}

function validateInput($user, $pass)
{
    //define variables that init to empty strings
    $username = $password = "";
    $username_err = $password_err = "";

    //validate username
    if (empty($user)) {
        //empty username passed
        $username_err = "Please enter your username";
    } else {
        $username = $user;
    }

    //validate password
    if (empty($pass)) {
        $password_err = "Please enter a password";
    } else {
        $password = $pass;
    }

    //validate credentials
    if (empty($username_err) && empty($password_err)) {
        //no invalid input found, check the DB to make sure the entered username is in the system
        $id = User::usernameExists($username);
        if(isset($id) && is_numeric($id) && $id > 0){
            $curUser = new User($id);

            //check if user account is active or not
            $active = $curUser->checkActiveStatus();
            if(!$active){
                $_SESSION['logoutMessage'] = "The account you are attempting to use is marked as inactive and cannot be logged into. If this is a mistake, please contact <a href='mailto:webdev@mail.informs.org'>webdev#mail.informs.org</a>.";
                unset($_SESSION['loginInput']);
                unset($_SESSION['loginErrors']);
                header('Location: /users/login.php');
                die;
            }

            //validate the password passed against the stored value
            if($curUser->checkPassword($password) == true) {
                //if valid, user is considered logged in; store user ID in session variable
                $_SESSION["loggedIn"] = $curUser->id;
                //unset the session variable containing use input if it was set so the system doesn't get 'confused' about stuff
                unset($_SESSION['loginInput']);

                //send user to the dashboard
                header('Location: /index.php');
                die;
            }
            //otherwise, collect errors to print out on login page
            else {
                $password_err = "The password entered did not match the password on record.";
                //set session variables to inform user of errors
                $_SESSION['loginErrors'] = array( 'usernameErrors' => $username_err, 'passwordErrors' => $password_err);
                $_SESSION['loginInput'] = array( 'username' => $username );
                //redirect to login page and inform user the results of the password verification
                header('Location: /users/login.php');
                die;
            }
        } else {
            //username not in system
            $username_err = "No account found with that username.";
            //set session variables to inform user of errors
            $_SESSION['loginErrors'] = array( 'usernameErrors' => $username_err, 'passwordErrors' => $password_err);
            $_SESSION['loginInput'] = array( 'username' => $username );
            //redirect to login page and inform user the results of the username check
            header('Location: /users/login.php');
            die;
        }
    } else {
        //there were errors in the user input
        $_SESSION['loginErrors'] = array('usernameErrors' => $username_err, 'passwordErrors' => $password_err);
        $_SESSION['loginInput'] = array('username' => $user);
        //redirect user back to login page to re-input stuff
        header("Location: /users/login.php");
        die;
    }
}