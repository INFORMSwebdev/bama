<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/26/2019
 * Time: 7:51 AM
 */

require_once( "../../init.php");
$response = [];
$errors = [];
$msg = '';

$FirstName = filter_input( INPUT_POST, 'firstName' );
$LastName = filter_input( INPUT_POST, 'lastName' );
$Username = filter_input( INPUT_POST, 'email' );
$Comments = filter_input( INPUT_POST, 'Comments' );
$InstitutionId = filter_input( INPUT_POST, 'inst', FILTER_SANITIZE_NUMBER_INT );

if (!$Username) $errors[] = "The email address is required.";
if (!filter_var( $Username, FILTER_VALIDATE_EMAIL)) $errors[] = "The email address provided is not a valid email address.";
if (User::getUserByEmail($Username, FALSE)) $errors[] = "There is already an account using that email address";

if (!count($errors)) {
    $UserId = User::create( ['Username'=>$Username, 'FirstName'=>$FirstName,'LastName'=>$LastName,'Comments'=>$Comments]);
    if (!$UserId) $errors[] = "Database insert failed, please alert IT Dept.";
    else {
        $User = new User( $UserId );
        if ($InstitutionId) $User->assignToInstitution( $InstitutionId );
        $result = $User->sendInviteEmail();
        if (!$result) $errors[] = "Invitation sending failed, alert IT Dept.";
        else $msg = "User $Username account created and invitation sent.";
    }
}

$response['errors'] = $errors;
$response['msg'] = $msg;
echo json_encode( $response );
