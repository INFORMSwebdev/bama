<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 10:30 AM
 */
//require the init file
require_once '../../init.php';

//get the instructorId
$instructorId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

//check to make sure we have an Id to work with
if(empty($instructorId)) {
    //get list of instructor to display
    $instructors = Instructor::getInstructors();
    $instListHelper = array();
    foreach($instructors as $s){
        $instListHelper[] = array('text' => $s['InstructorFirstName'] . ' ' . $s['InstructorLastName'], 'value' => $s['InstructorId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $instListHTML = optionsHTML($instListHelper);

    $content .= <<<EOT
<div class="flex-column">
    <h2>View Instructor Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="course">Select an Instructor</label>
		    <select class="form-control" name="dataset" id="dataset" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        {$instListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}
else {
    //display info about the specified instructor
    $instruc = new Instructor($instructorId);
    $firstName = $instruc->Attributes['InstructorFirstName'];
    $lastName = $instruc->Attributes['InstructorLastName'];
    $prefix = $instruc->Attributes['InstructorPrefix'];
    if(!isset($prefix) || empty($prefix)){
        $prefix = '';
    }
    $email = $instruc->Attributes['InstructorEmail'];
    $emailHTML = '';
    if(!isset($email) || empty($email)){
        $emailHTML = '<p>No email address on record for this instructor.</p>';
    }
    else {
        $emailHTML = "<p><a href='mailto:$email'>$email</a></p>";
    }

    $content .= <<<EOT
<div class="card">
    <div class="card-header"> 
        <h2 class="display2">Instructor Details</h2>
    </div>
    <div class="card-body"> 
        <h3>Prefix</h3>
        <p>{$prefix}</p>
        <h3>First Name</h3>
        <p>{$firstName}</p>
        <h3>Last Name</h3>
        <p>{$lastName}</p>
        <h3>Email</h3>
        {$emailHTML}
        <div class="btn-group">
            <a role="button" class="btn btn-warning mr-3" href="/instructors/edit.php?id={$instruc->id}">Edit this Instructor</a>
            <button id="id_{$instruc->id}" name="instructorDelete" class="btn btn-danger btn-instructor-delete">Delete this Instructor</button>
        </div>      
    </div>
</div>
EOT;
}

//set up the custom JS
$customJS = <<<EOT
$(function() {    
    //instructor delete button functionality
    $(document).on( 'click', '.btn-instructor-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this instructor?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteInstructor.php", { 'InstructorId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    //alert( msg );
                    $('#message').html('<p>' + msg + '</p>');
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
$page_params['page_title'] = "View Instructor Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();