<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/26/2019
 * Time: 11:48 AM
 */
//require the init file
require_once '../../init.php';

//get user info
if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
    $user = new User($_SESSION['loggedIn']);
}
else{
    $_SESSION['logoutMessage'] = 'You must be logged in to submit contact edits.';
    header('Location: /users/login.php');
    die;
}

$contactId = filter_input(FILTER_POST, 'contactId', FILTER_VALIDATE_INT);

if(empty($contactId)){
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'Valid contact Id must be passed to process edits.';
    header('Location: /index.php');
    die;
}
