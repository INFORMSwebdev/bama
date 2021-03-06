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

//get info for all programs
$progs = Program::getAnalyticsPrograms();

if($progs){
    $response['success'] = 1;
    $helper = array();
    //append institution name to each returned program
    foreach($progs as $prog){
        $inst = new Institution($prog['InstitutionId']);
        $prog['InstitutionName'] = $inst->Attributes['InstitutionName'];
        $helper[] = $prog;
    }
    $response['programs'] = $helper;
} else {
    $response['success'][] = 'No programs returned from getAnalyticPrograms';
}

//echo the response (as JSON) so the page that needs it can get the info from this script
echo json_encode($response);