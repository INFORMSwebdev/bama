<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/21/2019
 * Time: 12:05 PM
 */
//include the init file
require_once '../../init.php';

//set up response variables
$response = [];
$response['errors'] = [];

//get info for all programs
$progs = Program::getAllPrograms();

if($progs){
    $response['success'] = 1;
    $response['programs'] = $progs;
} else {
    $response['errors'][] = 'No programs returned from getAllPrograms';
}

//echo the response (as JSON) so the page that needs it can get the info from this script
echo json_encode($response);