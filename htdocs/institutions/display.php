<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/21/2019
 * Time: 8:16 AM
 */
//include the init file
require_once '../../init.php';

//checks for messages?

//check for loggedin?
# ToDo: confer w/ Dave on whether this page should be locked down by logging in or not. My money is on leave it open.

//get list of all institutions
$institutions = Institution::getInstitutions();
//turn that into an array of name/value pairs to pass to the optionsHTML.php file
$instListHelper = array();
foreach($institutions as $inst){
    $instListHelper[] = array('text' => $inst['InstitutionName'], 'value' => $inst['InstitutionId']);
}
//pass the name/value pairs to the file to get the generated HTML for a select list
include_once('/common/classes/optionsHTML.php');
$instListHTML = optionsHTML($instListHelper);

//get the institutionId from the query string
$id = filter_input( INPUT_GET, 'id',  FILTER_VALIDATE_INT);
//check to see if we have the ID value or not
if ($id) {
    $inst = new Institution($id);
    $name = $inst->Attributes['InstitutionName'];
    $addr = $inst->Attributes['InstitutionAddress'];
    $zip = $inst->Attributes['InstitutionZip'];
    $city = $inst->Attributes['InstitutionCity'];
    $state = $inst->Attributes['InstitutionState'];
    $region = $inst->Attributes['InstitutionRegion'];
    $phone = $inst->Attributes['InstitutionPhone'];
    $email = $inst->Attributes['InstitutionEmail'];
    $content = <<<EOT
<h2 class="display-2">{$name}</h2>
<div class="jumbotron">
    <form>
        <div class="form-row">
            <h3>Address Info</h3>
        </div>
        <div class="form-row">
            <div class="col-md-3">
                <label for="InstitutionAddress">Address</label>
                <input type="text" class="form-control" name="InstitutionAddress" value="{$addr}" id="InstitutionAddress" readonly />
            </div>
            <div class="col-md-3">
                <label for="InstitutionCity">City</label>
                <input type="text" class="form-control" name="InstitutionCity" value="{$city}" id="InstitutionCity" readonly />
            </div>
            <div class="col-md-3">
                <label for="InstitutionState">State</label>
                <input type="text" class="form-control" name="InstitutionState" value="{$state}" id="InstitutionState" readonly />
            </div>
            <div class="col-md-3">
                <label for="InstitutionZip">Zip Code</label>
                <input type="text" class="form-control" name="InstitutionZip" value="{$zip}" id="InstitutionZip" readonly />
            </div>
        </div>
        <div class="form-row">
            <h3>Contact Info</h3>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <label for="InstitutionPhone">Phone</label>
                <input type="text" class="form-control" name="InstitutionPhone" value="{$phone}" id="InstitutionPhone" readonly />
            </div>
            <div class="col-md-6">
                <label for="InstitutionEmail">Email</label>
                <input type="text" class="form-control" name="InstitutionEmail" value="{$email}" id="InstitutionEmail" readonly />
            </div>
        </div>
        <div class="form-row">
            <h3>Other Info</h3>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="InstitutionRegion">Region</label>
                <input type="text" class="form-control" name="InstitutionRegion" value="{$region}" id="InstitutionRegion" readonly />
            </div>
        </div>
    </form>
</div>
<div class="row d-block">
    <h2>View Another Institution's Info</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Institution">Select a different Institution</label>
		    <select class="form-control" id="Institution" name="Institution" aria-describedby="InstitutionHelp" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        $instListHTML
            </select>
            <p class="text text-muted" id="InstitutionHelp">The list may take a second to load, please be patient after clicking the field.</p>
        </div>
    </form>
</div>
EOT;
} else {
    //error parsing query string for integer value in id variable
    $content = <<<EOT
<div class="row">
    <p>Please select an institution from the list:</p>
</div>
<div class="row">
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Institution">Institution (select one)</label>
		    <select class="form-control" id="Institution" name="Institution" aria-describedby="InstitutionHelp" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        $instListHTML
            </select>
            <p class="text text-muted" id="InstitutionHelp">The list may take a second to load, please be patient after clicking the field.</p>
        </div>
    </form>
</div>
EOT;
}

//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = "Display Institution Information";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
//$page_params['active_menu_item'] = 'register';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();