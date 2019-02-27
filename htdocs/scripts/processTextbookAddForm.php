<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 1:58 PM
 */
//require the init file
require_once '../../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //gather form data
    $name = filter_input(INPUT_POST, 'textbookName');
    $authors =  filter_input(INPUT_POST, 'textbookAuthors');
    $pub = filter_input(INPUT_POST, 'textbookPublisher');
    //get the userId who submitted the new textbook
    if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
        $user = new User($_SESSION['loggedIn']);
    } else {
        //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
        $user = new User(1);
    }

    //set up the new objects' info
    $data = array(
        'TextbookName' => $name,
        'Authors' => $authors,
        'TextbookPublisher' => $pub
    );
    $x = Textbook::createInstance( $data );
    //add record to pending_updates
    $result = $x->createPendingUpdate( UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

    //check to make sure the insert occurred successfully
    if($result == true) {
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'New textbook successfully submitted and is awaiting approval for posting.';
    }
    else {
        //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "New textbook was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }

    //redirect user to index
    header('Location: /index.php');
    die;
}