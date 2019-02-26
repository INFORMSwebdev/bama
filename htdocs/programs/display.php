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
        $accessHTML = "<p><a target='_blank' href='$access'>$access</a></p>";
    } else {
        $accessHTML = "<p class='text-info'>Access information for this program is currently not available.</p>";
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

    $contactHTML = '';
    //get contact details to display
    if(is_numeric($contactId)){
        $contact = new Contact($contactId);
        $contactName = $contact->Attributes['ContactName'];
        $contactTitle = $contact->Attributes['ContactTitle'];
        $contactPhone = $contact->Attributes['ContactPhone'];
        $contactEmail = $contact->Attributes['ContactEmail'];
        $contactHTML = <<<EOT
<h3 class="display3">{$contactName}</h3>
<p>{$contactTitle}<br />{$contactPhone}<br /><a href="mailto:{$contactEmail}">{$contactEmail}</a></p>
EOT;
    } else {
        $contactName = $contactTitle = $contactPhone = $contactEmail = 'Contact information for this program is not currently available';
        $contactHTML = "<p class='text text-info'>$contactName</p>";
    }

    $collegeHTML = '';
    //get college details to display
    if(is_numeric($collegeId)){
        $college = new College($collegeId);
        $collegeName = $college->Attributes['CollegeName'];
        $collegeType = $college->Attributes['CollegeType'];
        $collegeHTML = <<<EOT
<h3 class="display3">Name</h3>
<p>{$collegeName}</p>
<h3 class="display3">Type</h3>
<p>{$collegeType}</p>
EOT;
    } else {
        $collegeName = $collegeType = 'College information for this program is not currently available.';
        $collegeHTML = "<p class='text text-info'>$collegeName</p>";
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

    $content = <<<EOT
<div class="card">
    <div class="card-header" id="cardHeader">
        <h2 class="display2">{$name}</h2>
        <ul class="nav nav-tabs card-header-tabs" id="cardNav" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="programDetails" href="#tabProgram" data-toggle="tab" aria-selected="true" aria-controls="tabProgram">Program Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="deliveryDetails" href="#tabDelivery" data-toggle="tab" aria-selected="false" aria-controls="tabDelivery">Delivery Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="requirementDetails" href="#tabRequirement" data-toggle="tab" aria-selected="false" aria-controls="tabRequirement">Requirement Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="creditDetails" href="#tabCredit" data-toggle="tab" aria-selected="false" aria-controls="tabCredit">Credit Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="institutionDetails" href="#tabInstitution" data-toggle="tab" aria-selected="false" aria-controls="tabInstitution">Institution Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="collegeDetails" href="#tabCollege" data-toggle="tab" aria-selected="false" aria-controls="tabCollege">College Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="contactDetails" href="#tabContact" data-toggle="tab" aria-selected="false" aria-controls="tabCollege">Contact Details</a>
            </li>
        </ul>
    </div>
    <div class="tab-content" id="ProgramTabContent">
        <div class="tab-pane fade show active" id="tabProgram" role="tabpanel" aria-labelledby="programDetails">
            <div class="card-body">
                <h3 class="display3">Objectives</h3>
                <p>{$objectives}</p>
                <h3 class="display3">Type</h3>
                <p>{$type}</p>
                <h3 class="display3">Access Link</h3>
                {$accessHTML}
                <h3 class="display3">Year Established</h3>
                <p>{$year}</p>
            </div>
        </div>
        <div class="tab-pane fade" id="tabDelivery" role="tabpanel" aria-labelledby="deliveryDetails">
            <div class="card-body">
                <h3 class="display3">Delivery Method</h3>
                <p>{$delivery}</p>
                <h3 class="display3">Duration</h3>
                <h4 class="display4">Full Time</h4>
                <p>{$fullTime}</p>
                <h4 class="display4">Part Time</h4>
                <p>{$partTime}</p>
            </div>
        </div>
        <div class="tab-pane fade" id="tabRequirement" role="tabpanel" aria-labelledby="requirementDetails">
            <div class="card-body">
                <h3 class="display3">Testing Requirements</h3>
                <p>{$reqs}</p>
                <h3 class="display3">Other Requirements</h3>
                <p>{$otherReqs}</p>
            </div>
        </div>
        <div class="tab-pane fade" id="tabCredit" role="tabpanel" aria-labelledby="creditDetails">
            <div class="card-body">
                <h3 class="display3">Total Credits</h3>
                <p>{$credits}</p>
                <h3 class="display3">Cost per Credit</h3>
                <p>{$cost}</p>
                <h3 class="display3">Estimated Resident Tuition</h3>
                <p>{$res}</p>
                <h3 class="display3">Estimated Non-Resident Tuition</h3>
                <p>{$nonRes}</p>
            </div>
        </div>
        <div class="tab-pane fade" id="tabInstitution" role="tabpanel" aria-labelledby="institutionDetails">
            <div class="card-body">
                <h3 class="display3">Address</h3>
                <h4 class="display4">{$instName}</h4>
                <p>{$instAddr}<br />{$instCity}, {$instState} {$instZip}</p>
                <h3 class="display3">Region</h3>
                <p>{$instRegion}</p>
            </div>
        </div>
        <div class="tab-pane fade" id="tabCollege" role="tabpanel" aria-labelledby="collegeDetails">
            <div class="card-body">
                {$collegeHTML}
            </div>
        </div>
        <div class="tab-pane fade" id="tabContact" role="tabpanel" aria-labelledby="contactDetails">
            <div class="card-body">
                {$contactHTML}
            </div>
        </div>
    </div>
    <div class="card-footer" id="cardFooter">
        <div class="btn-group" role="group" aria-label="Other program specific information">
            <a role="button" class="btn btn-outline-primary" href="/courses/programCourses.php?id={$prog->Attributes['ProgramId']}">Program Courses</a>
            <a role="button" class="btn btn-outline-primary" href="/instructors/programInstructors.php?id={$prog->Attributes['ProgramId']}">Program Instructors</a>
            <a role="button" class="btn btn-outline-primary" href="/software/programSoftware.php?id={$prog->Attributes['ProgramId']}">Program Software</a>
            <a role="button" class="btn btn-outline-primary" href="/textbooks/programTextbooks.php?id={$prog->Attributes['ProgramId']}">Program Text Books</a>
            <a role="button" class="btn btn-outline-primary" href="/cases/programCases.php?id={$prog->Attributes['ProgramId']}">Program Case Studies</a>
        </div>
    </div>
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