<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/21/2019
 * Time: 1:08 PM
 */

require_once( "../../init.php");
$response = [];
$errors = [];
$email = filter_input( INPUT_POST, 'email');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";

$response['errors'] = $errors;
$response['msg'] = $msg;
echo json_encode( $response );