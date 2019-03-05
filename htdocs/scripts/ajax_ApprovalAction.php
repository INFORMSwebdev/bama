<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/2/2019
 * Time: 2:04 PM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$response = [];
$errors = [];
$msg = '';

$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_NUMBER_INT );
if (!$action) die ( "missing required parameter: action" );
$UpdateId = filter_input( INPUT_POST, 'UpdateId', FILTER_SANITIZE_NUMBER_INT );
if (!$UpdateId) die( "missing required parameter: UpdateId" );

$PendingUpdate = new PendingUpdate( $UpdateId );
try {
    $result = $PendingUpdate->approvalAction( $action );
    $actionDesc = ($action == APPROVAL_TYPE_APPROVE) ? "approved" : "rejected";
    $msg = "Update $actionDesc.";
}
catch (Exception $e) {
    $errors[] = $e->getMessage();
}

$response['errors'] = $errors;
$response['msg'] = $msg;
echo json_encode( $response );