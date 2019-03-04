<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/25/2019
 * Time: 8:20 AM
 */
//require the init file
require_once '../../init.php';

//get the program ID (if set) of the program to edit
$progId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

//check if user is logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true) {
    //set up a message to display on the login page
    $_SESSION['logoutMessage'] = 'Please log in to edit your program\'s information.';
    //redirect to login page so user can log in
    header('Location: login.php');
    //don't want the script to keep executing after a redirect
    die;
}

$content = '';

//make sure the user has permission to edit this program's info
if (is_numeric($_SESSION['loggedIn'])) {
    $user = new User($_SESSION['loggedIn']);

    $userProgs = $user->getProgramAssignments();

    //make sure we actually have an Id to work with
    if (isset($progId)) {
        //if the programId passed via the query string is NOT in this list, the user does NOT have permission to edit this page
        if (!in_array($progId, $userProgs) && !isset($_SESSION['admin'])) {
            //set up the message to be red
            $_SESSION['editMessage']['success'] = false;
            $_SESSION['editMessage']['text'] = 'You do not have permission to edit the specified program\'s information.';

            //redirect to index
            header('Location: /index.php');
            die;
        } else {
            //get all the details about the requested program to display
            $prog = new Program($progId);
            $instId = $prog->Attributes['InstitutionId'];
            $contactId = $prog->Attributes['ContactId'];
            $name = $prog->Attributes['ProgramName'];
            $type = $prog->Attributes['ProgramType'];
            $delivery = $prog->Attributes['DeliveryMethod'];
            $access = $prog->Attributes['ProgramAccess'];
            $objectives = $prog->Attributes['ProgramObjectives'];
            $fullTime = $prog->Attributes['FullTimeDuration'];
            $partTime = $prog->Attributes['PartTimeDuration'];
            $reqs = $prog->Attributes['TestingRequirement'];
            $otherReqs = $prog->Attributes['OtherRequirement'];
            $credits = $prog->Attributes['Credits'];
            $year = $prog->Attributes['YearEstablished'];
            $scholarship = $prog->Attributes['Scholarship'];
            $res = $prog->Attributes['EstimatedResidentTuition'];
            $nonRes = $prog->Attributes['EstimatedNonresidentTuition'];
            $cost = $prog->Attributes['CostPerCredit'];
            //we only use the flag columns in queries, no need to display info about them on this page
            $ops = $prog->Attributes['ORFlag'];
            $opsHTML = <<<EOT
<div class="form-check">
    <input class="form-check-input" type="checkbox" id="ORFlag" name="ORFlag" value="0" />
    <label class="ml-1" for="ORFlag">Operations Research Program</label>
</div>
EOT;
            if ($ops == true) {
                $opsHTML = <<<EOT
<div class="form-check">
    <input class="form-check-input" type="checkbox" id="ORFlag" name="ORFlag" value="1" checked />
    <label for="ORFlag">Operations Research Program</label>
</div>
EOT;
            }
            $analytics = $prog->Attributes['AnalyticsFlag'];
            $analyticsHTML = <<<EOT
<div class="form-check">
    <input class="form-check-input" type="checkbox" id="AnalyticsFlag" name="AnalyticsFlag" value="0" />
    <label class="form-check-label" for="AnalyticsFlag">Analytics Program</label>
</div>
EOT;
            if ($analytics == true) {
                $analyticsHTML = <<<EOT
<div class="form-check">
    <input class="form-check-input" type="checkbox" id="AnalyticsFlag" name="AnalyticsFlag" value="1" checked />
    <label class="form-check-label" for="AnalyticsFlag">Analytics Program</label>
</div>
EOT;
            }
            $collegeId = $prog->Attributes['CollegeId'];

            //include file that creates the option lists function
            include_once('/common/classes/optionsHTML.php');

            //get contact details
            //get list of contacts and turn it into a select list
            $contacts = Contact::getAllContacts();
            $contactListHelper = array();
            foreach ($contacts as $c) {
                $contactListHelper[] = array('text' => $c['ContactName'], 'value' => $c['ContactId']);
            }
            $contactListHTML = optionsHTML($contactListHelper);
            if (isset($contactId)) {
                $contactListHTML = str_replace('<option value="' . $contactId . '">', '<option value="' . $contactId . '" selected>', $contactListHTML);
            }

            //get list of colleges and set the currently selected option to assigned college
            $colleges = College::getAllColleges();
            $collegeHelper = array();
            foreach ($colleges as $co) {
                //get institution name so editors can select the appropriate college tied to the institution
                $foo = new Institution($co['InstitutionId']);
                $collegeHelper[] = array('text' => $co['CollegeName'] . ' (' . $foo->Attributes['InstitutionName'] . ')', 'value' => $co['CollegeId']);
            }
            $collegeListHTML = optionsHTML($collegeHelper);
            if (isset($collegeId)) {
                $collegeListHTML = str_replace('<option value="' . $collegeId . '">', '<option value="' . $collegeId . '" selected>', $collegeListHTML);
            }

            //get institution details
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

            //get list of institutions for editor to select from
            //get list of all institutions
            $institutions = Institution::getInstitutions();
            //turn that into an array of name/value pairs to pass to the optionsHTML.php file
            $instListHelper = array();
            foreach ($institutions as $inst) {
                $instListHelper[] = array('text' => $inst['InstitutionName'], 'value' => $inst['InstitutionId']);
            }
            $instListHelper[] = array('text' => 'Other', 'value' => 'Other');
            //pass the name/value pairs to the file to get the generated HTML for a select list
            $instListHTML = optionsHTML($instListHelper);
            //make the currently assigned institution be the selected value
            $instListHTML = str_replace('<option value="' . $instId . '">', '<option value="' . $instId . '" selected>', $instListHTML);

            //user DOES have permission to edit this page, display the form
            $content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processProgramEditForm.php" method="POST">
        <div class="form-row">
            <h3>Program Details</h3>
        </div>
        <div class="form-row">
            <label for="programName">Program Name</label>
            <input type="text" class="form-control" name="programName" id="programName" value="{$name}" placeholder="Program Name" required />
        </div>
        <br />
        <div class="form-row">
            <label for="AnalyticsFlag">Program Classification</label>
        </div>
        <div class="form-row">
            {$analyticsHTML}
        </div>
        <div class="form-row">
            {$opsHTML}
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
            <textarea class="form-control" name="ProgramObjs" id="ProgramObjs" rows="3">{$objectives}</textarea>
        </div>
        <div class="form-row">
            <label for="ProgramType">Type</label>
            <input type="text" class="form-control" name="ProgramType" id="ProgramType" value="{$type}" placeholder="Program Type" required />
        </div>
        <div class="form-row">
            <label for="ProgramAccess">Access Link</label>
            <input type="text" class="form-control" name="ProgramAccess" id="ProgramAccess" value="{$access}" placeholder="URL to external program page" />
        </div>
        <div class="form-row">
            <label for="YearEstablished">Year Established</label>
            <input type="text" class="form-control" name="YearEstablished" id="YearEstablished" value="{$year}" />
        </div>
        <div class="form-row">
            <label for="Scholarship">Scholarships</label>
            <textarea class="form-control" name="Scholarship" id="Scholarship">{$scholarship}</textarea>
        </div>
        <br />
        <div class="form-row">
            <h3>Delivery Details</h3>
        </div>
        <div class="form-row">
            <label for="DeliveryMethod">Method</label>
            <input type="text" class="form-control" name="DeliveryMethod" id="DeliveryMethod" value="{$delivery}" />                
        </div>
        <div class="form-row">
            <label for="FullTime">Full Time Duration</label>
                <input type="text" class="form-control" name="FullTime" id="FullTime" value="{$fullTime}" />
        </div>
        <div class="form-row">
            <label for="PartTime">Part Time Duration</label>
            <input type="text" class="form-control" name="PartTime" id="PartTime" value="{$partTime}" />
        </div>
        <br />
        <div class="form-row">
            <h3>Requirement Details</h3>
        </div>
        <div class="form-row">
            <label for="TestingRequirement">Testing Requirements</label>
            <input type="text" class="form-control" name="TestingRequirement" id="TestingRequirement" value="{$reqs}" />
        </div>
        <div class="form-row">
            <label for="OtherRequirement">Other Requirements</label>
            <input type="text" class="form-control" name="OtherRequirement" id="OtherRequirement" value="{$otherReqs}" />
        </div>
        <br />
        <div class="form-row">
            <h3>Credit Details</h3>
        </div>
        <div class="form-row">
            <label for="Credits">Credits</label>
            <input type="text" class="form-control" name="Credits" id="Credits" value="{$credits}" />
        </div>
        <div class="form-row">
            <label for="CostPerCredit">Cost per Credit</label>
            <input type="text" class="form-control" name="CostPerCredit" id="CostPerCredit" value="{$cost}" />
        </div>
        <div class="form-row">
            <label for="ResidentTuition">Estimated Resident Tuition</label>
            <input type="text" class="form-control" name="ResidentTuition" id="ResidentTuition" value="{$res}" />
        </div>
        <div class="form-row">
            <label for="NonResident">Estimated Non-Resident Tuition</label>
            <input type="text" class="form-control" name="NonResident" id="NonResident" value="{$nonRes}" />
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
            <input type="hidden" id="programId" name="programId" value="{$progId}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This Program</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">These changes will not take effect until they have been approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;
        }
    }
    else {
        //display a list of programs user has permission to edit
        $progListHelper = array();
        foreach($userProgs as $uProgId){
            $tProg = new Program($uProgId);
            $progListHelper[] = array('text' => $tProg->Attributes['ProgramName'], 'value' => $tProg->Attributes['ProgramId']);
        }
        //get the options maker, its gonna be needed
        include_once('/common/classes/optionsHTML.php');
        //pass the name/value pairs to the file to get the generated HTML for a select list
        $progListHTML = optionsHTML($progListHelper);

        $content = <<<EOT
<div class="flex-column">
    <h2>My Programs</h2>
    <p>Inside the list below are all the programs you have permissions to edit.</p>
</div>
<div class="flex-column">
    <h2>Edit Program Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Course">Select a Program to edit</label>
		    <select class="form-control" name="Program" id="Program" onchange="self.location='edit.php?id='+this.options[this.selectedIndex].value">
		        $progListHTML
            </select>
        </div>
    </form>
</div>
EOT;
    }
}


//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Program Details";
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