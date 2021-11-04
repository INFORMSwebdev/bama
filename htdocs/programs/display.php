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
foreach ($progs as $prog) {
    $progListHelper[] = array('text' => $prog['ProgramName'] . ' – ' . $prog['InstitutionName'], 'value' => $prog['ProgramId']);
}
//pass the name/value pairs to the file to get the generated HTML for a select list
include_once('/common/classes/optionsHTML.php');
$progListHTML = optionsHTML($progListHelper);

//get the programID from the query string, if it was given
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    //get all the details about the requested program to display
    $prog = new Program($id);
    $instId = $prog->Attributes['InstitutionId'];
    $inst = new Institution($instId);
    //$contactId = $prog->Attributes['ContactId'];
    $contacts = $prog->getContacts();
    $name = $prog->Attributes['ProgramName'];
    //$type = $prog->Attributes['ProgramType'];
    $type = $prog->getType();
    //$delivery = $prog->Attributes['DeliveryMethod'];
    $courses = $prog->getCourses(TRUE, TRUE);
    $delivery = $prog->getDeliveryMethod();
    $access = $prog->Attributes['ProgramAccess'];
    if (isset($access) && !empty($access)) {
        $accessHTML = "<p><a target='_blank' href='$access'>$access</a></p>";
    } else {
        $accessHTML = "<p class='text-info'>Access information for this program is currently not available.</p>";
    }
    $objectives = $prog->Attributes['ProgramObjectives'];
    if (!isset($objectives) || empty($objectives)) {
        $objectives = 'Objectives for this program are not currently available.';
    }
    //$fullTime = $prog->Attributes['FullTimeDuration'];
    $fullTime = "TESTTEST";//$prog->getFullTimeDurationLabel();
    if (!isset($fullTime) || empty($fullTime)) {
        $fullTime = 'Full time duration for this program is not currently available.';
    }
    //$partTime = $prog->Attributes['PartTimeDuration'];
    $partTime = $prog->getPartTimeDurationLabel();
    if (!isset($partTime) || empty($partTime)) {
        $partTime = 'Part time duration for this program is not currently available.';
    }

    $waive = $prog->Attributes['Waiver'];
    if($waive === 1){
        $waiver = 'Waivers for testing are available';
    } else {
        $waiver = 'No waivers have been selected for this program.';
    }
    //$reqs = $prog->Attributes['TestingRequirement'];
    $curReqs = $prog->getTestingRequirements(TRUE);
    $reqs = '';
    foreach($curReqs as $c){
        $reqs .= $c . '<br/>';
    }
    if (!isset($reqs) || empty($reqs)) {
        $reqs = 'Requirements for this program is not currently available.';
    }
    $otherReqs = $prog->Attributes['OtherRequirement'];
    if (!isset($otherReqs) || empty($otherReqs)) {
        $otherReqs = 'Other requirements for this program is not currently available.';
    }
    $credits = $prog->Attributes['Credits'];
    if (!isset($credits) || empty($credits)) {
        $credits = 'Credit total for this program is not currently available.';
    }
    $year = $prog->Attributes['YearEstablished'];
    if (!isset($year) || empty($year)) {
        $year = 'Year established is not currently available.';
    }
    $scholarship = $prog->Attributes['Scholarship'];
    if (!isset($scholarship) || empty($scholarship)) {
        $scholarship = 'Scholarship information for this program is not currently available.';
    }
    $res = $prog->Attributes['EstimatedResidentTuition'];
    if (!isset($res) || empty($res)) {
        $res = 'Estimated resident tuition information for this program is not currently available.';
    }
    $nonRes = $prog->Attributes['EstimatedNonresidentTuition'];
    if (!isset($nonRes) || empty($nonRes)) {
        $nonRes = 'Estimated non-resident tuition information for this program is not currently available.';
    }
    $cost = $prog->Attributes['CostPerCredit'];
    if (!isset($cost) || empty($cost)) {
        $cost = 'Cost per credit information for this program is not currently available.';
    }

    $contactHTML = '';
    //get contact details to display
    if ($contacts) {
        $contactHTML .= '<div class="card-column">';

        //$contact = new Contact($contactId);
        foreach ($contacts as $contact) {
            $contactName = $contact->Attributes['ContactName'];
            $contactTitle = $contact->Attributes['ContactTitle'];
            if (!isset($contactTitle)) {
                $contactTitle = 'Title not set.';
            }

            $contactPhone = $contact->Attributes['ContactPhone'];
            if (!isset($contactPhone)) {
                $contactPhone = 'Phone not set.';
            }

            $contactEmail = $contact->Attributes['ContactEmail'];
            if (!isset($contactEmail)) {
                $contactEmail = 'Email not set.';
            } else {
                $contactEmail = "<a href=mailto:$contactEmail'>$contactEmail</a>";
            }

            $contactHTML .= <<<EOT
<div class="card">
    <div class="card-header">
        <h3 class="display3">{$contactName}</h3>
    </div>
    <div class="card-body">
        <p>{$contactTitle}<br />{$contactPhone}<br />{$contactEmail}</p>
    </div>
    <div class="card-footer"> 
        <div class="btn-group"> 
            <button type="button" class="btn btn-info btn-contact-unassign mr-3" id="id_{$contact->id}">Un-assign This Contact</button>
            <button type="button" class="btn btn-danger btn-contact-delete" id="id_{$contact->id}">Delete This Contact</button>
        </div>
    </div>
</div>
<br/>
EOT;
        }

        $contactHTML .= '</div>';
        $contactHTML .= "<a type='button' role='button' class='btn btn-primary btn-block btn-assign-contacts mt-3' id='id_{$prog->id}' href='/programs/assignProgramContact.php?progId={$prog->id}'>Assign Existing Contact(s)</a>";
        $contactHTML .= "<a role='button' href='/contacts/add.php?progId=$prog->id' id='addNewContact' class='btn btn-info btn-block btn-contact-add'>Add New Contact and Assign</a>";
    } else {
        $contactName = $contactTitle = $contactPhone = $contactEmail = 'No contacts are currently assigned to this program.';
        $contactHTML = "<p class='text text-info'>$contactName</p>";
        $contactHTML .= "<a type='button' role='button' class='btn btn-primary btn-block btn-assign-contacts mt-3' id='id_{$prog->id}' href='/programs/assignProgramContact.php?progId={$prog->id}'>Assign Existing Contact(s)</a>";
        $contactHTML .= "<a role='button' href='/contacts/add.php?progId=$prog->id' id='addNewContact' class='btn btn-info btn-contact-add'>Add New Contact and Assign</a>";
    }

    $collegeId = $prog->Attributes['CollegeId'];
    $collegeHTML = '';
    if (empty($collegeId) || !is_numeric($collegeId)) {
        $collegeName = $collegeType = 'This program is not currently assigned to a college.';
        $collegeHTML = "<p class='text text-info'>$collegeName</p>";
        $collegeHTML .= "<button id='id_{$instId}' class='btn btn-primary btn-assignToCollege'>Assign to College</button>";
    } else {
        $c = new College($collegeId);
        if (!empty($c->Attributes['TypeId'])) {
            $cType = Dropdowns::getCollegeTypeName($c->Attributes['TypeId']);
            if ($c->Attributes['TypeId'] == 6) {
                //this is Other type
                $cType .= '&mdash;' . $c->getOtherType();
            }
        }

        $collegeHTML = <<<EOT
<div class="card">
    <div class="card-header"> 
        <h3 class="display3">{$c->Attributes['CollegeName']}</h3>
    </div>
    <div class="card-body"> 
        <h4>Type</h4>
        <p>{$cType}</p>
        <h4>Created</h4>
        <p>{$c->Attributes['CreateDate']}</p>
    </div>
    <div class="card-footer"> 
        <div class="btn-group"> 
            <button type="button" class="btn btn-info btn-college-unassign mr-3" id="id_{$c->id}">Un-assign This College</button>
            <button type="button" class="btn btn-danger btn-college-delete" id="id_{$c->id}">Delete This College</button>
        </div>
    </div>
</div>
<br />
<button id='id_{$instId}' class='btn btn-primary btn-block btn-assignToCollege'>Assign to Different College</button>
EOT;
    }

    $courseHTML = '';
    if ($courses) {
        $courseHTML = <<<EOT
<table class="table table-striped" id="courseTable">
    <thead>
        <tr>
            <th>Title</th>
            <th>Number</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
EOT;

        foreach ($courses as $co) {
            $courseHTML .= <<<EOT
        <tr>
            <td>{$co->Attributes['CourseTitle']}</td>
            <td>{$co->Attributes['CourseNumber']}</td>
            <td>
                <a role="button" class="btn btn-info btn-block" href="/courses/display.php?id={$co->Attributes['CourseId']}">View Course Details</a>
                <a role="button" class="btn btn-warning btn-block" href="/courses/edit.php?id={$co->Attributes['CourseId']}">Edit this Course</a>
                <button id="id_{$co->Attributes['CourseId']}" name="courseDelete" type="submit" class="btn btn-danger btn-block btn-delete">Delete this Course</button>
            </td>
        </tr>
EOT;
        }

        $courseHTML .= <<<EOT
    </tbody>
</table>
<hr/>
<a role="button" class="btn btn-primary btn-block" href="/courses/add.php?progId={$prog->Attributes['ProgramId']}">Add Course</a>
EOT;
    } else {
        $courseHTML = '<p class="alert alert-info">No courses are currently assigned to this program.</p>';
    }

    //get institution details to display
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
            <li class="nav-item">
                <a class="nav-link" id="courseList" href="#tabCourse" data-toggle="tab" aria-selected="false" aria-controls="tabCourse">Assigned Courses</a>
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
                <h3>Financial Assistance (for example: scholarship, fellowship, etc.)</h3>
                <p>{$scholarship}</p>
            </div>
            <input type="hidden" id="progId" value="{$prog->id}" />
        </div>
        <div class="tab-pane fade" id="tabDelivery" role="tabpanel" aria-labelledby="deliveryDetails">
            <div class="card-body">
                <h3 class="display3">Delivery Method</h3>
                <p>{$delivery}</p>
                <h3 class="display3">Duration</h3>
                <h4 class="display4">Estimated Full Time</h4>
                <p>{$fullTime}</p>
                <h4 class="display4">Estimated Part Time</h4>
                <p>{$partTime}</p>
            </div>
        </div>
        <div class="tab-pane fade" id="tabRequirement" role="tabpanel" aria-labelledby="requirementDetails">
            <div class="card-body">
                <h3 class="display3">Testing Requirements</h3>
                <p>{$reqs}</p>
                <h3 class="display3">Other Requirements</h3>
                <p>{$otherReqs}</p>
                <h3>Waiver</h3>
                <p>{$waiver}</p>
            </div>
        </div>
        <div class="tab-pane fade" id="tabCredit" role="tabpanel" aria-labelledby="creditDetails">
            <div class="card-body">
                <h3 class="display3">Credit Hours</h3>
                <p>{$credits}</p>
                <!--<h3 class="display3">Cost per Credit</h3>
                <p>{$cost}</p>-->
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
        <div class="tab-pane fade" id="tabCourse" role="tabpanel" aria-labelledby="courseList">
            <div class="card-body">
                {$courseHTML}
            </div>
        </div>
    </div>
    <div class="card-footer" id="cardFooter">
        <div class="btn-group" role="group" aria-label="Other program specific information">
            <a role="button" class="btn btn-warning mr-3" href="/programs/edit.php?id={$id}">Edit This Program</a>
            <button id="id_{$id}" role="button" class="btn btn-danger btn-program-delete">Delete This Program</button>
        </div>
    </div>
</div>
EOT;
} else {
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
    
    //college delete button functionality
    $(document).on( 'click', '.btn-college-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this college?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteCollege.php", { 'CollegeId': id }, function(data) {
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
    
    //contact delete button functionality
    $(document).on( 'click', '.btn-contact-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this contact?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteContact.php", { 'ContactId': id }, function(data) {
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
    
    //course delete button functionality
    $(document).on( 'click', '.btn-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this course?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteCourse.php", { 'CourseId': id }, function(data) {
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
    
    //un-assign contact button functionality
    $(document).on( 'click', '.btn-contact-unassign', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
                
        var conf = confirm( "Are you sure you want to un-assign this contact from the program?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            var progId = new URLSearchParams(window.location.search).get('id');
            $.post( "/scripts/ajax_unassignContact.php", { 'ContactId': id, 'ProgramId': progId}, function(data) {
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
                    if(data.msg.includes('submitted') || data.msg.includes('successfully')){
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
    
    //un-assign college button functionality
    $(document).on( 'click', '.btn-college-unassign', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to un-assign this college from the program?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            var progId = new URLSearchParams(window.location.search).get('id');
            $.post( "/scripts/ajax_unassignCollege.php", { 'CollegeId': id, 'ProgramId': progId }, function(data) {
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
//put custom/extra JS files, if used
$page_params['js'][] = array("text" => $customJS);
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();