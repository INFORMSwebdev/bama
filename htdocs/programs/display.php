<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/21/2019
 * Time: 3:45 PM
 */
//require the init file
require_once '../../init.php';

//checks for messages?

//get list of all institutions
$progs = Program::getAllProgramsAndInstitutions();
//turn that into an array of name/value pairs to pass to the optionsHTML.php file
$progListHelper = array();
foreach($progs as $prog){
    $progListHelper[] = array('text' => $prog['ProgramName'] . ' – ' . $prog['InstitutionName'], 'value' => $prog['ProgramId']);
}
//pass the name/value pairs to the file to get the generated HTML for a select list
include_once('/common/classes/optionsHTML.php');
$progListHTML = optionsHTML($progListHelper);

//get the programID from the query string, if it was given
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if($id){
    //get all the details about the requested program to display
    $prog = new Program($id);
    $instId = $prog->Attributes['InstitutionId'];
    $contactId = $prog->Attributes['ContactId'];
    $name = $prog->Attributes['ProgramName'];
    $type = $prog->Attributes['ProgramType'];
    $delivery = $prog->Attributes['DeliveryMethod'];
    $access = $prog->Attributes['ProgramAccess'];
    if(isset($access) && !empty($access)){
        $accessHTML = <<<EOT
<label for="ProgramAccess">Access Page</label>
<a target="_blank" href="{$access}">
    <input type="text" class="form-control" name="ProgramAccess" id="ProgramAccess" value="{$access}" aria-describedby="ProgramAccessHelp" readonly />
</a>
<p class="text text-white">Clicking inside this field will open a new tab to the URL specified.</p>
EOT;
    } else {
        $accessHTML = <<<EOT
<label for="ProgramAccess">Access Page</label>
<input type="text" class="form-control" name="ProgramAccess" id="ProgramAccess" value="Access information for this program is currently not available." readonly />
<br />
EOT;
    }
    $objectives = $prog->Attributes['ProgramObjectives'];
    if(!isset($objectives) || empty($objectives)){
        $objectives = 'Objectives for this program are not currently available.';
    }
    $fullTime = $prog->Attributes['FullTimeDuration'];
    if(!isset($fullTime) || empty($fullTime)){
        $fullTime = 'Full time duration for this program is not currently available.';
    }
    $partTime = $prog->Attributes['PartTimeDuration'];
    if(!isset($partTime) || empty($partTime)){
        $partTime = 'Part time duration for this program is not currently available.';
    }
    $reqs = $prog->Attributes['TestingRequirement'];
    if(!isset($reqs) || empty($reqs)){
        $reqs = 'Requirements for this program is not currently available.';
    }
    $otherReqs = $prog->Attributes['OtherRequirement'];
    if(!isset($otherReqs) || empty($otherReqs)){
        $otherReqs = 'Other requirements for this program is not currently available.';
    }
    $credits = $prog->Attributes['Credits'];
    if(!isset($credits) || empty($credits)) {
        $credits = 'Credit total for this program is not currently available.';
    }
    $year = $prog->Attributes['YearEstablished'];
    if(!isset($year) || empty($year)) {
      $year = 'Year established is not currently available.';
    }
    $scholarship = $prog->Attributes['Scholarship'];
    if(!isset($scholarship) || empty($scholarship)) {
        $scholarship = 'Scholarship information for this program is not currently available.';
    }
    $res = $prog->Attributes['EstimatedResidentTuition'];
    if(!isset($res) || empty($res)) {
        $res = 'Estimated resident tuition information for this program is not currently available.';
    }
    $nonRes = $prog->Attributes['EstimatedNonresidentTuition'];
    if(!isset($nonRes) || empty($nonRes)) {
        $nonRes = 'Estimated non-resident tuition information for this program is not currently available.';
    }
    $cost = $prog->Attributes['CostPerCredit'];
    if(!isset($cost) || empty($cost)) {
        $cost = 'Cost per credit information for this program is not currently available.';
    }
    //we only use the flag columns in queries, no need to display info about them on this page
    //$ops = $prog->Attributes['ORFlag'];
    //$analytics = $prog->Attributes['AnalyticsFlag'];
    $collegeId = $prog->Attributes['CollegeId'];

    //get contact details to display
    if(is_numeric($contactId)){
        $contact = new Contact($contactId);
        $contactName = $contact->Attributes['ContactName'];
        $contactTitle = $contact->Attributes['ContactTitle'];
        $contactPhone = $contact->Attributes['ContactPhone'];
        $contactEmail = $contact->Attributes['ContactEmail'];
    } else {
        $contactName = $contactTitle = $contactPhone = $contactEmail = 'Contact information for this program is not currently available';
    }

    //get college details to display
    if(is_numeric($collegeId)){
        $college = new College($collegeId);
        $collegeName = $college->Attributes['CollegeName'];
        $collegeType = $college->Attributes['CollegeType'];
    } else {
        $collegeName = $collegeType = 'College information for this program is not currently available.';
    }

    //get institution details to display
    $inst = new Institution($instId);
    $instName = $inst->Attributes['InstitutionName'];
    $instAddr = $inst->Attributes['InstitutionAddress'];
    $instCity = $inst->Attributes['InstitutionCity'];
    $instState = $inst->Attributes['InstitutionState'];
    $instZip = $inst->Attributes['InstitutionZip'];
    $instRegion = $inst->Attributes['InstitutionRegion'];
    $instPhone = $inst->Attributes['InstitutionPhone'];
    $instEmail = $inst->Attributes['InstitutionEmail'];
    $instAccess = $inst->Attributes['InstitutionAccess'];

    # ToDo: implement null checks above and display appropriate information if a field is null

    $content = <<<EOT
<h2 class="display-3">{$name}</h2>
<h3 class="display-4">{$instName}</h3>
<div class="jumbotron bg-info text-white">
    <form>
        <div class="form-row">
            <h3>Program Details</h3>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="ProgramObjs">Objectives</label>
                <!--<input type="text" class="form-control text-wrap" name="ProgramObjs" id="ProgramObjs" value="{$objectives}" readonly />-->
                <textarea class="form-control" name="ProgramObjs" id="ProgramObjs" readonly>{$objectives}</textarea>
            </div>
        </div>
        <br />
        <div class="form-row">
            <div class="col-md-4">
                <label for="ProgramType">Type</label>
                <input type="text" class="form-control" name="ProgramType" id="ProgramType" value="{$type}" readonly />
            </div>
            <div class="col-md-4">
                <label for="DeliveryMethod">Delivery Method</label>
                <input type="text" class="form-control" name="DeliveryMethod" id="DeliveryMethod" value="{$delivery}" readonly />
            </div>
            <div class="col-md-4">
                $accessHTML
            </div>
        </div>
        <!--<br />-->
        <div class="form-row">
            <h3>Delivery Details</h3>
        </div>
        <div class="form-row">
            <div class="col-md-4">
                <label for="DeliveryMethod">Method</label>
                <input type="text" class="form-control" name="DeliveryMethod" id="DeliveryMethod" value="{$delivery}" readonly />
            </div>
            <div class="col-md-4">
                <label for="FullTime">Full Time Duration</label>
                <input type="text" class="form-control" name="FullTime" id="FullTime" value="{$fullTime}" readonly />
            </div>
            <div class="col-md-4">
                <label for="PartTime">Part Time Duration</label>
                <input type="text" class="form-control" name="PartTime" id="PartTime" value="{$partTime}" readonly />
            </div>
        </div>
        <br />
        <div class="form-row">
            <h3>Requirement Details</h3>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <label for="TestingRequirement">Testing Requirements</label>
                <input type="text" class="form-control" name="TestingRequirement" id="TestingRequirement" value="{$reqs}" readonly />
            </div>
            <div class="col-md-6">
                <label for="OtherRequirement">Other Requirements</label>
                <input type="text" class="form-control" name="OtherRequirement" id="OtherRequirement" value="{$otherReqs}" readonly />
            </div>
        </div>
        <br />
        <div class="form-row">
            <h3>Credit Details</h3>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <label for="Credits">Credits</label>
                <input type="text" class="form-control" name="Credits" id="Credits" value="{$credits}" readonly />
            </div>
            <div class="col-md-6">
                <label for="CostPerCredit">Cost per Credit</label>
                <input type="text" class="form-control" name="CostPerCredit" id="CostPerCredit" value="{$cost}" readonly />
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <label for="ResidentTuition">Estimated Resident Tuition</label>
                <input type="text" class="form-control" name="ResidentTuition" id="ResidentTuition" value="{$res}" readonly />
            </div>
            <div class="col-md-6">
                <label for="NonResident">Estimated Non-Resident Tuition</label>
                <input type="text" class="form-control" name="NonResident" id="NonResident" value="{$nonRes}" readonly />
            </div>
        </div>
        <br />
        <div class="form-row">
            <h3>Other Program Details</h3>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="YearEstablished">Year Established</label>
                <input type="text" class="form-control" name="YearEstablished" id="YearEstablished" value="{$year}" readonly />
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="Scholarship">Scholarships</label>
                <textarea class="form-control" name="Scholarship" id="Scholarship" readonly>{$scholarship}</textarea>
            </div>
        </div>
        <br />
        <div class="form-row">
            <h3>Contact Details</h3>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <label for="ContactName">Name</label>
                <input type="text" class="form-control" name="ContactName" id="ContactName" value="{$contactName}" readonly />
            </div>
            <div class="col-md-6">
                <label for="ContactTitle">Title</label>
                <input type="text" class="form-control" name="ContactTitle" id="ContactTitle" value="{$contactTitle}" readonly />
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-6">
                <label for="ContactPhone">Phone</label>
                <input type="text" class="form-control" name="ContactPhone" id="ContactPhone" value="{$contactPhone}" readonly />
            </div>
            <div class="col-md-6">
                <label for="ContactEmail">Email</label>
                <a href="mailto:{$contactEmail}"><input type="text" class="form-control" name="ContactEmail" id="ContactEmail" value="{$contactEmail}" aria-describedby="ContactEmailHelp" readonly /></a>
                <p class="text text-white" id="ContactEmailHelp">Clicking inside this field will open a new email to the address specified.</p>
            </div>
        </div>
        <!--<br />-->
        <div class="form-row">
            <h3>Institution Details</h3>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="InstitutionName">Name</label>
                <input type="text" class="form-control" name="InstitutionName" id="InstitutionName" value="{$instName}" readonly />
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="InstitutionAddress">Address</label>
                <input type="text" class="form-control" name="InstitutionAddress" id="InstitutionAddress" value="{$instAddr}" readonly />
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-4">
                <label for="InstitutionCity">City</label>
                <input type="text" class="form-control" name="InstitutionCity" id="InstitutionCity" value="{$instCity}" readonly />
            </div>
            <div class="col-md-4">
                <label for="InstitutionState">State</label>
                <input type="text" class="form-control" name="InstitutionState" id="InstitutionState" value="{$instState}" readonly />
            </div>
            <div class="col-md-4">
                <label for="InstitutionZip">Zip Code</label>
                <input type="text" class="form-control" name="InstitutionZip" id="InstitutionZip" value="{$instZip}" readonly />
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="InstitutionRegion">Region</label>
                <input type="text" class="form-control" name="InstitutionRegion" id="InstitutionRegion" value="{$instRegion}" readonly />
            </div>
        </div>
        <br />
        <div class="form-row">
            <h3>College Details</h3>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="CollegeName">Name</label>
                <input type="text" class="form-control" name="CollegeName" id="CollegeName" value="{$collegeName}" readonly />
            </div>
        </div>
        <div class="form-row">
            <div class="col-md-12">
                <label for="CollegeType">Type</label>
                <input type="text" class="form-control" name="CollegeType" id="CollegeType" value="{$collegeType}" readonly />
            </div>
        </div>
    </form>
</div>
<div class="flex-column">
    <h2>View Another Program's Info</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Institution">Select a Program</label>
		    <select class="form-control" name="" id="" aria-describedby="Help" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        $progListHTML
            </select>
            <!--<p class="text text-muted" id="Help">The list may take a second or two to load, please be patient after clicking the field.</p>-->
        </div>
    </form>
</div>
EOT;
} else {
    //invalid input, either not there or not an integer
    $content = <<<EOT
<div class="flex-column">
    <h2>View Program Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Institution">Select a Program</label>
		    <select class="form-control" name="" id="" aria-describedby="Help" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        $progListHTML
            </select>
            <!--<p class="text text-muted" id="Help">The list may take a second or two to load, please be patient after clicking the field.</p>-->
        </div>
    </form>
</div>
EOT;
}

# ToDo: add in another nav menu (maybe on the left rail?) that has the select list so people don't have to scroll to the bottom
 # of the page to change the program info
# ToDo: change the display from a form to divs or something, but move the form HTML to the edit page!

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Program Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
//$page_params['js'][] = array( 'text' => $custom_js );
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
//$page_params['active_menu_item'] = 'home';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();