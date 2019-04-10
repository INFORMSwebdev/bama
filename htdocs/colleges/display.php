<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/10/2019
 * Time: 12:17 AM
 */
//require the init file
require_once '../../init.php';

//get the college id
$collegeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

$college = new College($collegeId);

//get info to display
$instId = $college->Attributes['InstitutionId'];
$name = $college->Attributes['CollegeName'];
$type = $college->Attributes['CollegeType'];
if(!empty($instId)) {
    $inst = new Institution($instId);
    $instName = $inst->Attributes['InstitutionName'];
}
else {
    $instName = 'Not currently assigned to an institution.';
}

$content .= <<<EOT
<div class="card">
    <div class="card-header">
        <h2 class="display2">{$name}</h2>
    </div>
    <div class="card-body"> 
        <h3>Type of College</h3>
        <p>{$type}</p>
        <h3>Part of Institution</h3>
        <p>{$instName}</p>
        <div class="btn-group">
            <a role="button" class="btn btn-warning mr-3" href="/colleges/edit.php?id={$college->id}">Edit this College</a>
            <button id="id_{$college->id}" name="collegeDelete" type="submit" class="btn btn-danger btn-college-delete">Delete this College</button>
        </div>
    </div>
</div>
EOT;

$customJS = <<<EOT
$(function() {
    //college delete button functionality
    $(document).on( 'click', '.btn-college-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this college?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteCollege.php", { 'CollegeId': id }, function(data) {
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
$page_params['page_title'] = "View College Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();