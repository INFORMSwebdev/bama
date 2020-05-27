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
    header('Location: /users/login.php');
    die;
}

//include file that has the option lists function
include_once('/common/classes/optionsHTML.php');

$instId = filter_input(INPUT_GET, 'instId', FILTER_VALIDATE_INT);

$prog = new Program();
$deliveryOptions = $prog->getDeliveryMethodOptions(TRUE);

if(empty($instId)){
    $collegeHTML = '<p>No valid InstitutionId to get college information for. College must be assigned after this new program has been approved by an INFORMS admin.</p>';
}
else {
    $inst = new Institution($instId);

}

$programTypeOptions = Program::renderProgramTypeOptionHTML();

$tagHTML = Program::renderTagHTML();

$fullTimeDurationOptions = Program::getFullTimeDurationOptionHTML();
$partTimeDurationOptions = Program::getPartTimeDurationOptionHTML();
$TestingRequirementOptions = Program::renderTestingRequirementsHTML();

//user is logged in, let them add a program
$content = <<<EOT
<div class="flex-column">
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <form action="/scripts/processProgramAddForm.php" method="POST">
        <div class="form-row">
            <h3>Program Details</h3>
        </div>
        <div class="form-row">
            <label for="programName">Program Name</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="programName" id="programName" placeholder="Program Name" required />
        </div>
        <br />
        <div class="form-row">
            <h3>Program Classification</h3>
        </div>
        <div class="form-row">
            <p>Select up to three tags for this program. You must select at least one.</p>
        </div>
        <div class="form-row">
            $tagHTML
        </div>
        <!--<div class="form-row">
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
        </div>-->
        <div class="form-row">
            <label for="ProgramObjs">Objectives</label>
            <textarea class="form-control" name="ProgramObjs" id="ProgramObjs" rows="3"></textarea>
        </div>
        <div class="form-row">
            <label for="ProgramType">Type</label><span class="text text-danger">*</span>
            <!--<input type="text" class="form-control" name="ProgramType" id="ProgramType" placeholder="Program Type" required />-->
            <select class="form-control" name="ProgramTypeId" id="ProgramTypeId" placeholder="Program Type" required>$programTypeOptions</select>
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
            <label for="Scholarship">Financial Assistance (for example: scholarship, fellowship, etc.)</label>
            <textarea class="form-control" name="Scholarship" id="Scholarship"></textarea>
        </div>
        <br />
        <div class="form-row">
            <h3>Delivery Details</h3>
        </div>
        <div class="form-row">
            <label for="DeliveryMethod">Method</label>
            <select class="form-control" id="DeliveryMethod" name="DeliveryMethod">
                {$deliveryOptions}
            </select>             
        </div>
        <div class="form-row">
            <label for="FullTime">Full Time Duration</label>
                <!--<input type="text" class="form-control" name="FullTime" id="FullTime" />-->
            <select class="form-control" name="FullTime" id="FullTime"><option/>$fullTimeDurationOptions </select>
        </div>
        <div class="form-row">
            <label for="PartTime">Part Time Duration</label>
            <!--<input type="text" class="form-control" name="PartTime" id="PartTime" />-->
            <select class="form-control" name="PartTime" id="PartTime"><option/>$partTimeDurationOptions</select>
        </div>
        <br />
        <div class="form-row">
            <h3>Requirement Details</h3>
        </div>
        <div class="form-row">
            <label for="TestingRequirement">Testing Requirements</label>
            $TestingRequirementOptions
            <!--<input type="text" class="form-control" name="TestingRequirement" id="TestingRequirement" />-->
        </div>
        <div class=""form-row">
            <label for="Waiver">Waiver for testing requirements available?</label>
            <input type="checkbox" name="Waiver" id="Waiver" value="1" /> Yes
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
            <label for="Credits">Credit Hours</label>
            <input type="text" class="form-control" name="Credits" id="Credits" />
        </div>
        <!--<div class="form-row">
            <label for="CostPerCredit">Cost per Credit</label>
            <input type="text" class="form-control" name="CostPerCredit" id="CostPerCredit" />
        </div>-->
        <div class="form-row">
            <label for="ResidentTuition">Estimated Resident Tuition</label>
            <input type="text" class="form-control" name="ResidentTuition" id="ResidentTuition" />
        </div>
        <div class="form-row">
            <label for="NonResident">Estimated Non-Resident Tuition</label>
            <input type="text" class="form-control" name="NonResident" id="NonResident" />
        </div>
        <br/>

        <div class="form-row">
            <h3>College Assignment</h3>
        </div>
        <div class="form-row"> 
            <div id="collegeList"><!-- empty div for college list AJAX stuff --></div>
        </div>
        
        <div class="form-row">
            <input type="hidden" name="instId" id="instId" value="{$instId}" />
            <button class="btn btn-warning" type="submit" name="add" value="add">Submit New Program</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This program will not be added to the system until it is approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;

$customJS = <<<EOT
$(function() {
    //make sure message box gets re-hidden if its shown
    $('#message').hide();
        
    //get list of colleges to choose from
    var urlParams = new URLSearchParams(window.location.search);
    var id = urlParams.get('instId');
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
    
    $('form').submit( function(e) { 
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
  
    var html = '<h4>Colleges</h4>';
    if (colleges.length > 0) {
        html += '<p>Select one college below to assign this program to:</p>';
        
        //html += '<form>'
        //set up the select list
        html += '<select size="5" id="collegeSelectList" name="collegeSelectList">';
        html += '<option></option>';
        //set up how the info gets displayed in the div for the user to select the college
        for(var i = 0; i < colleges.length; i++) {
            html += '<option value="' + colleges[i].CollegeId + '">' + colleges[i].CollegeName + '</option>';
        }
        html += '</select>';
        //html += '<br />';
        //html += '<button id="collegeSelectSubmit" class="btn btn-info btn-submit-collegeAssignment">Assign to Selected College</button>';
        //html += '</form>';
    }
    else {
        html += '<p>There are no colleges listed for this institution. To assign this program to a college you will need to first add a college to the institution.</p>';
    }
    //return the HTML to put in the div
    return html;
}
EOT;


//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add a Program";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['js'][] = array( 'text' => $customJS );
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();