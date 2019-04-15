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
    $_SESSION['editMessage']['text'] = 'A valid ContactId was not supplied to edit.';
    header('Location: index.php');
    die;
}

$content = '';

//get the contact info to display
$con = new Contact($contactId);
$name = $con->Attributes['ContactName'];
$title = $con->Attributes['ContactTitle'];
$phone = $con->Attributes['ContactPhone'];
$email = $con->Attributes['ContactEmail'];

$content = <<<EOT
<div class="flex-column">
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <form action="../scripts/processContactEditForm.php" method="POST">
        <div class="form-row">
            <h3>Contact Details</h3>
        </div>
        <div class="form-row"> 
            <label for="name">Name</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="name" id="name" placeholder="First and last name of contact" value="{$name}" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="lastName">Title</label>
            <input type="text" class="form-control" name="title" id="title" placeholder="Last name/surname of instructor" value="{$title}" />
        </div>
        <br />
        <div class="form-row">
            <label for="prefix">Phone</label>
		    <input type="text" class="form-control" name="phone" id="phone" placeholder="e.g. Mister, Mr. Professor, Doctor, etc." value="{$phone}" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="email">Email</label>
            <input type="text" class="form-control" name="email" id="email" placeholder="Email address of contact" aria-describedby="emailHelp" value="{$email}" />
            <p id="emailHelp">Only valid email addresses will be accepted (e.g. name@organization.com)</p>
        </div>
        <!--<br />-->
        <div class="form-row">
            <input type="hidden" id="contactId" name="contactId" value="{$contactId}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete" id="id_{$contactId}">Delete This Contact</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">These changes will not be displayed until they are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
<div class="flex-column">
    <a href="/contacts/display.php?id={$con->id}" role="button" class="btn btn-primary">View Contact Details Page</a>
</div>
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Contact";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();