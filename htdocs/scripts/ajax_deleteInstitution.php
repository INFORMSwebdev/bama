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

if (!$InstId) $errors[] = "Missing required parameter: InstitutionId";
else {
    $inst = new Institution( $InstId );
    if (!$inst->valid) $errors[] = "The InstitutionId provided does not correspond to an existing program.";
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
        if ($result){
            $msg = "Institution '{$inst->Attributes['InstitutionName']}' submitted for deletion.";
        }
        else {
            $errors[] = "Institution '{$inst->Attributes['InstitutionName']}' could not be deleted, alert IT dept.";
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );