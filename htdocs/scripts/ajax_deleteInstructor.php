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

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to delete instructors.';
}
else if (!$InstructorId) {
    $errors[] = 'Missing required parameter: InstructorId.';
}
else {
    $inst = new Instructor( $InstructorId );
    if (!$inst->valid) {
        $errors[] = 'The InstructorId provided does not correspond to an existing course.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);
        $name = $inst->Attributes['InstructorFirstName'] . ' ' . $inst->Attributes['InstructorLastName'];

        if($user->id == 1){
            $inst->Attributes['Deleted'] = 1;
            $inst->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_DELETED;
            $result = $inst->save();

            if ($result) {
                $msg = "Instructor '$name' successfully marked as deleted.";
            }
            else {
                $errors[] = "Instructor '$name' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $inst->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

            if ($result) {
                $msg = "Instructor '$name' submitted for deletion.";
            }
            else {
                $errors[] = "Instructor '$name' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );