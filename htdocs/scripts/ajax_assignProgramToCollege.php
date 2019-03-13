<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/12/2019
 * Time: 3:22 PM
 */
//include the init file
require_once '../../init.php';

//set up response variables
$response = [];
$response['errors'] = [];

if(!isset($_SESSION['loggedIn'])){
    //user is not logged in
    $response['errors'][] = 'You must log in in order to assign a program to a college.';
}
else {
    //get user
    $user = new User($_SESSION['loggedIn']);

    //get InstitutionId
    $progId = filter_input(INPUT_POST, 'ProgramId', FILTER_VALIDATE_INT);

    //get the CollegeId
    $colId = filter_input(INPUT_POST, 'CollegeId', FILTER_VALIDATE_INT);

    //make objects from passed Id's
    $prog = new Program($progId);
    $college = new College($colId);

    //check to make sure we got valid record id's
    if (!$prog->valid) {
        $response['errors'][] = "The ProgramId provided does not correspond to an existing program.";
    }
    else if(!$college->valid){
        $response['errors'][] = "The CollegeId provided does not correspond to an existing college.";
    }
    else {
        //assign the program Id to the contact record
        $prog->Attributes['CollegeId'] = $college->id;

        $prog->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->id);

        //create the pending update
        $response['message'] = 'Program successfully assigned to college. Changes will be applied after approval by an INFORMS admin.';
    }
}

//echo the response (as JSON) so the page that needs it can get the info from this script
echo json_encode($response);