<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 10:31 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to add programs to the system.';
    header('Location: ../users/login.php');
    die;
}

//include file that has the option lists function
include_once('/common/classes/optionsHTML.php');

//get list of contacts and turn it into a select list
$contacts = Contact::getAllContacts();
$contactListHelper = array();
foreach($contacts as $c){
    $contactListHelper[] = array ('text' => $c['ContactName'], 'value' => $c['ContactId']);
}
$contactListHTML = optionsHTML($contactListHelper);

//get list of colleges into a select list
$colleges = College::getAllColleges();
$collegeHelper = array();
foreach($colleges as $co){
    //get institution name so editors can select the appropriate college tied to the institution
    $foo = new Institution($co['InstitutionId']);
    $collegeHelper[] = array ('text' => $co['CollegeName'] . ' (' . $foo->Attributes['InstitutionName'] . ')', 'value' => $co['CollegeId']);
}
$collegeListHTML = optionsHTML($collegeHelper);

//get list of all institutions
$institutions = Institution::getInstitutions();
//turn that into an array of name/value pairs to pass to the optionsHTML.php file
$instListHelper = array();
foreach($institutions as $inst){
    $instListHelper[] = array('text' => $inst['InstitutionName'], 'value' => $inst['InstitutionId']);
}
$instListHelper[] = array('text' => 'Other', 'value' => 'Other');
//pass the name/value pairs to the file to get the generated HTML for a select list
$instListHTML = optionsHTML($instListHelper);

//user is logged in, let them add a program
$content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processProgramAddForm.php" method="POST">
        <div class="form-row">
            <h3>Program Details</h3>
        </div>
        <div class="form-row">
            <label for="programName">Program Name</label>
            <input type="text" class="form-control" name="programName" id="programName" placeholder="Program Name" required />
        </div>
        <br />
        <div class="form-row">
            <label for="AnalyticsFlag">Program Classification</label>
        </div>
        <div class="form-row">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="AnalyticsFlag" name="AnalyticsFlag" value="1" />
                <label class="form-check-label" for="AnalyticsFlag">Analytics Program</label>
            </div>
        </div>
        <div class="form-row">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="ORFlag" name="ORFlag" value="1" />
                <label class="ml-1" for="ORFlag">Operations Research Program</label>
            </div>
        </div>
        <div class="form-row">
            <label for="institutionName">Institution</label>
            <select class="form-control" id="Institution" name="Institution" aria-describedby="InstitutionHelp" required>
                <!-- ToDo: make this into ajax and add a filter like Dave's stuff has? -->
		        {$instListHTML}
            </select>
            <p id="InstitutionHelp">The list may take a second or two to load, please be patient after clicking the field.</p>
        </div>
        <div class="form-row">
            <label for="ProgramObjs">Objectives</label>
            <textarea class="form-control" name="ProgramObjs" id="ProgramObjs" rows="3"></textarea>
        </div>
        <div class="form-row">
            <label for="ProgramType">Type</label>
            <input type="text" class="form-control" name="ProgramType" id="ProgramType" placeholder="Program Type" required />
        </div>
        <div class="form-row">
            <label for="ProgramAccess">Access Link</label>
            <input type="text" class="form-control" name="ProgramAccess" id="ProgramAccess" placeholder="URL to external program page" />
        </div>
        <div class="form-row">
            <label for="YearEstablished">Year Established</label>
            <input type="text" class="form-control" name="YearEstablished" id="YearEstablished" />
        </div>
        <div class="form-row">
            <label for="Scholarship">Scholarships</label>
            <textarea class="form-control" name="Scholarship" id="Scholarship"></textarea>
        </div>
        <br />
        <div class="form-row">
            <h3>Delivery Details</h3>
        </div>
        <div class="form-row">
            <label for="DeliveryMethod">Method</label>
            <input type="text" class="form-control" name="DeliveryMethod" id="DeliveryMethod" />                
        </div>
        <div class="form-row">
            <label for="FullTime">Full Time Duration</label>
                <input type="text" class="form-control" name="FullTime" id="FullTime" />
        </div>
        <div class="form-row">
            <label for="PartTime">Part Time Duration</label>
            <input type="text" class="form-control" name="PartTime" id="PartTime" />
        </div>
        <br />
        <div class="form-row">
            <h3>Requirement Details</h3>
        </div>
        <div class="form-row">
            <label for="TestingRequirement">Testing Requirements</label>
            <input type="text" class="form-control" name="TestingRequirement" id="TestingRequirement" />
        </div>
        <div class="form-row">
            <label for="OtherRequirement">Other Requirements</label>
            <input type="text" class="form-control" name="OtherRequirement" id="OtherRequirement" />
        </div>
        <br />
        <div class="form-row">
            <h3>Credit Details</h3>
        </div>
        <div class="form-row">
            <label for="Credits">Credits</label>
            <input type="text" class="form-control" name="Credits" id="Credits" />
        </div>
        <div class="form-row">
            <label for="CostPerCredit">Cost per Credit</label>
            <input type="text" class="form-control" name="CostPerCredit" id="CostPerCredit" />
        </div>
        <div class="form-row">
            <label for="ResidentTuition">Estimated Resident Tuition</label>
            <input type="text" class="form-control" name="ResidentTuition" id="ResidentTuition" />
        </div>
        <div class="form-row">
            <label for="NonResident">Estimated Non-Resident Tuition</label>
            <input type="text" class="form-control" name="NonResident" id="NonResident" />
        </div>
        <br />
        <div class="form-row">
            <h3>Contact Details</h3>
        </div>
        <div class="form-row">
            <label for="ContactId">Contact</label>
            <select class="form-control" id="ContactId" name="ContactId" aria-describedby="ContactHelp">
		        {$contactListHTML}
            </select>
            <!--<p id="ContactHelp">The list may take a second or two to load, please be patient after clicking the field.</p>-->
        </div>
        <br />
        <div class="form-row">
            <h3>College Details</h3>
        </div>
        <div class="form-row">
            <label for="CollegeId">College</label>
            <select class="form-control" id="CollegeId" name="CollegeId" aria-describedby="CollegeHelp">
		        {$collegeListHTML}
            </select>
            <!--<p id="InstitutionHelp">The list may take a second or two to load, please be patient after clicking the field.</p>-->
        </div>
        <br />
        <div class="form-row">
            <button class="btn btn-warning" type="submit" name="add" value="add">Submit New Program</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This program will not be added to the system until it is approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add a Program";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
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