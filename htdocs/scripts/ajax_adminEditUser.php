<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/28/2019
 * Time: 3:26 PM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$response = [];
$errors = [];
$success = 0;

$UserId = filter_input( INPUT_POST, 'UserId', FILTER_SANITIZE_NUMBER_INT );
$FirstName = trim(filter_input( INPUT_POST, 'FirstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES ));
$LastName = trim(filter_input( INPUT_POST, 'LastName', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES ));
$Username = filter_input( INPUT_POST, 'Username', FILTER_SANITIZE_EMAIL );
$Comments = filter_input( INPUT_POST, 'Comments', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

if (!$UserId) $errors[] = "Missing required parameter: UserId";
if (!$FirstName) $errors[] = "The First Name field is required.";
if (!$LastName) $errors[] = "The Last Name field is required.";
if (!$Username) $errors[] = "The Username / Email Address field is required.";

if (!count($errors)) {
    $User = new User( $UserId );
    $exists = User::usernameExists( $Username, $UserId );
    if ($exists) $errors[] = "That Username / Email Address is already in use.";
    else {
        $User->update( 'FirstName', $FirstName );
        $User->update( 'LastName', $LastName );
        $User->update( 'Username', $Username );
        $User->update( 'Comments', $Comments );
        $success = 1;
    }
}

$response['errors'] = $errors;
$response['success'] = $success;
echo json_encode( $response );