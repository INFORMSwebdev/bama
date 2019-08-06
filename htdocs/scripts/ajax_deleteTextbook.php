<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/9/2019
 * Time: 10:47 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$TextbookId = filter_input( INPUT_POST, 'TextbookId', FILTER_VALIDATE_INT );

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to delete instructors.';
}
else if (!$TextbookId) {
    $errors[] = 'Missing required parameter: TextbookId.';
}
else {
    $book = new Textbook( $TextbookId );
    if (!$book->valid) {
        $errors[] = 'The TextbookId provided does not correspond to an existing textbook.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);

        if($user->id == 1){
            $book->Attributes['Deleted'] = 1;
            //$book->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_DELETED;
            $result = $book->save();

            if ($result) {
                $msg = "Textbook '{$book->Attributes['TextbookName']}' successfully marked as deleted.";
            }
            else {
                $errors[] = "Textbook '{$book->Attributes['TextbookName']}' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $book->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
            if ($result) {
                $msg = "Textbook '{$book->Attributes['TextbookName']}' submitted for deletion.";
            }
            else {
                $errors[] = "Textbook '{$book->Attributes['TextbookName']}' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );