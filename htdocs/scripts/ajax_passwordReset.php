<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/21/2019
 * Time: 1:08 PM
 */

require_once( "../../init.php");
$response = [];
$msg = '';
$errors = [];
$email = filter_input( INPUT_POST, 'email');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
else {
    $User = User::getUserByEmail( $email );
    if ( $User->valid ) {
        $User->resetPassword();
        $msg = "A password reset message has been sent to $email";
    }
    else {
        $errors[] = "The email provided was not found in the user database.";
    }
}

$response['errors'] = $errors;
$response['msg'] = $msg;
echo json_encode( $response );