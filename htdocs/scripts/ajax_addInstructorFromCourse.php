<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/8/2019
 * Time: 11:16 AM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

//get input fields
$firstName = filter_input(INPUT_POST, 'FirstName', FILTER_SANITIZE_STRING);
$lastName = filter_input(INPUT_POST, 'LastName', FILTER_SANITIZE_STRING);
$prefix = filter_input(INPUT_POST, 'Prefix', FILTER_SANITIZE_STRING);
if(empty($prefix)){
    $prefix = null;
}
$email = filter_input(INPUT_POST, 'Email', FILTER_VALIDATE_EMAIL);
if(empty($email)){
    $email = null;
}

if (!$firstName && !$lastName){
    //missing both first and last names
    $errors[] = "Missing required parameter: FirstName";
    $errors[] = "Missing required parameter: LastName";
}
else if(!$lastName){
    //missing only last name
    $errors[] = "Missing required parameter: LastName";
}
else if(!$firstName){
    //missing only first name
    $errors[] = "Missing required parameter: FirstName";
}
else {
    //not missing any required fields
    $data = array(
        'InstructorFirstName' => $firstName,
        'InstructorLastName' => $lastName,
        'InstructorPrefix' => $prefix,
        'InstructorEmail' => $email
    );

    if(isset($_SESION['loggedIn'])) {
        $user = new User($_SESSION['loggedIn']);
    }
    else{
        //i don't think this will ever happen, but just in case:
        $user = new User(1);
    }

    //create an object w/ no Id
    $x = Instructor::createInstance( $data );

    //add record to pending_updates
    $result = $x->createPendingUpdate( UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

    //report on results of insertion
    if($result == true) {
        $pU = new PendingUpdate($result);
        $response['NewInstructorId'] = $pU->Attributes['UpdateRecordId'];
        $msg = 'New instructor successfully submitted and is awaiting approval for posting.';
    }
    else {
        $errors[] = "New instructor was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}
//set up response
$response['msg'] = $msg;
$response['errors'] = $errors;
//encode response as JSON and send it to the JS that called it
echo json_encode( $response );