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

    $content .= <<<EOT
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
    //add in check for HTTP/HTTPS
    //if()
    $accHelp = $access;
    $access = '<a href="' . $accHelp . '">' . $accHelp . '</a>';
    $analytics = $dataset->Attributes['AnalyticTag'];
    if(empty($analytics)){
        $analytics = 'Analytic tags are currently unavailable.';
    }
    $business = $dataset->Attributes['BusinessTag'];
    if(empty($business)){
        $business = 'Business tags are currently unavailable.';
    }

    if(isset($_SESSION['editMessage'])){
        //set up the alert color
        if($_SESSION['editMessage']['success'] == true){
            //successful insert into pending_updates table
            $content = '<div class="alert alert-success" id="message">';
        }
        else {
            //unsuccessful insert
            $content = '<div class="alert alert-danger" id="message">';
        }
        //add message to alert
        $content .= "<p>{$_SESSION['editMessage']['text']}</p></div>";

        //clear out the session variable after its' use
        $_SESSION['editMessage'] = null;
    }

    $content .= <<<EOT
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
        <div class="btn-group">
            <a role="button" class="btn btn-warning mr-3" href="/datasets/edit.php?id={$dataset->id}">Edit this Dataset</a>
            <button id="id_{$dataset->id}" name="instructorDelete" class="btn btn-danger btn-dataset-delete">Delete this Dataset</button>
        </div>
    </div>
</div>
EOT;
}

//set up the custom JS
$customJS = <<<EOT
$(function() {    
    //dataset delete button functionality
    $(document).on( 'click', '.btn-dataset-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this course?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteDataset.php", { 'DatasetId': id }, function(data) {
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
$page_params['page_title'] = "View Dataset Details";
$page_params['js'][] = array( 'text' => $customJS );
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();