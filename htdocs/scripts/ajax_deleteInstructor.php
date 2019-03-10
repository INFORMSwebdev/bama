<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/9/2019
 * Time: 8:52 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$InstructorId = filter_input( INPUT_POST, 'InstructorId', FILTER_VALIDATE_INT );

if (!$InstructorId) $errors[] = "Missing required parameter: InstructorId";
else {
    $inst = new Instructor( $InstructorId );
    if (!$inst->valid) $errors[] = "The InstructorId provided does not correspond to an existing course.";
    else {
        //get the userId
        if(isset($_SESSION['loggedIn'])){
            $user = new User($_SESSION['loggedIn']);
        }
        else {
            //this should never happen, but just in case:
            $user = new User(1);
        }
        $result = $inst->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
        $name = $inst->Attributes['InstructorFirstName'] . ' ' . $inst->Attributes['InstructorLastName'];
        if ($result){
            $msg = "Instructor '$name' submitted for deletion.";
        }
        else {
            $errors[] = "Instructor '$name' could not be deleted, alert IT dept.";
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );