<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 10:49 AM
 */
//require the init file
require_once '../../init.php';

//get the users Id to put in the table
if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
    $user = new User($_SESSION['loggedIn']);
}
else {
    $_SESSION['logoutMessage'] = 'You must be logged in to submit new programs.';
    header('Location: /users/login.php');
    die;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //collect the submitted info
    $progName = filter_input(INPUT_POST, 'programName', FILTER_SANITIZE_STRING);
    $instId = filter_input(INPUT_POST, 'instId', FILTER_VALIDATE_INT);
    $progType = filter_input(INPUT_POST, 'ProgramType', FILTER_SANITIZE_STRING);
    $progObjs = filter_input(INPUT_POST, 'ProgramObjs', FILTER_SANITIZE_STRING);
    $progAccess = filter_input(INPUT_POST, 'ProgramAccess', FILTER_VALIDATE_URL);
    //make sure if this field is blank, we don't pass FALSE to the DB
    if($progAccess == FALSE){
        $progAccess = NULL;
    }
    $progYear = filter_input(INPUT_POST, 'YearEstablished', FILTER_SANITIZE_NUMBER_INT);
    if(empty($progYear)){
        $progYear = NULL;
    }
    $progScholarships = filter_input(INPUT_POST, 'Scholarship', FILTER_SANITIZE_STRING);
    //$progDeliveryMethod = filter_input(INPUT_POST, 'DeliveryMethod', FILTER_SANITIZE_STRING);
    $progDeliveryMethod = filter_input(INPUT_POST, 'DeliveryMethod', FILTER_VALIDATE_INT);
    //default delivery method to unknown if none selected
    if(!$progDeliveryMethod) $progDeliveryMethod = 10;
    $progFullTime = filter_input(INPUT_POST, 'FullTime', FILTER_SANITIZE_STRING);
    $progPartTime = filter_input(INPUT_POST, 'PartTime', FILTER_SANITIZE_STRING);
    $progTestingReqs = filter_input(INPUT_POST, 'TestingRequirement', FILTER_SANITIZE_STRING);
    $progOtherReqs = filter_input(INPUT_POST, 'OtherRequirement', FILTER_SANITIZE_STRING);
    $progCredits = filter_input(INPUT_POST, 'Credits', FILTER_SANITIZE_STRING);
    $progCostPer = filter_input(INPUT_POST, 'CostPerCredit', FILTER_SANITIZE_STRING);
    $progResTuition = filter_input(INPUT_POST, 'ResidentTuition', FILTER_SANITIZE_STRING);
    $progNonResTuition = filter_input(INPUT_POST, 'NonResident', FILTER_SANITIZE_STRING);
    $analyticsFlag = filter_input(INPUT_POST, 'AnalyticsFlag', FILTER_VALIDATE_BOOLEAN);
    //if the flag value is null, the checkbox was NOT checked
    if(!isset($analyticsFlag)){
        $analyticsFlag = FALSE;
    }
    $orFlag = filter_input(INPUT_POST, 'ORFlag', FILTER_VALIDATE_BOOLEAN);
    //if the flag value is null, the checkbox was NOT checked
    if(!isset($orFlag)){
        $orFlag = FALSE;
    }

    $collegeId = filter_input(INPUT_POST, 'collegeSelectList', FILTER_VALIDATE_INT);

    //gather data to put in the pending_update record
    $data = array(
        "InstitutionId" => $instId,
        'ProgramName' => $progName,
        'ProgramType' => $progType,
        //'DeliveryMethod' => $progDeliveryMethod,
        'DeliveryMethodId' => $progDeliveryMethod,
        'ProgramAccess' => $progAccess,
        'ProgramObjectives' => $progObjs,
        'FullTimeDuration' => $progFullTime,
        'PartTimeDuration' => $progPartTime,
        'TestingRequirements' => $progTestingReqs,
        'OtherRequirements' => $progOtherReqs,
        'Credits' => $progCredits,
        'YearEstablished' => $progYear,
        'Scholarship' => $progScholarships,
        'EstimatedResidentTuition' => $progResTuition,
        'EstimatedNonresidentTuition' => $progNonResTuition,
        'CostPerCredit' => $progCostPer,
        'ORFlag' => $orFlag,
        'AnalyticsFlag' => $analyticsFlag,
        'CollegeId' => $collegeId
    );

    //make a Program record
    $x = new Program(Program::create( $data ));

    if($user->id == 1){
        if($x){
            $x->update('ApprovalStatusId', APPROVAL_TYPE_APPROVE);
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New program successfully added.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New program was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //add record to pending_updates
        $result = $x->createPendingUpdate(UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

        //check to make sure the insert occurred successfully
        if ($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New program successfully submitted and is awaiting approval for posting.';
        } else {
            //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New program was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}
//redirect user to index
header('Location: /index.php');
die;