<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/22/2019
 * Time: 1:47 PM
 */
//include the init file
require_once '../../init.php';

//set up response variables
$response = [];
$response['errors'] = [];

if(!isset($_SESSION['loggedIn'])){
    //user is not logged in
    $response['errors'][] = 'You must log in in order to get a list of editable programs.';
}
else {
    $user = new User($_SESSION['loggedIn']);

    //get info for all institutions this user is an editor of
    $insts = $user->getInstitutions();

    if ($insts) {
        $response['success'] = 1;

        $helper = [];
        //check for and update the the response for null fields
        foreach($insts as $foo){
            if(empty($foo->Attributes['InstitutionRegion'])){
                $foo->Attributes['InstitutionRegion'] = 'Region information not set.';
            }

            if(empty($foo->Attributes['InstitutionPhone'])){
                $foo->Attributes['InstitutionPhone'] = 'Phone number not set.';
            }

            if(empty($foo->Attributes['InstitutionEmail'])){
                $foo->Attributes['InstitutionEmail'] = 'Email not set.';
            }

            if(empty($foo->Attributes['InstitutionAccess'])){
                $foo->Attributes['InstitutionAccess'] = 'Access link not set.';
            }
            $helper[] = $foo;
        }
        $response['institutions'] = $helper;
        // ToDo: only here for testing/debugging, remove later
        $user->getDatasets();
    }
    else {
        $response['errors'][] = 'No institutions returned from getInstitutions.';
    }
}

//echo the response (as JSON) so the page that needs it can get the info from this script
echo json_encode($response);