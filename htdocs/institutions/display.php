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

# ToDo: make the institution select list ajax powered so it doesn't make the rest of the page load slower (optional, but would be nice)

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
    if(!isset($region) || empty($region)){
        $region = 'Region information is not currently available for this institution.';
    }
    $phone = $inst->Attributes['InstitutionPhone'];
    if(!isset($phone) || empty($phone)){
        $phone = 'Phone number not currently available for this institution.';
    }
    $email = $inst->Attributes['InstitutionEmail'];
    if(!isset($email) || empty($email)){
        $email = 'Email address not currently available for this institution.';
    }
    else {
        $email = "<a href='mailto:$email'>$email</a>";
    }

    $content = <<<EOT
<div class="card">
    <div class="card-header">
        <h2 class="display2">{$name}</h2>
    </div>
    <div class="card-body"> 
        <h3>Address</h3>
        <p>{$addr}</p>
        <p>{$city}, {$state} {$zip}</p>
        <h3>Contact Information</h3>
        <h4>Phone Number</h4>
        <p>{$phone}</p>
        <h4>Email</h4>
        <p>{$email}</p>
        <h3>Region</h3>
        <p>{$region}</p>
    </div>
</div>
EOT;
} else {
    //error parsing query string for integer value in id variable
    $content = <<<EOT
<div class="flex-column">
    <h2>View Institution Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Institution">Institution (select one)</label>
		    <select class="form-control" id="Institution" name="Institution" aria-describedby="InstitutionHelp" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        {$instListHTML}
            </select>
            <p class="text text-muted" id="InstitutionHelp">The list may take a second or two to load, please be patient after clicking the field.</p>
        </div>
    </form>
</div>
EOT;
}

//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = "Display Institution Information";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
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