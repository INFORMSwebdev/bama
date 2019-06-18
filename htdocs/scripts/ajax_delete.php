<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/4/2019
 * Time: 12:04 PM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$response = [];
$errors = [];
$msg = '';

$InstitutionId = filter_input( INPUT_POST, 'InstitutionId', FILTER_SANITIZE_NUMBER_INT );
$Value = filter_input( INPUT_POST, 'Value', FILTER_SANITIZE_NUMBER_INT );

$Institution = new Institution( $InstitutionId);
$result = $Institution->update( 'Deleted', $Value);
if ($result) {
    if ($Value) {
        $Institution->update( 'ApprovalStatusId', APPROVAL_TYPE_DELETED );
    }
    else {
        $Institution->update( 'ApprovalStatusId', APPROVAL_TYPE_APPROVE );
    }
}

$action = ($Value) ? "deleted" : "undeleted";
if ($result) $msg = "Institution $action.";
else $errors[] = "The database could not be updated, alert the IT dept.";

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );