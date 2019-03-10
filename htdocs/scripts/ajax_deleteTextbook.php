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

if (!$TextbookId) $errors[] = "Missing required parameter: TextbookId";
else {
    $book = new Textbook( $TextbookId );
    if (!$book->valid) $errors[] = "The TextbookId provided does not correspond to an existing textbook.";
    else {
        //get the userId
        if(isset($_SESSION['loggedIn'])){
            $user = new User($_SESSION['loggedIn']);
        }
        else {
            //this should never happen, but just in case:
            $user = new User(1);
        }
        $result = $book->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
        if ($result){
            $msg = "Textbook '{$book->Attributes['TextbookName']}' submitted for deletion.";
        }
        else {
            $errors[] = "Textbook '{$book->Attributes['TextbookName']}' could not be deleted, alert IT dept.";
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );