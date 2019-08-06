<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/7/2019
 * Time: 11:00 AM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$CourseId = filter_input( INPUT_POST, 'CourseId', FILTER_VALIDATE_INT );

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to delete courses.';
}
else if (!$CourseId) {
    $errors[] = 'Missing required parameter: CourseId.';
}
else {
    $course = new Course( $CourseId );
    if (!$course->valid) {
        $errors[] = 'The CourseId provided does not correspond to an existing course.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);

        if($user->id == 1){
            $course->Attributes['Deleted'] = 1;
            //$course->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_DELETED;
            $result = $course->save();
            if($result){
                $msg = "Course '{$course->Attributes['CourseTitle']}' successfully marked as deleted.";
            }
            else {
                $errors[] = "Course '{$course->Attributes['CourseTitle']}' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $course->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
            if ($result) {
                $msg = "Course '{$course->Attributes['CourseTitle']}' submitted for deletion.";
            }
            else {
                $errors[] = "Course '{$course->Attributes['CourseTitle']}' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );