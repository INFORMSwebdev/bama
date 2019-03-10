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

if (!$DatasetId) $errors[] = "Missing required parameter: DatasetId";
else {
    $data = new CaseStudy( $DatasetId );
    if (!$data->valid) $errors[] = "The DatasetId provided does not correspond to an existing data set.";
    else {
        //get the userId
        if(isset($_SESSION['loggedIn'])){
            $user = new User($_SESSION['loggedIn']);
        }
        else {
            //this should never happen, but just in case:
            $user = new User(1);
        }
        $result = $data->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
        if ($result){
            $msg = "Dataset '{$data->Attributes['DatasetName']}' submitted for deletion.";
        }
        else {
            $errors[] = "Dataset '{$data->Attributes['DatasetName']}' could not be deleted, alert IT dept.";
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );