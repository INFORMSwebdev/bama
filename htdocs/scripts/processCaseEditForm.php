<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 11:07 AM
 */
//require the init file
require_once '../../init.php';

if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
    $user = new User($_SESSION['loggedIn']);
}
else {
    $_SESSION['logoutMessage'] = 'You must be logged in to edit case studies.';
    header('Location: /users/login.php');
    die;
}

$caseId = filter_input(INPUT_POST, 'caseId', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get the object to be updated
    $c = new CaseStudy($caseId);

    //check which button was pushed
    if (isset($_POST['delete'])) {
        //delete button was clicked, create pending update
        $result = $c->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Case study submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Case study delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //gather form data
        $title = filter_input(INPUT_POST, 'caseTitle', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'caseType', FILTER_SANITIZE_STRING);
        $useDesc = filter_input(INPUT_POST, 'useDesc', FILTER_SANITIZE_STRING);
        $access = filter_input(INPUT_POST, 'caseAccess', FILTER_SANITIZE_STRING);
        $analytics = filter_input(INPUT_POST, 'analyticTag', FILTER_SANITIZE_STRING);
        $business = filter_input(INPUT_POST, 'businessTag', FILTER_SANITIZE_STRING);

        //set it up with the new info
        $c->Attributes['CaseTitle'] = $title;
        $c->Attributes['CaseType'] = $type;
        $c->Attributes['CaseUseDescription'] = $useDesc;
        $c->Attributes['CaseAccess'] = $access;
        $c->Attributes['AnalyticTag'] = $analytics;
        $c->Attributes['BusinessTag'] = $business;

        if($user->id == 1){
            //$c->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_APPROVE;
            $results = $c->save();
            if($results) {
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = 'Case study successfully updated.';
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Case study update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
        else {
            //$c->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_NEW;

            //put the updates in the pending_updates table
            $result = $c->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

            if ($result == true) {
                //set message to show user
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = 'Case study update successfully submitted and is awaiting approval for posting.';
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Case study update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
    }
}

//redirect user to index?
header("Location: /cases/display.php?id={$caseId}");
die;