<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/27/2019
 * Time: 4:03 PM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");
$response = [];
$msg = '';
$errors = [];
$PendingUserId = filter_input( INPUT_POST, 'PendingUserId', FILTER_SANITIZE_NUMBER_INT);
if (!$PendingUserId) $errors[] = "Missing required parameter: PendingUserId";
$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_NUMBER_INT );
if (!$action) $errors[] = "Missing required parameter: action";

if (!count($errors)) {
    $PendingUser = new PendingUser( $PendingUserId );
    try {
        $PendingUser->approvalAction( $action );
        $msg = "The user was " . (($action==APPROVAL_TYPE_APPROVE) ? "approved." : "rejected.");
    }
    catch (Exception $e) {
        $errors[] = $e->getMessage();
    }

}

$response['errors'] = $errors;
$response['msg'] = $msg;
echo json_encode( $response );