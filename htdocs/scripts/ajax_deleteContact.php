<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/10/2019
 * Time: 1:40 AM
 */
//require init
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$ContactId = filter_input( INPUT_POST, 'ContactId', FILTER_VALIDATE_INT );

if (!$ContactId) $errors[] = "Missing required parameter: ContactId";
else {
    $contact = new Contact( $ContactId );
    if (!$contact->valid) $errors[] = "The ContactId provided does not correspond to an existing contact.";
    else {
        //get the userId
        if(isset($_SESSION['loggedIn'])){
            $user = new User($_SESSION['loggedIn']);
        }
        else {
            //this should never happen, but just in case:
            $user = new User(1);
        }
        $result = $course->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
        if ($result){
            $msg = "Contact '{$contact->Attributes['ContactName']}' submitted for deletion.";
        }
        else {
            $errors[] = "Contact '{$contact->Attributes['ContactName']}' could not be deleted, alert IT dept.";
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );