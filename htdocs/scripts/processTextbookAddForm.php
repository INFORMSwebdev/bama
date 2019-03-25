<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 1:58 PM
 */
//require the init file
require_once '../../init.php';

//get the userId who submitted the new textbook
if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
    $user = new User($_SESSION['loggedIn']);
}
else {
    $_SESSION['logoutMessage'] = 'You must be logged in to submit new textbooks.';
    header('Location: /users/login.php');
    die;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $courseId = filter_input(INPUT_POST, 'courseId', FILTER_VALIDATE_INT);

    //gather form data
    $name = filter_input(INPUT_POST, 'textbookName');
    $authors =  filter_input(INPUT_POST, 'textbookAuthors');
    $pub = filter_input(INPUT_POST, 'textbookPublisher');

    //set up the new objects' info
    $data = array(
        'TextbookName' => $name,
        'Authors' => $authors,
        'TextbookPublisher' => $pub
    );
    $x = Textbook::createInstance( $data );

    if($user->id == 1){
        $result = $x->save();
        if($result){
            $t = new Textbook($result);
            $t->update('ApprovalStatusId', APPROVAL_TYPE_APPROVE);
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New textbook successfully added.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New textbook was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //add record to pending_updates
        $result = $x->createPendingUpdate(UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

        //check to make sure the insert occurred successfully
        if ($result == true) {

            $update = new PendingUpdate($result);

            //assign textbook to course
            if ($courseId) {
                $course = new Course($courseId);
                $course->assignTextbook($update->Attributes['UpdateRecordId']);
            }

            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New textbook successfully submitted and is awaiting approval for posting.';
        } else {
            //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New textbook was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}
//redirect user to index
header('Location: /index.php');
die;