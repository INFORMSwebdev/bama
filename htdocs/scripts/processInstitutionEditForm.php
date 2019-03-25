<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 11:47 AM
 */
//require the init file
require_once '../../init.php';

$instId = filter_input(INPUT_POST, 'instId', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get the institution record
    $inst = new Institution($instId);
    //get user info
    if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
        $user = new User($_SESSION['loggedIn']);
    }
    else{
        $_SESSION['logoutMessage'] = 'You must be logged in to submit institution edits.';
        header('Location: /users/login.php');
        die;
    }

    //check which button was pushed
    if (isset($_POST['delete'])) {
        //delete button was clicked, create pending update
        $result = $inst->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Institution submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Institution delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //gather form data
        $name = filter_input(INPUT_POST, 'institutionName', FILTER_SANITIZE_STRING);
        $addr = filter_input(INPUT_POST, 'institutionAddress', FILTER_SANITIZE_STRING);
        $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
        $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
        $zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
        $region = filter_input(INPUT_POST, 'region', FILTER_SANITIZE_STRING);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $access = filter_input(INPUT_POST, 'access', FILTER_VALIDATE_URL);
        $instId = filter_input(INPUT_POST, 'instId', FILTER_VALIDATE_INT);

        //update the record
        $inst->Attributes['InstitutionName'] = $name;
        $inst->Attributes['InstitutionAddress'] = $addr;
        $inst->Attributes['InstitutionCity'] = $city;
        $inst->Attributes['InstitutionState'] = $state;
        $inst->Attributes['InstitutionZip'] = $zip;
        $inst->Attributes['InstitutionRegion'] = $region;
        $inst->Attributes['InstitutionPhone'] = $phone;
        $inst->Attributes['InstitutionEmail'] = $email;
        $inst->Attributes['InstitutionAccess'] = $access;

        if($user->id == 1){
            $inst->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_APPROVE;
            $results = $inst->save();
            if($results) {
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = 'Institution successfully updated.';
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Institution update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
        else {
            $inst->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_NEW;
            //put the updates in the pending_updates table
            $result = $inst->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

            if ($result == true) {
                //set message to show user
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = 'Institution update successfully submitted and is awaiting approval for posting.';
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Institution update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
    }
}

//redirect user to index?
header("Location: /institutions/display.php?id={$instId}");
die;