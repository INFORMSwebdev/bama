<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/28/2019
 * Time: 1:47 PM
 */
//require the init file
require_once '../../init.php';

$courseId = filter_input(INPUT_POST, 'courseId', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get the object to be updated
    $course = new Course($courseId);

    //get user info
    if (isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])) {
        $user = new User($_SESSION['loggedIn']);
    }

    //check which button was pushed
    if (isset($_POST['delete'])) {
        //delete button was clicked, create pending update
        $result = $course->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Course submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Course delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //gather form data
        $title = filter_input(INPUT_POST, 'courseTitle', FILTER_SANITIZE_STRING);

        $number = filter_input(INPUT_POST, 'courseNumber', FILTER_SANITIZE_STRING);
        $delivery = filter_input(INPUT_POST, 'deliveryMethod', FILTER_SANITIZE_STRING);
        $capstone = filter_input(INPUT_POST, 'capstoneProject', FILTER_SANITIZE_STRING);
        $text = filter_input(INPUT_POST, 'courseText', FILTER_SANITIZE_STRING);
        $analytics = filter_input(INPUT_POST, 'analyticTag', FILTER_SANITIZE_STRING);
        $business = filter_input(INPUT_POST, 'businessTag', FILTER_SANITIZE_STRING);
        //since (for whatever reason) syllabus filesize column in the DB in non-nullable and we aren't allowing uploads of files
        //we have to set the filesize to 0
        $sylSize = 0;

        //set it up with the new info
        $course->Attributes['CourseTitle'] = $title;
        $course->Attributes['CourseNumber'] = $number;
        $course->Attributes['DeliveryMethod'] = $delivery;
        $course->Attributes['HasCapstoneProject'] = $capstone;
        $course->Attributes['CourseText'] = $text;
        $course->Attributes['SyllabusFilesize'] = $sylSize;
        $course->Attributes['AnalyticTag'] = $analytics;
        $course->Attributes['BusinessTag'] = $business;

        if($user->id == 1){
            $course->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_APPROVE;
            $course->save();
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Course successfully updated.';
        }
        else {
            $course->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_NEW;
            //put the updates in the pending_updates table
            $result = $course->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

            if ($result == true) {
                //set message to show user
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = 'Course update successfully submitted and is awaiting approval for posting.';
            } else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Course update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
    }
}

//redirect user to index?
header("Location: /courses/display.php?id={$courseId}");
die;