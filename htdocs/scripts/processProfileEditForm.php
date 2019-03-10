<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/4/2019
 * Time: 8:15 AM
 */
//require the init file
require_once '../../init.php';

//ensure we are processing only on a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userId = filter_input(INPUT_POST, 'userId', FILTER_VALIDATE_INT);
    //get user record
    $user = new User( $userId );

    //check which button was pushed
    if (isset($_POST['delete'])) {
        //delete button was clicked, create pending update

        $result = $user->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);

        if($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'User account submitted for deletion. This will be reflected after the deletion is approved by an INFORMS admin.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "User account delete failed. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //get form values
        $firstName = filter_input(INPUT_POST, 'FirstName', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        $lastName = filter_input(INPUT_POST, 'LastName', FILTER_SANITIZE_STRING);
        $userName = filter_input(INPUT_POST, 'Username', FILTER_SANITIZE_STRING);

        if ($user->Attributes['Username'] == $userName) {
            //username is the same, update the other info
            //editors should be able to update their own info w/o approval from INFORMS admins, so just update the record
            $updates = array(
                'FirstName' => $firstName,
                'LastName' => $lastName
            );
        } else {
            //username was changed, check to make sure its not currently in use
            if (User::usernameExists($userName)) {
                $_SESSION['editMessage']['success'] = false;
                $_SESSION['editMessage']['text'] = "Profile update failed. The new username you have picked is currently in use. Please choose a different username.";
                //redirect user to index?
                header('Location: /index.php');
                die;
            }
            //username is not taken, go ahead and update it too
            $updates = array(
                'FirstName' => $firstName,
                'LastName' => $lastName,
                'Username' => $userName
            );
        }

        //update the record info
        $user->updateMultiple($updates);

        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'You have successfully updated your profile information.';
    }
}
//redirect user to index?
header('Location: /index.php');
die;