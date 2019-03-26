<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 2:35 PM
 */
//require the init file
require_once '../../init.php';

//get the users Id to put in the table
if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
    $user = new User($_SESSION['loggedIn']);
}
else{
    $_SESSION['logoutMessage'] = 'You must be logged in to submit textbook edits.';
    header('Location: /users/login.php');
    die;
}

$id = filter_input(INPUT_POST, 'textbookId', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = filter_input(INPUT_POST, 'textbookId', FILTER_VALIDATE_INT);
    //get the record to update
    $book = new Textbook($id);

    //check which button was pushed
    if (isset($_POST['delete'])) {
        //delete button was clicked, create pending update
        $result = $book->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Textbook submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Textbook delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //get all the form data submitted
        $name = filter_input(INPUT_POST, 'textbookName', FILTER_SANITIZE_STRING);
        $authors = filter_input(INPUT_POST, 'textbookAuthors', FILTER_SANITIZE_STRING);
        $pub = filter_input(INPUT_POST, 'textbookPublisher', FILTER_SANITIZE_STRING);

        //update the attributes
        $book->Attributes['TextbookName'] = $name;
        $book->Attributes['Authors'] = $name;
        $book->Attributes['TextbookPublisher'] = $name;

        if($user->id == 1){
            $book->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_APPROVE;
            $results = $book->save();
            if($results) {
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = 'Textbook successfully updated.';
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Textbook update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
        else {
            $book->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_NEW;
            //put the updates in the pending_updates table
            $result = $book->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->id);

            if ($result == true) {
                //set message to show user
                $_SESSION['editMessage']['success'] = true;
                $_SESSION['editMessage']['text'] = 'Textbook update successfully submitted and is awaiting approval for posting.';
            }
            else {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Textbook update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
            }
        }
    }
}
//redirect user to index?
header("Location: /textbooks/display.php?id={$id}");
die;