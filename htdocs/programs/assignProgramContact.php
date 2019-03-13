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

$contacts = Contact::getAllContacts();

$tableRows = '';

foreach($contacts as $co){

    $c = new Contact($co['ContactId']);

    $name = $c->Attributes['ContactName'];
    $title = $c->Attributes['ContactTitle'];
    $phone = $c->Attributes['ContactPhone'];
    $email = $c->Attributes['ContactEmail'];

    $titleOut = $title;
    if(empty($title)){
        $titleOut = 'Not set';
    }

    $phoneOut = $phone;
    if(empty($phone)){
        $phoneOut = 'Not set';
    }

    $emailOut = 'Not set';
    if(!empty($email)){
        $emailOut = "<a href='mailto:$email'>$email</a>";
    }

    $tableRows .= <<<EOT
<tr>
    <td>{$name}</td>
    <td>{$titleOut}</td>
    <td>{$phoneOut}</td>
    <td>{$emailOut}</td>
    <td>
        <button id="id_{$c->id}" class="btn btn-primary btn-contact-select btn-block">Select this Contact</button>
        <a role="button" class="btn btn-warning btn-block" href="/contacts/edit.php?id={$c->id}">Edit this Contact</a>
        <button id="id_{$c->id}" name="contactDelete" class="btn btn-danger btn-block btn-contact-delete">Delete this Contact</button>
    </td>
</tr>
EOT;
}

$content = <<<EOT
<h2>Select a Contact From the Table</h2>
<p>Only 1 contact may be selected at a time.</p>
<input type="hidden" id="progId" value="{$progId}" />
<div class="container-fluid">
    <table class="table table-striped" id="contactTable">
        <thead> 
            <tr> 
                <th>Name</th>
                <th>Title</th>
                <th>Phone</th>
                <th>Email</th>
                <th></th>
            </tr>
        </thead>
        <tbody> 
            {$tableRows}
        </tbody>
    </table>
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