<?php
//require the init file
require_once '../../init.php';

//get user info
if (!isset($_SESSION['loggedIn']) && !is_numeric($_SESSION['loggedIn'])) {
    $_SESSION['logoutMessage'] = 'You must be logged in to add contacts.';
    header('Location: /users/login.php');
    die;
}

$user = new User($_SESSION['loggedIn']);

$progId = filter_input(INPUT_POST, 'programId', FILTER_VALIDATE_INT);

if(empty($progId)){
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'Valid program Id must be passed to add new contacts.';
    header('Location: /index.php');
    die;
}

$prog = new Program($progId);

//gather form data
$name = filter_input(INPUT_POST, 'contactName', FILTER_SANITIZE_STRING);
$title = filter_input(INPUT_POST, 'contactTitle', FILTER_SANITIZE_STRING);
$phone = filter_input(INPUT_POST, 'contactPhone', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

$data = array(
    'ContactName' => $name,
    'ContactTitle' => $title,
    'ContactPhone' => $phone,
    'ContactEmail' => $email
);

$x = Contact::createInstance($data);

if($user->id == 1){
    $result = $x->save();
    if($result){
        $c = new Contact($result);
        $prog->assignContact($c->id);
        $c->update('ApprovalStatusId', APPROVAL_TYPE_APPROVE);
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'New contact successfully added and associated with program.';
    }
    else {
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "New contact was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}
else {
    //add record to pending_updates
    $result = $x->createPendingUpdate(UPDATE_TYPE_INSERT, $user->id);

    //report on results of insertion
    if ($result == true) {
        //assign the new contact to the program so when it gets approved it will show
        $prog->assignContact($x->id);

        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'New contact successfully submitted and assigned, and is awaiting approval for posting.';
    }
    else {
        //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "New contact was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}
//redirect user to index
header('Location: /index.php');
die;