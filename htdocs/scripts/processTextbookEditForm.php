<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 2:35 PM
 */
//require the init file
require_once '../../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //check which button was pushed
    if (isset($_POST['edit'])) {
        //edit button clicked, make sure Deleted flag is 0
        $bookDeleted = 0;
    } else if (isset($_POST['delete'])) {
        //delete button was clicked, set the Deleted flag to 1
        $bookDeleted = 1;
    }

    //get all the form data submitted
    $name = filter_input(INPUT_POST, 'textbookName', FILTER_SANITIZE_STRING);
    $authors = filter_input(INPUT_POST, 'textbookAuthors', FILTER_SANITIZE_STRING);
    $pub = filter_input(INPUT_POST, 'textbookPublisher', FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_POST, 'textbookId', FILTER_VALIDATE_INT);

    //get the record to update
    $book = new Textbook($id);

    //update the attributes
    $book->Attributes['TextbookName'] = $name;
    $book->Attributes['Authors'] = $name;
    $book->Attributes['TextbookPublisher'] = $name;

    //get the users Id to put in the table
    if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
        $user = new User($_SESSION['loggedIn']);
    } else {
        //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
        $user = new User(1);
    }

    //put the updates in the pending_updates table
    $result = $book->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->Attributes['UserId']);

    if($result == true) {
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'Textbook update successfully submitted and is awaiting approval for posting.';
    }
    else {
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "Textbook update failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}
//redirect user to index?
header('Location: /index.php');
die;