<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/10/2019
 * Time: 1:20 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to assign a contact to a program.';
    header('Location: /users/login.php');
    die;
}

$progId = filter_input(INPUT_GET, 'progId', FILTER_VALIDATE_INT);

//make sure there is a valid program id to work with (i.e. it is present and is an integer)
if(!$progId){
    # ToDo: figure out what to do if not passed a valid ProgramId, and if an else statement is needed here for regular content
}

//get the program to work with
$prog = new Program($progId);

//get currently assigned contacts to pre-check
$programContacts = $prog->getContacts();

//make it easier to search the program contacts
$progConIds = [];
foreach($programContacts as $pc){
    $progConIds[] = $pc->id;
}

//get assignable contacts
$inst = new Institution($prog->Attributes['InstitutionId']);

//This is returned as an array of contacts for each program (i.e. the first level array is each program and inside each is a list of unique contacts for each program.
// But, what we need here is not to have duplicates appear if they are in separate programs
//So, the work around is going to be this:
// for each program, get contacts into a main array but after check for the contact already in array
$instContacts = $inst->getContacts();

//array of associative arrays
$finalContacts = [];

//loop through each program and add contacts to the finalContacts if they aren't already in it
foreach($instContacts as $progCon){
    //loop through each contact in this program and add them to the list if they aren't there yet
    foreach($progCon as $co){
        //check whether this contact is already in the final array
        if(array_search($co->id, array_column($finalContacts, 'ContactId')) === false) {
            //it is not already present
            //temporary helper array
            $foo = [];

            $foo['ContactName'] = $co->Attributes['ContactName'];
            $foo['ContactId'] = $co->id;

            //add contact to array
            $finalContacts[] = $foo;
        }
    }
}

//set up the checklist
$contactOptions = '';

foreach($finalContacts as $derp) {

    $contactId = $derp['ContactId'];
    $name = $derp['ContactName'];

    //check if this contact is currently assigned to the program and make that option pre-checked
    if(in_array($contactId, $progConIds)){
        $contactOptions .= <<<EOT
<div class="form-check">
    <input class="form-check-input" type="checkbox" name="contactChecklistOption[]" id="check_{$contactId}" value="{$contactId}" checked>
    <label for="check_{$contactId}">$name</label>
</div>
EOT;
    }
    else {
        $contactOptions .= <<<EOT
<div class="form-check">
    <input class="form-check-input" type="checkbox" name="contactChecklistOption[]" id="check_{$contactId}" value="{$contactId}">
    <label for="check_{$contactId}">$name</label>
</div>
EOT;
    }
}

$content = <<<EOT
<h2>Select Contacts From the Checklist</h2>
<p>Any checked contacts will be assigned to the {$prog->Attributes['ProgramName']} program. Any un-checked contacts will be unassigned from the program, if they are currently assigned.</p>
<div class="container-fluid">
    <form action="/scripts/processAssignProgramContactForm.php" method="POST">
        <div class="form-group">
            {$contactOptions}
        </div>
        <div class="form-group">
            <input type="hidden" id="progId" name="progId" value="{$prog->id}" />
            <button type="submit" class="btn btn-primary">Submit Assignments</button>
        </div>
    </form>
</div>
EOT;

$customJS = <<<EOT
$(function() {
    $('#contactTable').DataTable();
    
    //contact delete button functionality
    $(document).on( 'click', '.btn-contact-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this contact?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteContact.php", { 'ContactId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    alert( msg );
                    //$('#message').html('<p>' + msg + '</p>').removeClass('d-hidden').addClass('alert alert-danger');
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
    
    //contact select button functionality
    $(document).on( 'click', '.btn-contact-select', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to select this contact?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            var progId = ('#progId').val();
            $.post( "/scripts/ajax_selectContact.php", { 'ContactId': id, 'ProgramId': progId }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    alert( msg );
                    //$('#message').html('<p>' + msg + '</p>').removeClass('d-hidden').addClass('alert alert-danger');
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
$page_params['page_title'] = "Assign Contacts to Program";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
$page_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
//$page_params['js'][] = array( 'text' => $customJS );
//$page_params['js'][] = array( 'text' => $custom_js );
//$page_params['active_menu_item'] = 'home';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();