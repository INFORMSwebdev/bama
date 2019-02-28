<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/28/2019
 * Time: 11:31 AM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$response = [];
$errors = [];
$msg = '';

$UserId = filter_input( INPUT_POST, 'UserId', FILTER_SANITIZE_NUMBER_INT );
if (!$UserId) $errors[] = "Missing required parameter: UserId";
else {
    $User = new User( $UserId );
    if (!$User->valid) $errors[] = "The UserId provided does not correspond to an existing user.";
    else {
        $result = $User->update( "Deleted", 1);
        if ($result) $msg = "User deleted.";
        else $errors[] = "User could not be deleted, alert IT dept.";
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );