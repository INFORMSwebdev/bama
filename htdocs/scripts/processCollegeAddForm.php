<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/6/2019
 * Time: 1:58 PM
 */
//require the init file
require_once '../../init.php';

//get user info
if(isset($_SESSION['loggedIn'])){
    $user = new User($_SESSION['loggedIn']);
}
else {
    $_SESSION['logoutMessage'] = 'You must be logged in to submit new colleges.';
    header('Location: /users/login.php');
    die;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //gather form fields
    $name = filter_input(INPUT_POST, 'collegeName', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'collegeType', FILTER_SANITIZE_STRING);
    $instId = filter_input(INPUT_POST, 'institutionId', FILTER_VALIDATE_INT);

    //get the form data into an array to create an object
    $data = array(
        'InstitutionId' => $instId,
        'CollegeName' => $name,
        'CollegeType' => $type
    );

    //create an object w/ Id
    $x = College::create( $data );

    if($user->id == 1){
        if($x){
            $x->update('ApprovalStatusId', APPROVAL_TYPE_APPROVE);
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New college successfully added.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New college was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //add record to pending_updates
        $result = $x->createPendingUpdate(UPDATE_TYPE_INSERT, $user->id);

        //report on results of insertion
        if ($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New college successfully submitted and is awaiting approval for posting.';
        }
        else {
            //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New college was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}
//redirect user to index
header('Location: /index.php');
die;