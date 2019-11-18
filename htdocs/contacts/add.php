<?php
//require the init file
require_once '../../init.php';

//get the program id that this contact will be associated with
$progId = filter_input(INPUT_GET, 'progId', FILTER_VALIDATE_INT);

if(empty($progId)){
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'A valid ProgramId was not supplied to associate the new contact with.';
    header('Location: index.php');
    die;
}
else {
    $prog = new Program($progId);

    $content = <<<EOT
<div class="flex-column">
    <p>This contact will be associated with the '{$prog->Attributes['ProgramName']}' program.</p>
</div>
<div class="container-fluid">
    <form action="/scripts/processContactAddForm.php" method="POST">
        <div class="form-row">
            <h3>Contact Details</h3>
        </div>
        <div class="form-row"> 
            <label for="contactName">Name</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="contactName" id="contactName" placeholder="Full mame of contact" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="contactTitle">Title</label>
            <input type="text" class="form-control" name="contactTitle" id="contactTitle" placeholder="Title of contact (if applicable)" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="contactPhone">Phone</label>
            <input type="text" class="form-control" name="contactPhone" id="contactPhone" placeholder="555-555-5555" aria-describedby="phoneHelp" />
            <small id="phoneHelp" class="form-text text-muted">Enter only digits, the phone number is formatted automatically.</small>
        </div>
        <br />
        <div class="form-row"> 
            <label for="email">Email</label>
            <input type="text" class="form-control" name="email" id="email" placeholder="name@organization.com" aria-describedby="emailHelp" />
            <p id="emailHelp">Only valid email addresses will be accepted.</p>
        </div>
        <div class="form-row">
            <input type="hidden" name="programId" id="programId" value="{$progId}" />
            <button class="btn btn-primary" type="submit" name="add" value="add">Add and Assign Contact</button>
        </div>
    </form>
</div>
EOT;
}

$maskJS = <<<EOT
$(function() {
    $('#contactPhone').inputmask('999-999-9999');
});
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add New Contact";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['js'][] = array('url' => 'https://rawgit.com/RobinHerbots/Inputmask/5.x/dist/jquery.inputmask.js' );
$page_params['js'][] = array('text' => $maskJS );
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();