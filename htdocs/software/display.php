<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 7:58 AM
 */
//require the init file
require_once '../../init.php';

//get the caseId
$softwareId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

//check to make sure we have an Id to work with
if(empty($softwareId)) {
    //get list of software to display
    $softwares = Software::getAllSoftware();
    $softListHelper = array();
    foreach($softwares as $s){
        $softListHelper[] = array('text' => $s['SoftwareName'], 'value' => $s['SoftwareId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $softListHTML = optionsHTML($softListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>View Software Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="course">Select Software</label>
		    <select class="form-control" name="dataset" id="dataset" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        {$softListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}
else {
    //display info about the specified software
    $soft = new Software($softwareId);
    $name = $soft->Attributes['SoftwareName'];
    $pub = $soft->Attributes['SoftwarePublisher'];
    if(!isset($pub) || empty($pub)){
        $pub = 'No publisher information currently available for this software.';
    }

    $content = <<<EOT
<div class="card">
    <div class="card-header">
        <h2 class="display2">{$name}</h2>
    </div>
    <div class="card-body"> 
        <h3>Publisher</h3>
        <p>{$pub}</p>
    </div>
</div>
EOT;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "View Software Details";
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