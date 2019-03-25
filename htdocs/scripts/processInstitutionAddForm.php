<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 11:47 AM
 */
//require the init file
require_once '../../init.php';

//get user info
if(isset($_SESSION['loggedIn']) && is_numeric($_SESSION['loggedIn'])){
    $user = new User($_SESSION['loggedIn']);
}
else {
    $_SESSION['logoutMessage'] = 'You must be logged in to submit new institutions.';
    header('Location: /users/login.php');
    die;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //gather form data
    $name = filter_input(INPUT_POST, 'institutionName', FILTER_SANITIZE_STRING);
    $addr = filter_input(INPUT_POST, 'institutionAddress', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
    $zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
    $region = filter_input(INPUT_POST, 'region', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $access = filter_input(INPUT_POST, 'access', FILTER_VALIDATE_URL);

    //get the form data into an array to create an object
    $data = array(
        'InstitutionName' => $name,
        'InstitutionAddress' => $addr,
        'InstitutionCity' => $city,
        'InstitutionState' => $state,
        'InstitutionZip' => $zip,
        'InstitutionRegion' => $region,
        'InstitutionPhone' => $phone,
        'InstitutionAccess' => $access,
        'InstitutionEmail' => $email
    );
    //create an object w/ no Id
    $x = Institution::createInstance( $data );

    if($user->id == 1){
        $result = $x->save();
        if($result){
            $inst = new Institution($result);
            $inst->update('ApprovalStatusId', APPROVAL_TYPE_APPROVE);
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New institution successfully added.';
        }
        else {
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New institution was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
    else {
        //add record to pending_updates
        $result = $x->createPendingUpdate(UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

        //report on results of insertion
        if ($result == true) {
            //set message to show user
            $_SESSION['editMessage']['success'] = true;
            $_SESSION['editMessage']['text'] = 'New institution successfully submitted and is awaiting approval for posting.';
        } else {
            //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = "New institution was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
        }
    }
}
//redirect user to index
header('Location: /index.php');
die;