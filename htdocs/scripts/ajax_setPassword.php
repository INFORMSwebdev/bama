<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/25/2019
 * Time: 12:31 PM
 */

require_once( "../../init.php");
$response = [];
$result = 0;
$errors = [];
$password = filter_input( INPUT_POST, 'password');
$password_confirm = filter_input( INPUT_POST, 'password_confirm');

// do server-side validation in case javascript turned off at client side
if (!$password) $errors[] = "The password field is required.";
if (strlen($password) < 8) $errors[] = "The password must be at least 8 characters in length.";
if (!$password_confirm) $errors[] = "The password confirmation field is required.";
if ($password != $password_confirm) $errors[] = "The password confirmation field did not match the password field entry.";
if (!isset($_SESSION["password_set_for"])) $errors[] = "There is a system problem, please let us know you received this message.";

if (!count($errors)) {
  $User = new User( $_SESSION["password_set_for"] );
  $result = $User->update( "Password", password_hash( $password, PASSWORD_DEFAULT ));
  $_SESSION["password_set_for"] = NULL;
}

$response['errors'] = $errors;
$response['result'] = $result;
echo json_encode( $response );