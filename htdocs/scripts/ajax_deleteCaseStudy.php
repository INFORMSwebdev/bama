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

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to delete case studies.';
}
else if (!$CaseId) {
    $errors[] = 'Missing required parameter: CaseId.';
}
else {
    $case = new CaseStudy( $CaseId );
    if (!$case->valid) {
        $errors[] = 'The CaseId provided does not correspond to an existing case study.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);

        if($user->id == 1){
            $case->Attributes['Deleted'] = 1;
            $result = $case->save();
            if ($result) {
                $msg = "Case study '{$case->Attributes['CaseTitle']}' successfully marked as deleted.";
            }
            else {
                $errors[] = "Case study '{$case->Attributes['CaseTitle']}' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $case->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
            if ($result) {
                $msg = "Case study '{$case->Attributes['CaseTitle']}' submitted for deletion.";
            }
            else {
                $errors[] = "Case study '{$case->Attributes['CaseTitle']}' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );