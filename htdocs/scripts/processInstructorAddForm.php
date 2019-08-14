<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 2:35 PM
 */
//require the init file
require_once '../../init.php';

//get user info
if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
    $user = new User($_SESSION['loggedIn']);
}
else {
    $_SESSION['logoutMessage'] = 'You must be logged in to submit new instructors.';
    header('Location: /users/login.php');
    die;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //gather form data
    $fName = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING);
    $lName = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING);
    $prefix = filter_input(INPUT_POST, 'prefix', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $courseId = filter_input(INPUT_POST, 'courseId', FILTER_VALIDATE_INT);

    //get the form data into an array to create an object
    $data = array(
        'InstructorFirstName' => $fName,
        'InstructorLastName' => $lName,
        'InstructorPrefix' => $prefix,
        'InstructorEmail' => $email
    );

    //create an object w/ an Id
    $x = new Instructor(Instructor::create( $data ));

    //add instructor to course
    if ($courseId) {
        $course = new Course($courseId);
        $course->assignInstructor($x->Attributes['InstructorId']);
    }

    if($user->id == 1){
        if($x){
            $x->update('ApprovalStatusId', APPROVAL_TYPE_APPROVE);
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New instructor successfully added.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New instructor was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //add record to pending_updates
        $result = $x->createPendingUpdate(UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

        //report on results of insertion
        if ($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New instructor successfully submitted and is awaiting approval for posting.';
        } else {
            //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New instructor was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}
//redirect user to index
header('Location: /index.php');
die;