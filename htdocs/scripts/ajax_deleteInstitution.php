<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/7/2019
 * Time: 12:23 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$InstId = filter_input( INPUT_POST, 'InstitutionId', FILTER_VALIDATE_INT );

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to delete institutions.';
}
else if (!$InstId) {
    $errors[] = 'Missing required parameter: InstitutionId.';
}
else {
    $inst = new Institution( $InstId );
    if (!$inst->valid) {
        $errors[] = 'The InstitutionId provided does not correspond to an existing program.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);

        if($user->id == 1){
            $inst->Attributes['Deleted'] = 1;
            //$inst->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_DELETED;
            $result = $inst->save();
            if ($result) {
                $msg = "Institution '{$inst->Attributes['InstitutionName']}' successfully marked as deleted.";
            }
            else {
                $errors[] = "Institution '{$inst->Attributes['InstitutionName']}' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $inst->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
            if ($result) {
                $msg = "Institution '{$inst->Attributes['InstitutionName']}' submitted for deletion.";
            }
            else {
                $errors[] = "Institution '{$inst->Attributes['InstitutionName']}' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );