<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/26/2019
 * Time: 11:25 AM
 */
//require the init file
require_once '../../init.php';

//get the contact id
$contactId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(empty($contactId)){
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'A valid ContactId was not supplied to display.';
    header('Location: index.php');
    die;
}
else {
    //get contact details to display
    $con = new Contact($contactId);
    $name = $con->Attributes['ContactName'];
    $title = $con->Attributes['ContactTitle'];
    if(empty($title)){
        $title = 'Title not set.';
    }
    $phone = $con->Attributes['ContactPhone'];
    if(empty($phone)){
        $phone = 'Phone not set.';
    }
    $email = $con->Attributes['ContactEmail'];
    if(empty($email)){
        $email = 'Email not set.';
    }
    else {
        $email = "<a href='mailto:$email'>$email</a>";
    }

    $content = <<<EOT
<div class="card">
    <div class="card-header"> 
        <h2 class="display2">Contact Details</h2>
    </div>
    <div class="card-body"> 
        <h3>Name</h3>
        <p>{$name}</p>
        <h3>Title</h3>
        <p>{$title}</p>
        <h3>Phone</h3>
        <p>{$phone}</p>
        <h3>Email</h3>
        <p>{$email}</p>
        <div class="btn-group">
            <a role="button" class="btn btn-warning mr-3" href="/contacts/edit.php?id={$con->id}">Edit this Contact</a>
            <button id="id_{$con->id}" name="contactDelete" class="btn btn-danger btn-contact-delete">Delete this Contact</button>
        </div>      
    </div>
</div>
EOT;

}

//set up the custom JS
$customJS = <<<EOT
$(function() {    
    //Contact delete button functionality
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
$page_params['page_title'] = "View Contact Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();