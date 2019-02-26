<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/25/2019
 * Time: 4:15 PM
 */
//require the init file
require_once '../../init.php';

//ensure we are processing only on a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //check which button was pushed
    if (isset($_POST['edit'])) {
        //edit button clicked, make sure Deleted flag is 0
        $progDeleted = 0;
    } else if (isset($_POST['delete'])) {
        //delete button was clicked, set the Deleted flag to 1
        $progDeleted = 1;
    }

    //get all the form field values
    $progId = filter_input(INPUT_POST, 'programId', FILTER_VALIDATE_INT);
    $progName = filter_input(INPUT_POST, 'programName', FILTER_SANITIZE_STRING);
    $instId = filter_input(INPUT_POST, 'Institution', FILTER_VALIDATE_INT);
    $progType = filter_input(INPUT_POST, 'ProgramType', FILTER_SANITIZE_STRING);
    $progObjs = filter_input(INPUT_POST, 'ProgramObjs', FILTER_SANITIZE_STRING);
    $progAccess = filter_input(INPUT_POST, 'ProgramAccess', FILTER_VALIDATE_URL);
    $progYear = filter_input(INPUT_POST, 'YearEstablished', FILTER_SANITIZE_NUMBER_INT);
    $progScholarships = filter_input(INPUT_POST, 'Scholarship', FILTER_SANITIZE_STRING);
    $progDeliveryMethod = filter_input(INPUT_POST, 'DeliveryMethod', FILTER_SANITIZE_STRING);
    $progFullTime = filter_input(INPUT_POST, 'FullTime', FILTER_SANITIZE_STRING);
    $progPartTime = filter_input(INPUT_POST, 'PartTime', FILTER_SANITIZE_STRING);
    $progTestingReqs = filter_input(INPUT_POST, 'TestingRequirement', FILTER_SANITIZE_STRING);
    $progOtherReqs = filter_input(INPUT_POST, 'OtherRequirement', FILTER_SANITIZE_STRING);
    $progCredits = filter_input(INPUT_POST, 'Credits', FILTER_SANITIZE_STRING);
    $progCostPer = filter_input(INPUT_POST, 'CostPerCredit', FILTER_SANITIZE_STRING);
    $progResTuition = filter_input(INPUT_POST, 'ResidentTuition', FILTER_SANITIZE_STRING);
    $progNonResTuition = filter_input(INPUT_POST, 'NonResident', FILTER_SANITIZE_STRING);
    //since the following 2 Id's can be null, we don't want a filter
    $contactId = filter_input(INPUT_POST, 'ContactId');
    $collegeId = filter_input(INPUT_POST, 'CollegeId');
    $analyticsFlag = filter_input(INPUT_POST, 'AnalyticsFlag'); // maybe add FILTER_VALIDATE_BOOLEAN? Test and find out
    $orFlag = filter_input(INPUT_POST, 'AnalyticsFlag'); // maybe add FILTER_VALIDATE_BOOLEAN? Test and find out

    //get the record info to update
    $prog = new Program($progId);

    //update the info in the objects attributes
    $prog->Attributes['InstitutionId'] = $instId;
    $prog->Attributes['ContactId'] = $contactId;
    $prog->Attributes['ProgramName'] = $progName;
    $prog->Attributes['ProgramType'] = $progType;
    $prog->Attributes['DeliveryMethod'] = $progDeliveryMethod;
    $prog->Attributes['ProgramAccess'] = $progAccess;
    $prog->Attributes['ProgramObjectives'] = $progObjs;
    $prog->Attributes['FullTimeDuration'] = $progFullTime;
    $prog->Attributes['PartTimeDuration'] = $progPartTime;
    $prog->Attributes['TestingRequirements'] = $progTestingReqs;
    $prog->Attributes['OtherRequirements'] = $progOtherReqs;
    $prog->Attributes['Credits'] = $progCredits;
    $prog->Attributes['YearEstablished'] = $progYear;
    $prog->Attributes['Scholarship'] = $progScholarships;
    $prog->Attributes['EstimatedResidentTuition'] = $progResTuition;
    $prog->Attributes['EstimatedNonresidentTuition'] = $progNonResTuition;
    $prog->Attributes['CostPerCredit'] = $progCostPer;
    $prog->Attributes['Deleted'] = $progDeleted;
    $prog->Attributes['ORFlag'] = $orFlag;
    $prog->Attributes['AnalyticsFlag'] = $analyticsFlag;
    $prog->Attributes['CollegeId'] = $collegeId;

    //get the users Id to put in the table
    if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
        $user = new User($_SESSION['loggedIn']);
    } else {
        //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
        $user = new User(1);
    }

    //put the updates in the pending_updates table
    $result = $prog->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

    if($result == true) {
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'Program update successfully submitted and is awaiting approval for posting.';
    }
    else {
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "Program update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}
//redirect user to index?
header('Location: /index.php');
die;