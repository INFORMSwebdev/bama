<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/22/2019
 * Time: 1:53 PM
 */
//include the init file
require_once '../../init.php';

//set up response variables
$response = [];
$response['errors'] = [];

//get info for all programs
$progs = Program::getORPrograms();

if($progs){
    $response['success'] = 1;
    $response['programs'] = $progs;
}
else if(empty($progs)){
    $response['errors'][] = 'There are currently no programs marked as Operations Research in the system.';
}
else {
    $response['errors'][] = 'No programs returned from getORPrograms';
}

//echo the response (as JSON) so the page that needs it can get the info from this script
echo json_encode($response);