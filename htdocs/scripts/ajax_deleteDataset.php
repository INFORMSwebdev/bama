<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/9/2019
 * Time: 11:13 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$DatasetId = filter_input( INPUT_POST, 'DatasetId', FILTER_VALIDATE_INT );

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to delete datasets.';
}
else if (!$DatasetId) {
    $errors[] = 'Missing required parameter: DatasetId.';
}
else {
    $data = new CaseStudy( $DatasetId );

    if (!$data->valid) {
        $errors[] = 'The DatasetId provided does not correspond to an existing data set.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);

        if($user->id == 1){
            $data->Attributes['Deleted'] = 1;
            //$data->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_DELETED;
            $result = $data->save();
            if ($result) {
                $msg = "Dataset '{$data->Attributes['DatasetName']}' successfully marked as deleted.";
            }
            else {
                $errors[] = "Dataset '{$data->Attributes['DatasetName']}' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $data->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
            if ($result) {
                $msg = "Dataset '{$data->Attributes['DatasetName']}' submitted for deletion.";
            }
            else {
                $errors[] = "Dataset '{$data->Attributes['DatasetName']}' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );