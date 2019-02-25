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
} else {
    //get info for all programs this user is an editor of
    $progs = Program::getEditorPrograms($_SESSION['loggedIn']);

    if ($progs) {
        $response['success'] = 1;
        $helper = array();
        //append institution name to each returned program
        foreach ($progs as $prog) {
            $inst = new Institution($prog['InstitutionId']);
            $prog['InstitutionName'] = $inst->Attributes['InstitutionName'];
            $helper[] = $prog;
        }
        $response['programs'] = $helper;
    } else {
        $response['errors'][] = 'No programs returned from getEditorPrograms';
    }
}

//echo the response (as JSON) so the page that needs it can get the info from this script
echo json_encode($response);