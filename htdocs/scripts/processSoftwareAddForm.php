<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 2:45 PM
 */
//require the init file
require_once '../../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //gather form data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $pub = filter_input(INPUT_POST, 'publisher', FILTER_SANITIZE_STRING);

    //get user info
    if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
        $user = new User($_SESSION['loggedIn']);
    }
    else {
        //I don't think this should ever be hit, but just in case:
        $user = new User(1);
    }

    //get the form data into an array to create an object
    $data = array(
        'SoftwareName' => $name,
        'SoftwarePublisher' => $pub
    );
    //create an object w/ no Id
    $x = Software::createInstance( $data );
    //add record to pending_updates
    $result = $x->createPendingUpdate( UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

    //report on results of insertion
    if($result == true) {
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'New software successfully submitted and is awaiting approval for posting.';
    }
    else {
        //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "New software was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }

    //redirect user to index
    header('Location: /index.php');
    die;
}