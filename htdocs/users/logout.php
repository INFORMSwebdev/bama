<?php
//include the init file w/ all the session starting and error reporting
require_once '../../init.php';

//unset all pertinent session variables
unset($_SESSION['loggedIn']);
unset($_SESSION['admin']);

//set up logout message to display on the login page
$_SESSION['logoutMessage'] = 'You have been logged out of the site.';

//redirect to login page
header("Location: /users/login.php");

//halt execution of this script after the redirect
//(this probably doesn't matter in this particular script since the end is right after here anyways)
die;