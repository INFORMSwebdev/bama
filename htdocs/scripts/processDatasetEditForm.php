<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/28/2019
 * Time: 2:31 PM
 */
//require the init file
require_once '../../init.php';

//get user info
if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
    $user = new User($_SESSION['loggedIn']);
}
else{
    $_SESSION['logoutMessage'] = 'You must be logged in to submit dataset edits.';
    header('Location: /users/login.php');
    die;
}

$datasetId = filter_input(INPUT_POST, 'datasetId', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get the object to be updated
    $dataset = new Dataset($datasetId);

    if (isset($_POST['delete'])) {
        //delete button was clicked, create pending update
        $result = $dataset->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Dataset submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Dataset delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //gather form data
        $name = filter_input(INPUT_POST, 'datasetName', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'datasetType', FILTER_SANITIZE_STRING);
        $integrity = filter_input(INPUT_POST, 'datasetIntegrity', FILTER_SANITIZE_STRING);
        $fileType = filter_input(INPUT_POST, 'datasetFileType', FILTER_SANITIZE_STRING);
        $useDesc = filter_input(INPUT_POST, 'useDescription', FILTER_SANITIZE_STRING);
        $access = filter_input(INPUT_POST, 'datasetAccess', FILTER_SANITIZE_STRING);
        $analytics = filter_input(INPUT_POST, 'analyticTag', FILTER_SANITIZE_STRING);
        if (empty($analytics)) {
            $analytics = NULL;
        }
        $business = filter_input(INPUT_POST, 'businessTag', FILTER_SANITIZE_STRING);
        if (empty($business)) {
            $business = NULL;
        }

        //set it up with the new info
        $dataset->Attributes['DatasetName'] = $name;
        $dataset->Attributes['DatasetType'] = $type;
        $dataset->Attributes['DatasetIntegrity'] = $integrity;
        $dataset->Attributes['DatasetFileType'] = $fileType;
        $dataset->Attributes['DatasetUseDescription'] = $useDesc;
        $dataset->Attributes['DatasetAccess'] = $access;
        $dataset->Attributes['AnalyticTag'] = $analytics;
        $dataset->Attributes['BusinessTag'] = $business;


        if($user->id == 1){
            //$dataset->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_APPROVE;
            $results = $dataset->save();
            if($results) {
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = 'Dataset successfully updated.';
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Dataset update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
        else {
            //$dataset->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_NEW;
            //put the updates in the pending_updates table
            $result = $course->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

            if ($result == true) {
                //set message to show user
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = Dataset::getSubmissionMessage('edit');
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Dataset update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
    }
}

//redirect user to index?
header("Location: /datasets/display.php?id={$datasetId}");
die;