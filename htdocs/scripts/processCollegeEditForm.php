<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/7/2019
 * Time: 1:03 PM
 */
//require the init file
require_once '../../init.php';

//get user info
if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
    $user = new User($_SESSION['loggedIn']);
}
else {
    $_SESSION['logoutMessage'] = 'You must be logged in to edit colleges.';
    header('Location: /users/login.php');
    die;
}

$collegeId = filter_input(INPUT_POST, 'collegeId', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get the college record
    $college = new College($collegeId);

    //check which button was pushed
    if (isset($_POST['delete'])) {
        //delete button was clicked, create pending update
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

        //update the record
        $college->Attributes['CollegeName'] = $name;
        //$college->Attributes['CollegeType'] = $type;
        $typeId = filter_input(INPUT_POST, 'collegeType', FILTER_VALIDATE_INT);
        if(is_numeric($typeId)){
            $college->Attributes['TypeId'] = $typeId;
        } else {
            $college->Attributes['TypeId'] = NULL;
        }

        $otherType = filter_input(INPUT_POST, 'otherType', FILTER_SANITIZE_STRING);
        if($otherType === FALSE){
            $otherType = NULL;
        }
        $college->Attributes['OtherType'] = $otherType;

        if($user->id == 1){
            //$college->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_APPROVE;
            $results = $college->save();
            if($results) {
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = 'College successfully updated.';
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "College update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
        else {
            //$college->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_NEW;
            //put the updates in the pending_updates table
            $result = $college->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->id);

            if ($result == true) {
                //set message to show user
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = College::getSubmissionMessage('edit' );
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "College update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
    }
}

//redirect user to index?
//header("Location: /colleges/display.php?id={$collegeId}");
header('Location: /index.php');
die;