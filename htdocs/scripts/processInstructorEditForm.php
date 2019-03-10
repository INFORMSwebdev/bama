<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 2:35 PM
 */
//require the init file
require_once '../../init.php';

$instId = filter_input(INPUT_POST, 'instId', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get the instructor record
    $inst = new Instructor($instId);

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
        $result = $inst->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Instructor submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Instructor delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //gather form data
        $fName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
        $lName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
        $prefix = filter_input(INPUT_POST, 'prefix', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

        //update the record
        $inst->Attributes['InstructorFirstName'] = $fName;
        $inst->Attributes['InstructorLastName'] = $lName;
        $inst->Attributes['InstructorPrefix'] = $prefix;
        $inst->Attributes['InstructorEmail'] = $email;

        //put the updates in the pending_updates table
        $result = $inst->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->id);

        if ($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Instructor update successfully submitted and is awaiting approval for posting.';
        } else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Instructor update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}

//redirect user to index?
header("Location: /instructors/display.php?id=$instId");
die;