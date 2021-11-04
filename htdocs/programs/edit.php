<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/25/2019
 * Time: 8:20 AM
 */
//require the init file
require_once '../../init.php';
include_once '/common/classes/optionsHTML.php';

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

    if(isset($_SESSION['admin']) && $_SESSION['admin'] == true){
        $userProgs = Program::getAllPrograms();
    }
    else {
        $userProgs = $user->getProgramAssignments();
    }

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
        }
        else {
            //get all the details about the requested program to display
            $prog = new Program($progId);
            $instId = $prog->Attributes['InstitutionId'];
            $contactId = $prog->Attributes['ContactId'];
            $name = $prog->Attributes['ProgramName'];
            $type = $prog->Attributes['ProgramType'];
            //$delivery = $prog->Attributes['DeliveryMethod'];
            $deliveryMethods = $prog->getDeliveryMethodOptions();
            $access = $prog->Attributes['ProgramAccess'];
            $objectives = $prog->Attributes['ProgramObjectives'];
            $fullTime = $prog->Attributes['FullTimeDurationInt'];
            if (!$fullTime) $fullTime = '';
            $partTime = $prog->Attributes['PartTimeDurationInt'];
            if (!$partTime) $partTime = '';
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
    <label class="form-check-label" for="ORFlag">Operations Research Program</label>
</div>
EOT;
            if ($ops == true) {
                $opsHTML = <<<EOT
<div class="form-check">
    <input class="form-check-input" type="checkbox" id="ORFlag" name="ORFlag" value="1" checked >
    <label class="form-check-label" for="ORFlag">Operations Research Program</label>
</div>
EOT;
            }
            $analytics = $prog->Attributes['AnalyticsFlag'];
            $analyticsHTML = <<<EOT
<div class="form-check">
    <input class="form-check-input" type="checkbox" id="AnalyticsFlag" name="AnalyticsFlag" value="0" >
    <label class="form-check-label" for="AnalyticsFlag">Analytics Program</label>
</div>
EOT;
            if ($analytics == true) {
                $analyticsHTML = <<<EOT
<div class="form-check">
    <input class="form-check-input" type="checkbox" id="AnalyticsFlag" name="AnalyticsFlag" value="1" checked >
    <label class="form-check-label" for="AnalyticsFlag">Analytics Program</label>
</div>
EOT;
            }
            $collegeId = $prog->Attributes['CollegeId'];

            //include file that creates the option lists function
            //include_once('/common/classes/optionsHTML.php');

            //get contact details
            //get list of contacts and turn it into a select list
            //$contacts = Contact::getAllContacts();
            $contacts = $prog->getContacts();
            if($contacts) {
                $contactListHelper = array();
                foreach ($contacts as $c) {
                    $contactListHelper[] = array('text' => $c->Attributes['ContactName'], 'value' => $c->Attributes['ContactId']);
                }
                $contactListHTML = optionsHTML($contactListHelper);
                if (isset($contactId)) {
                    $contactListHTML = str_replace('<option value="' . $contactId . '">', '<option value="' . $contactId . '" selected>', $contactListHTML);
                }
            }
            else {
                $contactListHTML = '<p>There are no contacts associated with this program.</p>';
            }

            //get institution details
            $inst = new Institution($instId);
            $instName = $inst->Attributes['InstitutionName'];
            $instAddr = $inst->Attributes['InstitutionAddress'];
            $instCity = $inst->Attributes['InstitutionCity'];
            $instState = $inst->Attributes['InstitutionState'];
            $instZip = $inst->Attributes['InstitutionZip'];
            //$instRegion = $inst->Attributes['InstitutionRegion'];
            $instRegion = Dropdowns::getInstitutionRegionName($inst->Attributes['RegionId']);
            $instPhone = $inst->Attributes['InstitutionPhone'];
            $instEmail = $inst->Attributes['InstitutionEmail'];
            //$instAccess = $inst->Attributes['InstitutionAccess']; //this field was removed from the DB

            //get list of institutions for editor to select from
            //get list of all institutions
            $institutions = Institution::getInstitutions();
            //turn that into an array of name/value pairs to pass to the optionsHTML.php file
            $instListHelper = array();
            foreach ($institutions as $instFoo) {
                $instListHelper[] = array('text' => $instFoo['InstitutionName'], 'value' => $instFoo['InstitutionId']);
            }
            $instListHelper[] = array('text' => 'Other', 'value' => 'Other');
            //pass the name/value pairs to the file to get the generated HTML for a select list
            $instListHTML = optionsHTML($instListHelper);
            //make the currently assigned institution be the selected value
            $instListHTML = str_replace('<option value="' . $instId . '">', '<option value="' . $instId . '" selected>', $instListHTML);

            //get list of colleges in the institution and set the currently selected option to assigned college
            $colleges = $inst->getColleges();
            $collegeHelper = array();
            foreach ($colleges as $co) {
                //get institution name so editors can select the appropriate college tied to the institution
                $foo = new Institution($co->Attributes['InstitutionId']);
                $collegeHelper[] = array('text' => $co->Attributes['CollegeName'] . ' (' . $foo->Attributes['InstitutionName'] . ')', 'value' => $co->Attributes['CollegeId']);
            }
            $collegeListHTML = optionsHTML($collegeHelper);
            if (isset($collegeId)) {
                $collegeListHTML = str_replace('<option value="' . $collegeId . '">', '<option value="' . $collegeId . '" selected>', $collegeListHTML);
            }

            //program tags addition
            //find out if any tags are currently selected
            $curTags = $prog->getTags();
            if($curTags){
                //currently assigned tags
                # ToDo: Test this and make sure it works as expected
                //$tagHTML = Dropdowns::getProgramTagOptionsHTML($curTags);
                $tagHTML = Program::renderTagHTML($curTags);
            } else {
                //no tags yet
                //$tagHTML = Dropdowns::getProgramTagOptionsHTML();
                $tagHTML = Program::renderTagHTML();
            }

            $curReqs = $prog->getTestingRequirements();
            $reqIds = array();
            if($curReqs){
                foreach($curReqs as $r){
                    $reqIds[] = $r['id'];
                }
            }
            $requirementsHTML = Program::renderTestingRequirementsHTML($reqIds);

            //program type dropdown change
            $curType = $prog->getType();
            if($curType){
                $typeHtml = Dropdowns::getProgramTypeOptionsHTML($curType);
            } else {
                $typeHtml = Dropdowns::getProgramTypeOptionsHTML();
            }
            /*$curFullTime = $prog->getFullTimeDuration();
            if($curFullTime){
                $fullTimeOptions = Dropdowns::getProgramFullTimeDurationOptionsHTML($curFullTime);
            } else {
                $fullTimeOptions = Dropdowns::getProgramFullTimeDurationOptionsHTML();
            }
            $curPartTime = $prog->getPartTimeDuration();
            if($curPartTime){
                $partTimeOptions  = Dropdowns::getProgramPartTimeDurationOptionsHTML($curPartTime);
            } else {
                $partTimeOptions  = Dropdowns::getProgramPartTimeDurationOptionsHTML();
            }*/

            $contactHTML = <<<EOT
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
EOT;
            # ToDo: Fix the form groups for all pages
            //user DOES have permission to edit this page, display the form
            $content = <<<EOT
<div class="flex-column">
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <form action="../scripts/processProgramEditForm.php" method="POST">
        <h3>Program Details</h3>
        <div class="form-group">
            <label for="programName">Program Name</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="programName" id="programName" value="{$name}" placeholder="Program Name" required />
        </div>
        <div class="form-group">
            <label for="programType">Program Type</label><span class="text text-danger">*</span>
            <select class="form-control" id="ProgramType" name="ProgramType" required>
                {$typeHtml}
            </select> 
        </div>
        <div class="form-group">
            <label for="programs_option">Program Classification</label>
            <div class="form-row">
                {$tagHTML}
            </div>
            <p class="form-text text-info">Limited to selection of at MOST 3 tags</p>
        </div>
        <div class="form-group">
            <label for="ProgramObjs">Objectives</label>
            <textarea class="form-control" name="ProgramObjs" id="ProgramObjs" rows="3">{$objectives}</textarea>
        </div>
        <div class="form-group">
            <label for="ProgramAccess">Access Link</label>
            <input type="text" class="form-control" name="ProgramAccess" id="ProgramAccess" value="{$access}" placeholder="URL to external program page" />
        </div>
        <div class="form-group">
            <label for="YearEstablished">Year Established</label>
            <input type="text" class="form-control" name="YearEstablished" id="YearEstablished" value="{$year}" />
        </div>
        <div class="form-group">
            <label for="Scholarship">Financial Assistance (for example: scholarship, fellowship, etc.)</label>
            <textarea class="form-control" name="Scholarship" id="Scholarship">{$scholarship}</textarea>
        </div>       
        <h3>Delivery Details</h3>
        <div class="form-group">
            <label for="DeliveryMethod">Method</label>
            <select class="form-control" id="DeliveryMethod" name="DeliveryMethod">
                {$deliveryMethods}
            </select>                
        </div>
        <div class="form-group">
            <label for="FullTime">Estimated Full Time Duration (months)</label>
           <input type="number" class="form-control" id="FullTime" name="FullTime" min="1" max="100" value="{$fullTime}"/>
           <i>Please enter numbers only, no alpha characters</i>
        </div>
        <div class="form-group">
            <label for="PartTime">Estimated Part Time Duration (months)</label>
            <input type="number" class="form-control" id="PartTime" min="1" max="100"  name="PartTime" value="{$partTime}"/>
            <i>Please enter numbers only, no alpha characters</i>
        </div>
        <h3>Requirement Details</h3>
        <div class="form-group">
            <label for="TestingRequirement">Testing Requirements</label>
            <div class="form-row">
                {$requirementsHTML}
            </div>
        </div>
        <div class="form-group">
            <label for="OtherRequirement">Other Requirements</label>
            <input type="text" class="form-control" name="OtherRequirement" id="OtherRequirement" value="{$otherReqs}" />
        </div>
        <div class="form-check"> 
            <input type="checkbox" class="form-check-input" id="Waiver" name="Waiver" value="1">
            <label class="form-check-label" for="Waiver">Does your program offer waivers for testing?</label>
        </div> 
        <h3>Credit Details</h3>
        <div class="form-group">
            <label for="Credits">Credit Hours</label>
            <input type="text" class="form-control" name="Credits" id="Credits" value="{$credits}" />
        </div>
        <!--<div class="form-group">
            <label for="CostPerCredit">Cost per Credit</label>
            <input type="text" class="form-control" name="CostPerCredit" id="CostPerCredit" value="{$cost}" />
        </div>-->
        <div class="form-group">
            <label for="ResidentTuition">Estimated Resident Tuition</label>
            <input type="text" class="form-control" name="ResidentTuition" id="ResidentTuition" value="{$res}" />
        </div>
        <div class="form-group">
            <label for="NonResident">Estimated Non-Resident Tuition</label>
            <input type="text" class="form-control" name="NonResident" id="NonResident" value="{$nonRes}" />
        </div>
        <!-- Contact & College details went here -->
        <h3>College Assignment</h3>
        <div class="form-group"> 
            <div id="collegeList"><!-- empty div for college list AJAX stuff --></div>
        </div>
        <div class="form-group">
            <input type="hidden" id="currentCollege" name="currentCollege" value="{$prog->Attributes['CollegeId']}" />
            <input type="hidden" id="programId" name="programId" value="{$progId}" />
            <input type="hidden" id="institutionId" name="institutionId" value="{$inst->id}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This Program</button>
        </div>
        <div class="form-group">
            <p class="lead">These changes will not take effect until they have been approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
<div class="flex-column">
    <a href="/programs/display.php?id={$prog->id}" role="button" class="btn btn-primary">View Program Details Page</a>
</div>
EOT;
        }
    }
    else {
        //display a list of programs user has permission to edit
        $progListHelper = array();
        foreach($userProgs as $uProgId){
            if(isset($_SESSION['admin'])){
                $instHelp = new Institution($uProgId['InstitutionId']);
                $progListHelper[] = array('text' => $uProgId['ProgramName'] . ' - ' . $instHelp->Attributes['InstitutionName'], 'value' => $uProgId['ProgramId']);
            }
            else {
                $tProg = new Program($uProgId);
                $instHelp = new Institution($tProg->Attributes['InstitutionId']);
                $progListHelper[] = array('text' => $tProg->Attributes['ProgramName'] . ' - ' . $instHelp->Attributes['InstitutionName'], 'value' => $tProg->Attributes['ProgramId']);
            }
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
		        {$progListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
    }
}

$customJS = <<<EOT
$(function() {
    //max limit on number of tags
    $('input.programTag').on('change', function(e){
        var maxAllowed = 3;
        if($('input.programTag:checked').length > maxAllowed){
            $(this).prop('checked', '');
            alert('Too many tags selected, the limit is 3.');
        }
    });

    //make sure message box gets re-hidden if its shown
    $('#message').hide();
        
    //get list of colleges to choose from
    //var urlParams = new URLSearchParams(window.location.search);
    var id = $('#institutionId').val();
    //alert('id = ' + id); 
    $.getJSON( "/scripts/ajax_getColleges.php", { 'InstitutionId': id }, function(data) {
        //alert( data );
        if (data.errors.length > 0 ) {
            var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
            for (var i = 0; i < data.errors.length; i++) {
                msg +=  data.errors[i] + "\\r\\n";
            }
            //alert( msg );
            $('#message').html('<p>' + msg + '</p>');
            $('#message').addClass('alert alert-danger');
            $('#message').show();
        }
        else if (data.success == 1) {
        //display list to user in a nice way
            var htmlFoo = processCollegeList(data.colleges);
            $('#collegeList').html(htmlFoo);
        }
        else {
            //I don't think this should ever get hit, but what would happen if it did?
            $('#collegeList').html('<p>Derp</p>');
        }
    }); 
    
    $('.programs_option').on( 'change', function(e) {
      if ($('.programs_option:checked').length > 3) {
        alert('You may select a maximum of three tags.');
        this.checked = false;
      }
    });
    
    var orig_data = $('form').serialize();
    
    $('form').submit( function(e) { 
      if (orig_data == $('form').serialize()) {
        alert('There have been no changes made.');
        return false;
      }
      var errors = [];
      if ($('.programs_option:checked').length < 1) errors.push('You must select at least one tag.');
      if (errors.length > 0) {
        msg = "One or more errors were encountered: \\n\\n" + errors.join("\\n\\n");
        alert(msg);
        return false;
      } else {
        return true;
      }
    });
});

function processCollegeList(colleges){
    //var html = '<h4>Colleges</h4>';
    html = '<p class="form-text">Select <strong>one</strong> college below to assign this program to:</p>';
    
    //html += '<form>'
    var id = $('#currentCollege').val();
    //console.log(id);
    //set up the select list
    html += '<select size="5" id="collegeSelectList" name="collegeSelectList" class="form-control">';
    html += '<option></option>';
    //set up how the info gets displayed in the div for the user to select the college
    for(var i = 0; i < colleges.length; i++) {
        if(id == colleges[i].CollegeId){
            html += '<option value="' + colleges[i].CollegeId + '" selected>' + colleges[i].CollegeName + '</option>';
        }
        else {
            html += '<option value="' + colleges[i].CollegeId + '">' + colleges[i].CollegeName + '</option>';
        }
    }
    html += '</select>';
    //html += '<br />';
    //html += '<button id="collegeSelectSubmit" class="btn btn-info btn-submit-collegeAssignment">Assign to Selected College</button>';
    //html += '</form>';
    
    //return the HTML to put in the div
    return html;
}
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Program Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['js'][] = array( 'text' => $customJS );
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();