<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/4/2019
 * Time: 1:51 PM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$response = [];
$errors = [];
$msg = '';

$InstitutionId = Institution::create( $_POST );
if ($InstitutionId) {
    $Institution = new Institution( $InstitutionId );
    $Name = $Institution->Attributes['InstitutionName'];
    $msg = "$Name has been added to the Institutions table.";
}
else {
    $errors[] = "The database insert failed, please notify the IT Dept.";
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );