<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/28/2019
 * Time: 2:31 PM
 */
//require the init file
require_once '../../init.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //gather form data
    $name = filter_input(INPUT_POST, 'datasetName', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'datasetType', FILTER_SANITIZE_STRING);
    $integrity = filter_input(INPUT_POST, 'datasetIntegrity', FILTER_SANITIZE_STRING);
    $fileType = filter_input(INPUT_POST, 'datasetFileType', FILTER_SANITIZE_STRING);
    $useDescription = filter_input(INPUT_POST, 'useDescription', FILTER_SANITIZE_STRING);
    $datasetAccess = filter_input(INPUT_POST, 'datasetAccess', FILTER_VALIDATE_URL);
    $analytics = filter_input(INPUT_POST, 'analyticTag', FILTER_SANITIZE_STRING);
    $business = filter_input(INPUT_POST, 'businessTag', FILTER_SANITIZE_STRING);

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
        'DatasetName' => $name,
        'DatasetType' => $type,
        'DatasetIntegrity' => $integrity,
        'DatasetFileType' => $fileType,
        'DatasetUseDescription' => $useDescription,
        'DatasetAccess' => $datasetAccess,
        'AnalyticTag' => $analytics,
        'BusinessTag' => $business
    );

    //create an object w/ no Id
    $x = Dataset::createInstance( $data );

    //add record to pending_updates
    $result = $x->createPendingUpdate( UPDATE_TYPE_INSERT, $user->Attributes['UserId']);

    //report on results of insertion
    if($result == true) {
        //set message to show user
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'New dataset successfully submitted and is awaiting approval for posting.';
    }
    else {
        //I can't think of why this case would ever happen, but just in case set the user to default ADMIN/system record
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = "New dataset was not added to the system. Please contact <a href='mailto:webdev@mail.informs.org'>webdev@mail.informs.org</a>.";
    }

    //redirect user to index
    header('Location: /index.php');
    die;
}