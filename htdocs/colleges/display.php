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
$collegeId = filter_input_array(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

$college = new College($collegeId);

//get info to display
$instId = $college->Attributes['InstitutionId'];
$inst = new Institution($instId);
$name = $college->Attributes['CollegeName'];
$type = $college->Attributes['CollegeType'];

$content .= <<<EOT
<div class="card">
    <div class="card-header">
        <h2 class="display2">{$name}</h2>
    </div>
    <div class="card-body"> 
        <h3>Type of College</h3>
        <p>{$type}</p>
        <h3>Part of Institution</h3>
        <p>{$inst->Attributes['InstitutionName']}</p>
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