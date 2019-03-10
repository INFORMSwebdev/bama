<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/9/2019
 * Time: 11:12 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$CaseId = filter_input( INPUT_POST, 'CaseId', FILTER_VALIDATE_INT );

if (!$CaseId) $errors[] = "Missing required parameter: CaseId";
else {
    $case = new CaseStudy( $CaseId );
    if (!$case->valid) $errors[] = "The CaseId provided does not correspond to an existing case study.";
    else {
        //get the userId
        if(isset($_SESSION['loggedIn'])){
            $user = new User($_SESSION['loggedIn']);
        }
        else {
            //this should never happen, but just in case:
            $user = new User(1);
        }
        $result = $case->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
        if ($result){
            $msg = "Case study '{$case->Attributes['CaseTitle']}' submitted for deletion.";
        }
        else {
            $errors[] = "Case study '{$case->Attributes['CaseTitle']}' could not be deleted, alert IT dept.";
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );