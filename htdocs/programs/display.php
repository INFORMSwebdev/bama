<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/21/2019
 * Time: 3:45 PM
 */
//require the init file
require_once '../../init.php';

$content = '';

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
    }
    else {
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
    }
    else {
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
    }
    else {
        $collegeName = $collegeType = 'This program is not currently assigned to a college.';
        $collegeHTML = "<p class='text text-info'>$collegeName</p>";
        $collegeHTML .= "<button id='id_{$instId}' class='btn btn-primary btn-assignToCollege'>Assign to College</button>";
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

    $content .= <<<EOT
<div class="card">
    <div class="card-header" id="cardHeader">
        <h2 class="display2">{$name} - {$instName}</h2>
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
                <a class="nav-link" id="contactDetails" href="#tabContact" data-toggle="tab" aria-selected="false" aria-controls="tabContact">Contact Details</a>
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
                <h3>Scholarship</h3>
                <p>{$scholarship}</p>
            </div>
            <input type="hidden" id="progId" value="{$prog->id}" />
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
                <div id="collegeList"><!-- empty div for college list AJAX stuff --></div> 
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
            <a role="button" class="btn btn-warning mr-3" href="/programs/edit.php?id={$id}">Edit This Program</a>
            <button id="id_{$id}" role="button" class="btn btn-danger btn-institution-delete">Delete This Program</button>
        </div>
    </div>
</div>
EOT;
}
else {
    //invalid input, either not there or not an integer
    $content .= <<<EOT
<div class="flex-column">
    <h2>View Program Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Program">Select a Program</label>
		    <select class="form-control" name="Program" id="Program" aria-describedby="Help" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        {$progListHTML}
            </select>
            <!--<p class="text text-muted" id="Help">The list may take a second or two to load, please be patient after clicking the field.</p>-->
        </div>
    </form>
</div>
EOT;
}

$customJS = <<<EOT
$(function() {    
    //assignToCollege button functionality
    $(document).on( 'click', '.btn-assignToCollege', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        
        //get list of colleges to choose from
        var id = $(this).attr('id').substring(3);
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
    });
    
    //submitting college assignment via ajax
    $(document).on( 'click', '.btn-submit-collegeAssignment', function(e) {
        //hide the message box so the old message (if there) doesn't get confused with the new message (is this necessary?)
        $('#message').hide();
    
        //gather selected college
        var colId = $('#collegeSelectList option:selected').val();
        //get the program id
        var progId = $('#progId').val();
        
        //hide the college list
        $('#collegeList').hide();
        
        //send it to the ajax processor
        //I hope I can still use this id from above the .get!
        $.post( "/scripts/ajax_assignProgramToCollege.php", {'ProgramId': progId, 'CollegeId': colId}, function(data){
            //alert( data );
            //display results of processor
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
            else {
                $('#message').html('<p>' + data.message + '</p>');
                $('#message').addClass('alert alert-success');
                $('#message').show();
            }
        }, 'json' ); 
    });
    
    //institution delete button functionality
    $(document).on( 'click', '.btn-institution-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this institution?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteInstitution.php", { 'InstitutionId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    alert( msg );
                    //$('#message').html('<p>' + msg + '</p>').removeClass('d-hidden').addClass('alert alert-danger');
                }
                else if (data.msg) {
                    //alert( data.msg );
                    $('#message').html('<p>' + data.msg + '</p>');
                    if(data.msg.includes('submitted')){
                        $('#message').addClass('alert alert-success');
                    }
                    else {
                        $('#message').addClass('alert alert-danger');
                    }
                    $('#message').show();
                }
            }, "json");
        }
    });
    
    //program delete button functionality
    $(document).on( 'click', '.btn-program-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this program?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteProgram.php", { 'ProgramId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    alert( msg );
                    //$('#message').html('<p>' + msg + '</p>').removeClass('d-hidden').addClass('alert alert-danger');
                }
                else if (data.msg) {
                    //alert( data.msg );
                    $('#message').html('<p>' + data.msg + '</p>');
                    if(data.msg.includes('submitted')){
                        $('#message').addClass('alert alert-success');
                    }
                    else {
                        $('#message').addClass('alert alert-danger');
                    }
                    $('#message').show();
                }
            }, "json");
        }
    });
});

function processCollegeList(colleges){
    var html = '<h2>Colleges</h2>';
    html += '<p>Select a College Below:</p>';
    
    //html += '<form>'
    //set up the select list
    html += '<select size="5" id="collegeSelectList">';
    html += '<option></option>';
    //set up how the info gets displayed in the div for the user to select the college
    for(var i = 0; i < colleges.length; i++) {
        html += '<option value="' + colleges[i].CollegeId + '">' + colleges[i].CollegeName + '</option>';
    }
    html += '</select>';
    html += '<br />';
    html += '<button id="collegeSelectSubmit" class="btn btn-info btn-submit-collegeAssignment">Assign to Selected College</button>';
    //html += '</form>';
    
    //return the HTML to put in the div
    return html;
}

EOT;


//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Program Details";
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
$page_params['js'][] = array("text" => $customJS);
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();