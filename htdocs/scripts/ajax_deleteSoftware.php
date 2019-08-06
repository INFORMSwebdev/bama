<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/9/2019
 * Time: 10:51 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$SoftwareId = filter_input( INPUT_POST, 'SoftwareId', FILTER_VALIDATE_INT );

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to delete instructors.';
}
else if (!$SoftwareId) {
    $errors[] = 'Missing required parameter: SoftwareId.';
}
else {
    $soft = new Software( $SoftwareId );
    if (!$soft->valid) {
        $errors[] = 'The SoftwareId provided does not correspond to an existing software.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);

        if($user->id == 1){
            $soft->Attributes['Deleted'] = 1;
            //$soft->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_DELETED;
            $result = $soft->save();

            if ($result){
                $msg = "Software '{$soft->Attributes['SoftwareName']}' successfully marked as deleted.";
            }
            else {
                $errors[] = "Software '{$soft->Attributes['SoftwareName']}' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $soft->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
            if ($result) {
                $msg = "Software '{$soft->Attributes['SoftwareName']}' submitted for deletion.";
            }
            else {
                $errors[] = "Software '{$soft->Attributes['SoftwareName']}' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );