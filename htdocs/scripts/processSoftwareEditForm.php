<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 2:46 PM
 */
//require the init file
require_once '../../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //check which button was pushed
    if (isset($_POST['edit'])) {
        //edit button clicked, make sure Deleted flag is 0
        $softDeleted = 0;
    } else if (isset($_POST['delete'])) {
        //delete button was clicked, set the Deleted flag to 1
        $softDeleted = 1;
    }

    //gather form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $pub = filter_input(INPUT_POST, 'publisher', FILTER_SANITIZE_STRING);
    $softId = filter_input(INPUT_POST, 'softId', FILTER_VALIDATE_INT);

    //get user info
    if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
        $user = new User($_SESSION['loggedIn']);
    } else {
        //I don't think this should ever be hit, but just in case:
        $user = new User(1);
    }

    //get the software record
    $soft = new Software($softId);

    //update its attributes
    $soft->Attributes['SoftwareName'] = $name;
    $soft->Attributes['SoftwarePublisher'] = $pub;
    $soft->Attributes['Deleted'] = $softDeleted;

    //put the updates in the pending_updates table
    $result = $soft->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

    if($result == true) {
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'Software update successfully submitted and is awaiting approval for posting.';
    }
    else {
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "Software update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}

//redirect user to index?
header('Location: /index.php');
die;