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

    //update the record info
    $user->Attributes['FirstName'] = $firstName;
    $user->Attributes['LastName'] = $lastName;
    $user->Attributes['Username'] = $userName;
    $user->Attributes['Deleted'] = $userDeleted;

    //put the updates in the pending_updates table
    $result = $user->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

    if($result == true) {
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'Profile update successfully submitted and is awaiting approval for posting.';
    }
    else {
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "Profile update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}
//redirect user to index?
header('Location: /index.php');
die;