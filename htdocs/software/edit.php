<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 7:58 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to edit software information.';
    header('Location: /users/login.php');
    die;
}

//get current user
$user = new User($_SESSION['loggedIn']);

//get the software id
$softId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(isset($_SESSION['admin']) && $_SESSION['admin'] == true){
    $userSoft = Software::getAllSoftware();
}
else {
    $userSoft = $user->getSoftware();
}


if($softId){
    //make sure user has permission to edit this software
    if (!in_array($softId, $userSoft) && !isset($_SESSION['admin'])) {
        //set up the message to be red
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'You do not have permission to edit the specified software\'s information.';

        //redirect to index
        header('Location: /index.php');
        die;
    }

    $soft = new Software($softId);
    //get the details to pre-fill form
    $name = $soft->Attributes['SoftwareName'];
    $pub = $soft->Attributes['SoftwarePublisher'];

    //display the form for adding institution info to the user
    $content = <<<EOT
<div class="flex-column">
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <form action="../scripts/processSoftwareEditForm.php" method="POST">
        <div class="form-row">
            <h3>Software Details</h3>
        </div>
        <div class="form-row"> 
            <label for="name">Name</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="name" id="name" placeholder="Name of software" value="{$name}" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="publisher">Publisher</label>
            <input type="text" class="form-control" name="publisher" id="publisher" placeholder="Publishing company of the software" value="{$pub}" />
        </div>
        <br />
        <div class="form-row">
            <input type="hidden" id="softId" name="softId" value="{$softId}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This Software</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">These changes will not take effect until they have been approved by an INFORMS administrator..</p>
        </div>
    </form>
</div>
<div class="flex-column">
    <a href="/software/display.php?id={$soft->id}" role="button" class="btn btn-primary">View Software Details Page</a>
</div>
EOT;
}
else {
    //display list of software the user has permission to edit
    $softListHelper = array();
    foreach($userSoft as $s){
        if(isset($_SESSION['admin'])){
            $softListHelper[] = array('text' => $s['SoftwareName'], 'value' => $s['SoftwareId']);
        }
        else {
            $so = new Software($s);
            $softListHelper[] = array('text' => $so->Attributes['SoftwareName'], 'value' => $so->Attributes['SoftwareId']);
        }
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $softListHTML = optionsHTML($softListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>My Software</h2>
    <p>Inside the list below are all the software you have permissions to edit.</p>
</div>
<div class="flex-column">
    <h2>Edit Software Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Textbook">Select software to edit</label>
		    <select class="form-control" name="Textbook" id="Textbook" onchange="self.location='edit.php?id='+this.options[this.selectedIndex].value">
		        {$softListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Software";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();