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

    $content .= <<<EOT
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

    $content .= <<<EOT
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
        <div class="btn-group">
            <a role="button" class="btn btn-warning mr-3" href="/cases/edit.php?id={$case->id}">Edit this Case Study</a>
            <button id="id_{$case->id}" name="courseDelete" type="submit" class="btn btn-danger btn-case-delete">Delete this Case Study</button>
        </div>
    </div>
</div>
EOT;
}

$customJS = <<<EOT
$(function() {
    //case study delete button functionality
    $(document).on( 'click', '.btn-case-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this case study?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteCaseStudy.php", { 'CaseId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    //alert( msg );
                    $('#message').html('<p>' + msg + '</p>')
                    $('#message').addClass('alert alert-danger');
                    $('#message').show();
                }
                else if (data.msg) {
                    //alert( data.msg );
                    $('#message').html('<p>' + data.msg + '</p>');
                    if(data.msg.includes('submitted')){
                        $('#message').addClass('alert alert-success');
                    }
                    else {
                        $('#message').addClass('alert alert-danger');
                    }
                    $('#message').show();
                }
            }, "json");
        }
    });
});
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "View Case Study Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['js'][] = array( 'text' => $customJS );
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();