<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 11:07 AM
 */
//require the init file
require_once '../../init.php';

//get user info
if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
    $user = new User($_SESSION['loggedIn']);
}
else {
    $_SESSION['logoutMessage'] = 'You must be logged in to submit new case studies.';
    header('Location: /users/login.php');
    die;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //gather form data
    $title = filter_input(INPUT_POST, 'caseTitle', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'caseType', FILTER_SANITIZE_STRING);
    $useDesc = filter_input(INPUT_POST, 'useDesc', FILTER_SANITIZE_STRING);
    $access = filter_input(INPUT_POST, 'capstoneProject', FILTER_SANITIZE_STRING);
    $analytics = filter_input(INPUT_POST, 'analyticTag', FILTER_SANITIZE_STRING);
    $business = filter_input(INPUT_POST, 'businessTag', FILTER_SANITIZE_STRING);

    $courseId = filter_input(INPUT_POST, 'courseId', FILTER_VALIDATE_INT);

    //get the form data into an array to create an object
    $data = array(
        'Case' => $title,
        'CaseType' => $type,
        'CaseUseDescription' => $useDesc,
        'CaseAccess' => $access,
        'AnalyticTag' => $analytics,
        'BusinessTag' => $business
    );
    //create an object w/ no Id
    $x = CaseStudy::create( $data );

    //assign case study to course
    if ($courseId) {
        $course = new Course($courseId);
        $course->assignCaseStudy($x->Attributes['CaseId']);
    }

    if($user->id == 1){
        if($result){
            $x->update('ApprovalStatusId', APPROVAL_TYPE_APPROVE);
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New case study successfully added.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New case study was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //add record to pending_updates
        $result = $x->createPendingUpdate(UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

        //report on results of insertion
        if ($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New case study successfully submitted and is awaiting approval for posting.';
        } else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New case study was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}
//redirect user to index
header('Location: /index.php');
die;