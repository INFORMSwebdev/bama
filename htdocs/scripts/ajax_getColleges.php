<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/12/2019
 * Time: 12:08 PM
 */
//include the init file
require_once '../../init.php';

//set up response variables
$response = [];
$response['errors'] = [];

if(!isset($_SESSION['loggedIn'])){
    //user is not logged in
    $response['errors'][] = 'You must log in in order to get a list of assignable colleges.';
}
else {
    //get user
    $user = new User($_SESSION['loggedIn']);

    //get InstitutionId
    $id = filter_input(INPUT_GET, 'InstitutionId', FILTER_VALIDATE_INT);

    if($id) {
        $inst = new Institution($id);
        if (!$inst->valid) {
            $errors[] = "The InstitutionId provided does not correspond to an existing institution.";
        }
        else {
            $colleges = [];
            //get list of colleges under the passed institution
            $colHelp = $inst->getColleges();
            foreach($colHelp as $foo){
                $derp = [];
                $derp['CollegeId'] = $foo->Attributes['CollegeId'];
                $derp['CollegeName'] = $foo->Attributes['CollegeName'];
                $colleges[] = $derp;
            }

            $response['success'] = 1;

            //set up response to have the list
            $response['colleges'] = $colleges;
        }
    }
    else {
        $response['errors'][] = 'An institution Id is required in order to get colleges for selecting.';
    }
}

//echo the response (as JSON) so the page that needs it can get the info from this script
echo json_encode($response);