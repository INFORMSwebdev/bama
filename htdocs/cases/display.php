<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 9:56 AM
 */
//require the init file
require_once '../../init.php';

//get the caseId
$caseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

//check to make sure we have an Id to work with
if(empty($caseId)) {
    //get list of case studies to display
    $cases = CaseStudy::getCaseStudies();
    $caseListHelper = array();
    foreach($cases as $s){
        $caseListHelper[] = array('text' => $s['CaseTitle'], 'value' => $s['CaseId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $caseListHTML = optionsHTML($caseListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>View Case Study Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="course">Select a Case Study</label>
		    <select class="form-control" name="dataset" id="dataset" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        {$caseListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}
else {
    //display info about the specified case study
    $case = new CaseStudy($caseId);
    $title = $case->Attributes['CaseTitle'];
    $type = $case->Attributes['CaseType'];
    if(!isset($type) || empty($type)){
        $type = 'No type information currently available for this case study.';
    }
    $useDesc = $case->Attributes['CaseUseDescription'];
    if(!isset($useDesc) || empty($useDesc)){
        $useDesc = 'No use description currently available for this case study.';
    }
    $access = $case->Attributes['CaseAccess'];
    if(!isset($access) || empty($access)){
        $access = 'No access information currently available for this case study.';
    }
    $analytics = $case->Attributes['AnalyticTag'];
    if(!isset($analytics) || empty($analytics)){
        $analytics = 'No analytics tags currently available for this case study.';
    }
    $business = $case->Attributes['BusinessTag'];
    if(!isset($business) || empty($business)){
        $business = 'No business tags currently available for this case study.';
    }

    $content = <<<EOT
<div class="card">
    <div class="card-header"> 
        <h2 class="display2">{$title}</h2>
    </div>
    <div class="card-body"> 
        <h3>Type</h3>
        <p>{$type}</p>
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
$page_params['page_title'] = "View Case Study Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
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