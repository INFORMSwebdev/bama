<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 2:35 PM
 */
//require the init file
require_once '../../init.php';

$id = filter_input(INPUT_POST, 'textbookId', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = filter_input(INPUT_POST, 'textbookId', FILTER_VALIDATE_INT);
    //get the record to update
    $book = new Textbook($id);

    //get the users Id to put in the table
    if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
        $user = new User($_SESSION['loggedIn']);
    } else {
        //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
        $user = new User(1);
    }

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

        //put the updates in the pending_updates table
        $result = $book->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

        if ($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'Textbook update successfully submitted and is awaiting approval for posting.';
        } else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "Textbook update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}
//redirect user to index?
header("Location: /textbooks/display.php?id=$id");
die;