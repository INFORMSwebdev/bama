<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 11:07 AM
 */
//require the init file
require_once '../../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //check which button was pushed
    if (isset($_POST['edit'])) {
        //edit button clicked, make sure Deleted flag is 0
        $cDeleted = 0;
    } else if (isset($_POST['delete'])) {
        //delete button was clicked, set the Deleted flag to 1
        $cDeleted = 1;
    }

    //gather form data
    $title = filter_input(INPUT_POST, 'caseTitle', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'caseType', FILTER_SANITIZE_STRING);
    $useDesc = filter_input(INPUT_POST, 'useDesc', FILTER_SANITIZE_STRING);
    $access = filter_input(INPUT_POST, 'caseAccess', FILTER_SANITIZE_STRING);
    $analytics = filter_input(INPUT_POST, 'analyticTag', FILTER_SANITIZE_STRING);
    $business = filter_input(INPUT_POST, 'businessTag', FILTER_SANITIZE_STRING);
    $caseId = filter_input(INPUT_POST, 'caseId', FILTER_VALIDATE_INT);

    //get user info
    if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
        $user = new User($_SESSION['loggedIn']);
    } else {
        //I don't think this should ever be hit, but just in case:
        $user = new User(1);
    }

    //get the object to be updated
    $c = new CaseStudy($caseId);

    //set it up with the new info
    $c->Attributes['CaseTitle'] = $title;
    $c->Attributes['CaseType'] = $type;
    $c->Attributes['CaseUseDescription'] = $useDesc;
    $c->Attributes['CaseAccess'] = $access;
    $c->Attributes['AnalyticTag'] = $analytics;
    $c->Attributes['BusinessTag'] = $business;
    $c->Attributes['Deleted'] = $cDeleted;

    //put the updates in the pending_updates table
    $result = $c->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

    if($result == true) {
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'Case study update successfully submitted and is awaiting approval for posting.';
    }
    else {
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "Case study update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}

//redirect user to index?
header('Location: /index.php');
die;