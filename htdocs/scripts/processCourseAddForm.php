<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/28/2019
 * Time: 9:26 AM
 */
//require the init file
require_once '../../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //gather form data
    $title = filter_input(INPUT_POST, 'courseTitle', FILTER_SANITIZE_STRING);
    $instructorId= filter_input(INPUT_POST, 'instructor', FILTER_VALIDATE_INT);
    if(empty($instructorId)){
        $instructorId = null;
    }
    $number = filter_input(INPUT_POST, 'courseNumber', FILTER_SANITIZE_STRING);
    $delivery = filter_input(INPUT_POST, 'deliveryMethod', FILTER_SANITIZE_STRING);
    $capstone = filter_input(INPUT_POST, 'capstoneProject', FILTER_SANITIZE_STRING);
    $text = filter_input(INPUT_POST, 'courseText', FILTER_SANITIZE_STRING);
    $analytics = filter_input(INPUT_POST, 'analyticTag', FILTER_SANITIZE_STRING);
    $business = filter_input(INPUT_POST, 'businessTag', FILTER_SANITIZE_STRING);
    //since (for whatever reason) syllabus filesize column in the DB in non-nullable and we aren't allowing uploads of files
    //we have to set the filesize to 0
    $sylSize = 0;
    $progId = filter_input(INPUT_POST, 'progId', FILTER_VALIDATE_INT);



    //get user info
    if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
        $user = new User($_SESSION['loggedIn']);
    }
    else {
        $_SESSION['logoutMessage'] = 'You must be logged in to submit new courses.';
        header('Location: /users/login.php');
        die;
    }

    //get the form data into an array to create an object
    $data = array(
        'CourseTitle' => $title,
        'InstructorId' => $instructorId,
        'CourseNumber' => $number,
        'DeliveryMethod' => $delivery,
        'HasCapstoneProject' => $capstone,
        'CourseText' => $text,
        'SyllabusFilesize' => $sylSize,
        'AnalyticTag' => $analytics,
        'BusinessTag' => $business
    );
    //create an object w/ no Id
    $x = Course::createInstance( $data );
    //add record to pending_updates
    $result = $x->createPendingUpdate( UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

    //report on results of insertion
    if($result == true) {

        $update = new PendingUpdate($result);

        //assign the course to the program
        if($progId){
            //create program object
            $program = new Program($progId);

            //assign course to this program
            $program->assignCourse($update->Attributes['UpdateRecordId']);
        }

        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'New course successfully submitted and is awaiting approval for posting.';
    }
    else {
        //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "New course was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}
//redirect user to index
header('Location: /index.php');
die;