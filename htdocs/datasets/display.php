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
        $dataListHelper[] = array('text' => $s['DatasetName'], 'value' => $s['DatasetId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $setListHTML = optionsHTML($dataListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>View Dataset Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="course">Select a Dataset</label>
		    <select class="form-control" name="dataset" id="dataset" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        {$setListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}
else {
    //get details of dataset
    $dataset = new Dataset($dataId);
    $name = $dataset->Attributes['DatasetName'];
    $type = $dataset->Attributes['DatasetType'];
    $integrity = $dataset->Attributes['DatasetIntegrity'];
    $fileType = $dataset->Attributes['DatasetFileType'];
    $useDesc = $dataset->Attributes['DatasetUseDescription'];
    $access = $dataset->Attributes['DatasetAccess'];
    $analytics = $dataset->Attributes['AnalyticTag'];
    if(empty($analytics)){
        $analytics = 'Analytic tags are currently unavailable.';
    }
    $business = $dataset->Attributes['BusinessTag'];
    if(empty($business)){
        $business = 'Business tags are currently unavailable.';
    }

    $content = <<<EOT
<div class="card">
    <div class="card-header">   
        <h2 class="display2">{$name}</h2>
    </div>
    <div class="card-body">     
        <h3>Type</h3>
        <p>{$type}</p>
        <h3>Integrity</h3>
        <p>{$integrity}</p>
        <h3>File Type</h3>
        <p>{$fileType}</p>
        <h3>Use Description</h3>
        <p>{$useDesc}</p>
        <h3>Access</h3>
        <p>{$access}</p>
        <h3>Analytics Tags</h3>
        <p>{$analytics}</p>
        <h3>Business Tags</h3>
        <p>{$business}</p>
    </div>
</div>
EOT;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "View Dataset Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
//$page_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
//$page_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
//$page_params['js'][] = array( 'text' => $customJS );
//$page_params['js'][] = array( 'text' => $custom_js );
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
//$page_params['active_menu_item'] = 'home';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();