<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/10/2019
 * Time: 1:45 AM
 */
//init file
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$ContactId = filter_input( INPUT_POST, 'ContactId', FILTER_VALIDATE_INT );
$ProgramId = filter_input( INPUT_POST, 'ProgramId', FILTER_VALIDATE_INT );

if (!$ContactId) $errors[] = "Missing required parameter: ContactId";
if (!$ProgramId) $errors[] = "Missing required parameter: ProgramId";
else {
    $contact = new Contact( $ContactId );
    $program = new Program ($ProgramId);

    if (!$contact->valid) $errors[] = "The ContactId provided does not correspond to an existing contact.";

    if(!$program->valid) $errors [] = "The ProgramId provided does not correspond to an existing program.";
    else {
        //get the userId
        if(isset($_SESSION['loggedIn'])){
            $user = new User($_SESSION['loggedIn']);
        }
        else {
            //this should never happen, but just in case:
            $user = new User(1);
        }
        $result = $program->assignContact($contact->id);
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