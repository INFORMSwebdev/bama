<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/4/2019
 * Time: 8:15 AM
 */
//require the init file
require_once '../../init.php';

//ensure we are processing only on a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //check which button was pushed
    if (isset($_POST['edit'])) {
        //edit button clicked, make sure Deleted flag is 0
        $userDeleted = 0;
    } else if (isset($_POST['delete'])) {
        //delete button was clicked, set the Deleted flag to 1
        $userDeleted = 1;
    }

    //get form values
    $firstName = filter_input(INPUT_POST, 'FirstName', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
    $lastName = filter_input(INPUT_POST, 'LastName', FILTER_SANITIZE_STRING);
    $userName = filter_input(INPUT_POST, 'Username', FILTER_SANITIZE_STRING);
    $userId = filter_input(INPUT_POST, 'userId', FILTER_VALIDATE_INT);

    //get user record
    $user = new User( $userId );

    if($user->Attributes['Username'] == $userName){
        //username is the same, update the other info
        //editors should be able to update their own info w/o approval from INFORMS admins, so just update the record
        $updates = array(
            'FirstName' => $firstName,
            'LastName' => $lastName
        );
    }
    else {
        //username was changed, check to make sure its not currently in use
        if(User::usernameExists($userName)){
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Profile update failed. The new username you have picked is currently in use. Please choose a different username.";
            //redirect user to index?
            header('Location: /index.php');
            die;
        }
        //username is not taken, go ahead and update it too
        $updates = array(
            'FirstName' => $firstName,
            'LastName' => $lastName,
            'Username' => $userName
        );
    }

    //update the record info
    $user->updateMultiple($updates);

    //set message to show user
    $_SESSION['editMessage']['success'] = true;
    $_SESSION['editMessage']['text'] = 'You have successfully updated your profile information.';
}
//redirect user to index?
header('Location: /index.php');
die;