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

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn']))
{
    $errors[] = 'You must be logged in to delete contacts.';
}
else if (!$ContactId) {
    $errors[] = 'Missing required parameter: ContactId.';
}
else {
    $contact = new Contact( $ContactId );

    if (!$contact->valid) {
        $errors[] = 'The ContactId provided does not correspond to an existing contact.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);

        if($user->id == 1){
            $contact->Attributes['Deleted'] = 1;
            //$contact->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_DELETED;
            $result = $contact->save();
            if($result){
                $msg = "Contact '{$contact->Attributes['ContactName']}' successfully marked as deleted.";
            }
            else {
                $errors[] = "Contact '{$contact->Attributes['ContactName']}' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $contact->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
            if ($result) {
                $msg = "Contact '{$contact->Attributes['ContactName']}' submitted for deletion.";
            }
            else {
                $errors[] = "Contact '{$contact->Attributes['ContactName']}' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );