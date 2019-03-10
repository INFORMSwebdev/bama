<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 2:46 PM
 */
//require the init file
require_once '../../init.php';

$softId = filter_input(INPUT_POST, 'softId', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get the software record
    $soft = new Software($softId);

    //get user info
    if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
        $user = new User($_SESSION['loggedIn']);
    } else {
        //I don't think this should ever be hit, but just in case:
        $user = new User(1);
    }

    //check which button was pushed
    if (isset($_POST['delete'])) {
        //delete button was clicked, create pending update
        $result = $soft->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Program submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Program delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //gather form data
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $pub = filter_input(INPUT_POST, 'publisher', FILTER_SANITIZE_STRING);

        //update its attributes
        $soft->Attributes['SoftwareName'] = $name;
        $soft->Attributes['SoftwarePublisher'] = $pub;
        $soft->Attributes['Deleted'] = $softDeleted;

        //put the updates in the pending_updates table
        $result = $soft->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

        if ($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Software update successfully submitted and is awaiting approval for posting.';
        } else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Software update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}

//redirect user to index?
header("Location: /software/display.php?id=$softId");
die;