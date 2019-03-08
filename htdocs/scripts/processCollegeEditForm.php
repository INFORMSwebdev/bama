<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/7/2019
 * Time: 1:03 PM
 */
//require the init file
require_once '../../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $collegeId = filter_input(INPUT_POST, 'collegeId', FILTER_VALIDATE_INT);

    //check which button was pushed
    if (isset($_POST['delete'])) {
        //delete button was clicked, create pending update
        //get the college record
        $college = new College($collegeId);
        //get user info
        if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
            $user = new User($_SESSION['loggedIn']);
        }
        else {
            //I don't think this should ever be hit, but just in case:
            $user = new User(1);
        }

        $result = $college->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'College submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "College delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //gather form data
        $name = filter_input(INPUT_POST, 'collegeName', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'collegeType', FILTER_SANITIZE_STRING);

        //get user info
        if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
            $user = new User($_SESSION['loggedIn']);
        } else {
            //I don't think this should ever be hit, but just in case:
            $user = new User(1);
        }

        //get the institution record
        $coll = new College($collegeId);

        //update the record
        $coll->Attributes['CollegeName'] = $name;
        $coll->Attributes['CollegeType'] = $type;

        //put the updates in the pending_updates table
        $result = $coll->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'College update successfully submitted and is awaiting approval for posting.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "College update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}

//redirect user to index?
header('Location: /index.php');
die;