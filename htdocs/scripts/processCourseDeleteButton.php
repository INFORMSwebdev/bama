<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/7/2019
 * Time: 10:29 AM
 */
//require the init file
require_once '../../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //get the course record
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

    //make sure we have a value to work with
    if(!$id){
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'No valid CourseId supplied to delete.';
        header('Location: /index.php');
        die;
    }

    $course = new Course($id);
    //get the userId
    if(isset($_SESSION['loggedIn'])){
        $user = new User($_SESSION['loggedIn']);
    }
    else {
        //this should never happen, but just in case:
        $user = new User(1);
    }

    $result = $course->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

    if($result == true) {
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'Course submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
    }
    else {
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "Course delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }
}

//redirect user to index?
header('Location: /index.php');
die;