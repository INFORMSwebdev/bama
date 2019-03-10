<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 7:58 AM
 */
//require the init file
require_once '../../init.php';

//get the softwareId
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

    $content .= <<<EOT
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

    $content .= <<<EOT
<div class="card">
    <div class="card-header">
        <h2 class="display2">{$name}</h2>
    </div>
    <div class="card-body"> 
        <h3>Publisher</h3>
        <p>{$pub}</p>
        <div class="btn-group">
            <a role="button" class="btn btn-warning mr-3" href="/software/edit.php?id={$soft->id}">Edit this Software</a>
            <button id="id_{$soft->id}" name="courseDelete" type="submit" class="btn btn-danger btn-software-delete">Delete this Software</button>
        </div>
    </div>
</div>
EOT;
}

$customJS = <<<EOT
$(function() {
    //software delete button functionality
    $(document).on( 'click', '.btn-software-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this software?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteSoftware.php", { 'SoftwareId': id }, function(data) {
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
$page_params['js'][] = array( 'text' => $customJS );
$page_params['page_title'] = "View Software Details";
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