<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/25/2019
 * Time: 8:20 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true) {
    //set up a message to display on the login page
    $_SESSION['logoutMessage'] = 'Please log in to edit institution information.';
    //redirect to login page so user can log in
    header('Location: login.php');
    //don't want the script to keep executing after a redirect
    die;
}

//get the user
$user = new User($_SESSION['loggedIn']);

//get the institutionId
$instId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

//get the institutions this user has permission to edit
if(isset($_SESSION['admin']) && $_SESSION['admin'] == true){
    $userInsts = Institution::getInstitutions();
}
else {
    $userInsts = $user->getInstitutionAssignments();
}

//get the options maker, its gonna be needed
include_once('/common/classes/optionsHTML.php');

if($instId){
    //check if user has permission to edit this institution
    if (!in_array($instId, $userInsts) && !isset($_SESSION['admin'])) {
        //set up the message to be red
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'You do not have permission to edit the specified institution\'s information.';

        //redirect to index
        header('Location: /index.php');
        die;
    }

    //gather info
    $instObj = new Institution($instId);
    $name = $instObj->Attributes['InstitutionName'];
    $addr = $instObj->Attributes['InstitutionAddress'];
    $city = $instObj->Attributes['InstitutionCity'];
    $state = $instObj->Attributes['InstitutionState'];
    $zip = $instObj->Attributes['InstitutionZip'];
    $region = $instObj->Attributes['InstitutionRegion'];
    $phone = $instObj->Attributes['InstitutionPhone'];
    $email = $instObj->Attributes['InstitutionEmail'];
    $access = $instObj->Attributes['InstitutionAccess'];

    //get list of states
    $states = User::getStateList();
    $stateListHelper = array();
    foreach($states as $s){
        $stateListHelper[] = array('text' => $s['name'], 'value' => $s['abbr']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $stateListHTML = optionsHTML($stateListHelper);
    //since state is required, we don't need to check if there is already one set to make it the currently selected option
    $stateListHTML = str_replace('<option value="' . $state . '">', '<option value="' . $state . '" selected>', $stateListHTML);

    //display form w/ the specified institution info
    $content = <<<EOT
<div class="flex-column">
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <form action="../scripts/processInstitutionEditForm.php" method="POST">
        <div class="form-row">
            <h3>Institution Details</h3>
        </div>
        <div class="form-row"> 
            <label for="institutionName">Name</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="institutionName" id="institutionName" placeholder="Name of institution" value="{$name}" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="institutionAddress">Address</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="institutionAddress" id="institutionAddress" placeholder="Address of institution" aria-describedby="addressHelp" value="{$addr}" required />
            <p id="addressHelp">Please use the format: Number, Street Name, Suite (if applicable)</p>
        </div>
        <div class="form-row">
            <label for="city">City</label><span class="text text-danger">*</span>
		    <input type="text" class="form-control" name="city" id="city" placeholder="City where institution is located" value="{$city}" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="state">State</label><span class="text text-danger">*</span>
            <select class="form-control" name="state" id="state" required>
		        {$stateListHTML}
            </select>
        </div>
        <br />
        <div class="form-row"> 
            <label for="zip">Zip Code</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="zip" id="zip" placeholder="Zip code" value="{$zip}" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="region">Region</label>
            <input type="text" class="form-control" name="region" id="region" placeholder="Geographical region where institution is located" value="{$region}" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="phone">Phone</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="E.g. 555-555-5555 or (555) 555-5555" value="{$phone}" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="email">Email</label>
            <input type="text" class="form-control" name="email" id="email" placeholder="Email address to contact institution" value="{$email}" aria-describedby="emailHelp" />
            <p id="emailHelp">Only valid email addresses will be accepted (e.g. name@organization.com)</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="access">Access</label>
            <input type="text" class="form-control" name="access" id="access" placeholder="Website for the institution" value="{$access}" aria-describedby="accessHelp" />
            <p id="accessHelp">Only valid URLs will be accepted</p>
        </div>
        <!--<br />-->
        <div class="form-row">
            <input type="hidden" id="instId" name="instId" value="{$instId}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This Institution</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This institution will not be added to the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
<div class="flex-column">
    <a href="/institutions/display.php?id={$instObj->id}" role="button" class="btn btn-primary">View Institution Details Page</a>
</div>
EOT;
}
else {
    //institution id either not an integer or not present in query string

    //display a list of institutions to the user for them to select from THAT THEY HAVE PERMISSION TO EDIT
    $instListHelper = array();
    foreach($userInsts as $foo){
        if(isset($_SESSION['admin'])){
            $instListHelper[] = array('text' => $foo['InstitutionName'], 'value' => $foo['InstitutionId']);
        }
        else {
            $inst = new Institution($foo);
            $instListHelper[] = array('text' => $inst->Attributes['InstitutionName'], 'value' => $inst->Attributes['InstitutionId']);
        }
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    $instListHTML = optionsHTML($instListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>My Institutions</h2>
    <p>Inside the list below are all the institutions you have permissions to edit.</p>
</div>
<div class="flex-column">
    <h2>Edit Institution Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Course">Select an Institution to edit</label>
		    <select class="form-control" name="Course" id="Course" onchange="self.location='edit.php?id='+this.options[this.selectedIndex].value">
		        {$instListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}
//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Institution";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();