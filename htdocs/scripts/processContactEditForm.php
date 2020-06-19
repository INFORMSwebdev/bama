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
if (!isset($_SESSION['loggedIn']) && !is_numeric($_SESSION['loggedIn'])) {
    $_SESSION['logoutMessage'] = 'You must be logged in to submit contact edits.';
    header('Location: /users/login.php');
    die;
}

$user = new User($_SESSION['loggedIn']);

$contactId = filter_input(INPUT_POST, 'contactId', FILTER_VALIDATE_INT);

if(empty($contactId)){
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'Valid contact Id must be passed to process edits.';
    header('Location: /index.php');
    die;
}

$con = new Contact($contactId);

//check which button was pushed
if (isset($_POST['delete'])) {
    //delete button was clicked, create pending update
    $result = $con->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

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
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    //$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_NUMBER_INT);
    $phone = str_replace('-', '', $phone);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    //update the record
    $con->Attributes['ContactName'] = $name;
    $con->Attributes['ContactTitle'] = $title;
    $con->Attributes['ContactPhone'] = $phone;
    $con->Attributes['ContactEmail'] = $email;

    //check if admin or editor update
    if($user->id == 1) {
        //$con->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_APPROVE;
        $results = $con->save();
        if($results) {
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Contact successfully updated.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Contact update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //$con->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_NEW;
        //put the updates in the pending_updates table
        $result = $con->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->id);

        if ($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = Contact::getSubmissionMessage('edit' );
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Contact update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}

//redirect user to index?
//header("Location: /instructors/display.php?id={$instId}");
header('Location: /index.php');
die;