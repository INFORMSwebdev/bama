<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/28/2019
 * Time: 2:29 PM
 */
//require the init file
require_once '../../init.php';

//get the courseId
$dataId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

//check to make sure we have an Id to work with
if(empty($dataId)) {
    //no valid Id supplied in query string
    //display a list of datasets to select from
    $sets = Dataset::getAllDatasets();
    $dataListHelper = array();
    foreach($sets as $s){
        $dataListHelper[] = array('text' => $s['DatasetName'], 'value' => $s['CourseId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $setListHTML = optionsHTML($dataListHelper);

}
else {
    //get details of dataset
}