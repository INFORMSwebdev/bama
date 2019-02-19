<?php
//include the init.php file
require_once '../../init.php';

//check if user is already logged in (first checking if the session variable is set, then if it is true)
if (isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] == true) {
    //redirect user to their institution admin dashboard
    # ToDo: update this location when the admin dashboard is created
    header("Location: ../index.php");
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
            //validate the password passed against the stored value
            # ToDo: figure out how the password verification works with the User object and finish this section of code
            //$passVerify = User::validatePassword($password);
            //if verified:
            //if valid, user is considered logged in; store user info in session variables
            if($curUser->validatePassword($password) == true) {
                $_SESSION["loggedIn"] = true;
                $_SESSION["id"] = $curUser->Attributes['UserId'];
                $_SESSION["username"] = $username;
            }
            //ELSE collect error to print out on login page
            else {
                $password_err = "The password entered was not valid.";
            }
        } else {
            //username not in system
            $username_err = "No account found with that username.";
        }

        if(empty($username_err) && empty($password_err)){

        }
    } else {
        //there were errors in the user input
        $_SESSION['loginErrors'] = array($username_err, $password_err);
        $_SESSION['loginInput'] = array('username' => $user, 'password' => $password);
        //redirect user back to login page to re-input stuff
        header("Location: ../users/login.php");
        die;
    }
}