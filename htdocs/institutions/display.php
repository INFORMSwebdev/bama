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
$content = '';

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
    //$region = $inst->Attributes['InstitutionRegion'];
    $region = $inst->Attributes['RegionId'];
    if(!isset($region) || empty($region)){
        $region = 'Region information is not currently available for this institution.';
    } else {
        $region = Dropdowns::getInstitutionRegionName($region);
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
/*
    $content .= <<<EOT
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
    $content .= <<<EOT
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
*/
}

$id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );

$content = <<<EOT
<div class="container-fluid" id="programList"> 
</div>
EOT;

$custom_js = <<<EOT
$(function() {
    $('#programList').html('<p>Loading data, please wait&hellip;</p>');
    $.get( "/scripts/ajax_displayEditorInstitution.php", {'id': $id }, function( data ) {
        //alert(data);
        if (data.errors.length > 0) { 
            var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
            for (var i = 0; i < data.errors.length; i++) {
                msg +=  data.errors[i] + "\\r\\n";
            }
            alert( msg );
        }
        else if (data.success == 1) {
            //process the returned info into HTML
            var helper = processProgramList(data.institutions);
            //display the returned info in the div
            $('#programList').html(helper);
           // $('#programDetails').tab('show'); // this is how we go to tab
           // $('#usersTable').DataTable();
           // $('#courseTable').DataTable();
            
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
        }
    }, "json");
  
    function processProgramList(progs){

        if(progs.length < 1) {
            //there are no programs in the passed list
            var foo = '<p class="text text-danger">No institutions available to display right now, please try again later.</p>';
            return foo;
        } 
        else if (progs.length == 1) {
            var html = '<div class="card">';
            html += '<div class="card-header">';
            html += '<h2 class="display2">' + progs[0].InstitutionName + '</h2>';
            html += '<ul class="nav nav-tabs card-header-tabs" id="cardNav" role="tablist">';
            html += '<li class="nav-item">';
            html += '<a class="nav-link active" id="institutionDetails" href="#tabInstitution" data-toggle="tab" aria-selected="true" aria-controls="tabInstitution">Institution</a>';
            html += '</li>';
            html += '<li class="nav-item">';
            html += '<a class="nav-link" id="collegeDetails" href="#tabCollege" data-toggle="tab" aria-selected="false" aria-controls="tabCollege">College</a>';
            html += '</li>';
            html += '<li class="nav-item">';
            html += '<a class="nav-link" id="programDetails" href="#tabProgram" data-toggle="tab" aria-selected="false" aria-controls="tabProgram">Program</a>';
            html += '</li>';
            html += '</ul>';
            html += '</div>';
        
            //institution tab
            html += '<div class="tab-content" id="InstitutionTabContent">';
            html += '<div class="tab-pane fade show active" id="tabInstitution" role="tabpanel" aria-labelledby="institutionDetails">';
            html += '<div class="card-body">';
            html += '<h3>Address</h3>';
            html += '<p>' + progs[0].InstitutionAddress + '</p>';
            html += '<p>' + progs[0].InstitutionCity + ', ' + progs[0].InstitutionState + ' ' + progs[0].InstitutionZip + '</p>';
            html += '<h3>Region</h3>';
            html += '<p>' + progs[0].InstitutionRegion + '</p>';
            html += '<h3>Contact Information</h3>';
            html += '<h4>Phone Number</h4>';
            html += '<p>' + progs[0].InstitutionPhone + '</p>';
            html += '<h4>Email</h4>';
            if(progs[0].InstitutionEmail.indexOf("@") < 0){
                html += '<p>' + progs[0].InstitutionEmail + '</p>';
            }
            else {
                html += '<p><a href="mailto:' + progs[0].InstitutionEmail + '">' + progs[0].InstitutionEmail + '</a></p>';
            }
            //html += '<h4>Access</h4>';
            //if(progs[0].InstitutionAccess.indexOf("www") < 0){
            //    html += '<p>' + progs[0].InstitutionAccess + '</p>';
            //}
            //else{
            //    html += '<p><a href="' + progs[0].InstitutionAccess + '" target="_blank">' + progs[0].InstitutionAccess + '</a></p>';
            //}
            html += '<h3>Last Modified</h3>';
            html += '<p>' + progs[0].LastModifiedDate + '</p>';
            html += '<div class="btn-group">';
            html += '<a role="button" class="btn btn-warning mr-3" href="/institutions/edit.php?id=' + progs[0].InstitutionId + '">Edit this Institution</a>';
            html += '<button id="id_' + progs[0].InstitutionId + '" name="instDelete" type="submit" class="btn btn-danger btn-institution-delete">Delete this Institution</button>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        
            //programs tab
            html += '<div class="tab-pane fade" id="tabProgram" role="tabpanel" aria-labelledby="programDetails">';
            html += '<div class="card-body">';
            for(var x = 0; x < progs.length; x++){
                //there is either only 1 program returned or a message that no programs available
                if(progs[x].programs.length < 1){
                    //no programs for this institution
                    html += '<p>No programs are currently assigned to this institution.</p>';
                }
                else {
                    html += '<div class="card-column">';
                    for(var i = 0; i < progs[x].programs.length; i++){
                        html += '<div class="card">';
                        html += '<div class="card-header">';
                        html += '<h3>' + progs[x].programs[i].ProgramName + '</h3>';
                        html += '<ul class="nav nav-tabs card-header-tabs" id="progCardNav" role="tablist">';
                        html += '<li class="nav-item">';
                        html += '<a class="nav-link active" id="progDetails' + i +'" href="#tabPrograms' + i + '" data-toggle="tab" aria-selected="true" aria-controls="tabPrograms' + i + '">Program Details</a>';
                        html += '</li>';
                        html += '<li class="nav-item">';
                        html += '<a class="nav-link" id="courseDetails' + i + '" href="#tabCourse' + i + '" data-toggle="tab" aria-selected="false" aria-controls="tabCourse' + i + '">Courses</a>';
                        html += '</li>';
                        html += '</ul>';                
                        html += '</div>';
                        html += '<div class="tab-content" id="tabProgramsDerp' + i + '">';
                        html += '<div class="tab-pane fade show active" id="tabPrograms' + i + '" role="tabpanel" aria-labelledby="progDetails' + i +'">';
                        html += '<div class="card-body">';
                        html += '<h4>Type</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramType + '</p>';
                        html += '<h4>Delivery Method</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramDelivery + '</p>';
                        html += '<h4>Access</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramAccess + '</p>';
                        html += '<h4>Objectives</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramObjectives + '</p>';
                        html += '<h4>Full Time Duration</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramFullTime + '</p>';
                        html += '<h4>Part Time Duration</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramPartTime + '</p>';
                        html += '<h4>Testing Requirements</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramTestingRequirements + '</p>';
                        html += '<h4>Other Requirements</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramOtherRequirements + '</p>';
                        html += '<h4>Year Established</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramEstablished + '</p>';
                        html += '<h4>Scholarship</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramScholarship + '</p>';
                        html += '<h4>Credits</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramCredits + '</p>';
                        html += '<h4>Estimated Resident Tuition</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramResidentTuition + '</p>';
                        html += '<h4>Estimated Non-Resident Tuition</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramNonResidentTuition + '</p>';
                        html += '<h4>Cost Per Credit</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramCostPerCredit + '</p>';
                        html += '<h4>Analytics or O.R. Program</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramAnalyticsOR + '</p>';
                        html += '<h4>This Record Created On</h4>';
                        html += '<p>' + progs[x].programs[i].ProgramCreated + '</p>';
                        html += '<h3>College Assignment</h3>';
                        html += '<p>' + progs[x].programs[i].College + '</p>';
                        html += '<h3>Assigned Contacts</h3>';
                        if(progs[x].programs[i].Contacts[0] == null){
                            html += '<p>No contacts currently assigned to this program.</p>';
                        }
                        else {
                            for(var k = 0; k < progs[x].programs[i].Contacts.length; k++){
                                html += '<h4>Name</h4>';
                                html += '<p>' + progs[x].programs[i].Contacts[k].ContactName + '</p>';
                                html += '<h4>Title</h4>';
                                html += '<p>' + progs[x].programs[i].Contacts[k].ContactTitle + '</p>';
                                html += '<h4>Phone</h4>';
                                html += '<p>' + progs[x].programs[i].Contacts[k].ContactPhone + '</p>';
                                html += '<h4>Email</h4>';
                                html += '<p>' + progs[x].programs[i].Contacts[k].ContactEmail + '</p>';
                                if(k + 1 < progs[x].programs[i].Contacts.length){
                                    html += '<hr />';
                                }
                            }
                        }
                        html += '<div class="btn-group">';
                        html += '<a role="button" class="btn btn-info mr-3" href="/programs/display.php?id=' + progs[x].programs[i].ProgramId + '">View Program Details</a>';
                        html += '<a role="button" class="btn btn-warning mr-3" href="/programs/edit.php?id=' + progs[x].programs[i].ProgramId + '">Edit this Program</a>';
                        html += '<button id="id_' + progs[x].programs[i].ProgramId + '" name="programDelete" type="submit" class="btn btn-danger btn-program-delete">Delete this Program</button>';
                        html += '</div>'; //button-group
                        html += '</div>'; //body
                        html += '<div class="card-footer">';
                        html += '<div class="btn-group">';
                        html += '<a role="button" class="btn btn-primary mr-3" href="/programs/assignProgramContact.php?progId=' + progs[x].programs[i].ProgramId + '">Assign Existing Contact</a>';
                        html += '<a role="button" href="/contacts/add.php?progId=' + progs[x].programs[i].ProgramId + '" id="addNewContact" name="addNewContact" class="btn btn-info btn-contact-add">Add New Contact and Assign</a>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>'; //tab-pane
                        
                        //courses tab
                        html += '<div class="tab-pane fade" id="tabCourse' + i + '" role="tabpanel" aria-labelledby="courseDetails' + i + '">';
                        html += '<div class="card-body">';
                        html += '<table class="table table-striped" id="courseTable">';
                        html += '<thead>';
                        html += '<tr><th>Title</th><th>Number</th><!--<th>Instructor</th>--><th></th></tr>';
                        html += '</thead>';
                        html += '<tbody>';
                        for( var y = 0; y < progs[x].programs[i].courses.length; y++){
                            html += '<tr>';
                            html += '<td>' + progs[x].programs[i].courses[y].CourseTitle + '</td>';
                            html += '<td>' + progs[x].programs[i].courses[y].CourseNumber + '</td>';
                            //html += '<td>' + progs[x].programs[i].courses[y].instructor.InstructorName + '</td>';
                            html += '<td>';
                            html += '<a role="button" class="btn btn-info btn-block" href="/courses/display.php?id=' + progs[x].programs[i].courses[y].CourseId + '">View Course Details</a>';
                            html += '<a role="button" class="btn btn-warning btn-block" href="/courses/edit.php?id=' + progs[x].programs[i].courses[y].CourseId + '">Edit this Course</a>';
                            html += '<button id="id_' + progs[x].programs[i].courses[y].CourseId + '" name="courseDelete" type="submit" class="btn btn-danger btn-block btn-delete">Delete this Course</button>';  
                            html += '</td>';
                            html += '</tr>';
                        }
                        html += '</tbody>';
                        html += '</table>';
                        html += '</div>'; //card-body
                        html += '<div class="card-footer">';
                        html += '<a role="button" class="btn btn-primary" href="/courses/add.php?progId=' + progs[x].programs[i].ProgramId + '">Add Course</a>';
                        html += '</div>'; //card-footer
                        html += '</div>' //tab-pane;
                        html += '</div>'; //tab-content
                        html += '</div>'; //card
                        html += '<br/>';
                    }
                    html += '</div>';
                }
            }
            html += '</div>';
            html += '</div>';
            
            //college tab
            html += '<div class="tab-pane fade" id="tabCollege" role="tabpanel" aria-labelledby="collegeDetails">';
            html += '<div class="card-body">';
            for( var x = 0; x < progs.length; x++){
                if(progs[x].colleges.length < 1){
                    html += '<p>No colleges are currently assigned to this institution.</p>';
                }
                else {
                    html += '<div class="card-column">';
                    for(var i = 0; i < progs[x].colleges.length; i++){
                        html += '<div class="card">';
                        html += '<div class="card-header">';
                        html += '<h3>'+ progs[x].colleges[i].CollegeName + '</h3>';
                        html += '</div>';
                        html += '<div class="card-body">';
                        html += '<h4>Type</h4>';
                        html += '<p>' + progs[x].colleges[i].CollegeType + '</p>';
                        html += '<h4>Created</h4>';
                        html += '<p>' + progs[x].colleges[i].CollegeCreated + '</p>';
                        html += '<div class="btn-group">';
                        html += '<a role="button" class="btn btn-info mr-3" href="/colleges/display.php?id=' + progs[x].colleges[i].CollegeId + '">View College Details</a>';
                        html += '<a role="button" class="btn btn-warning mr-3" href="/colleges/edit.php?id=' + progs[x].colleges[i].CollegeId + '">Edit this College</a>';
                        html += '<button id="id_' + progs[x].colleges[i].CollegeId + '" name="collegeDelete" type="submit" class="btn btn-danger btn-college-delete">Delete this College</button>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '<br/>';
                    }
                    html += '</div>';
                }
            }
            html += '</div>';
            html += '</div>';
            
            //footer
            html += '<div class="card-footer">';
            html += '<div class="btn-group mr-3" role="group" aria-label="Add colleges or programs to this institution">';
            html += '<a role="button" class="btn btn-outline-primary" href="/colleges/add.php?instId=' + progs[0].InstitutionId + '">Add a College</a>';
            html += '<a role="button" class="btn btn-outline-primary" href="/programs/add.php?instId=' + progs[0].InstitutionId + '">Add a Program</a>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
            return html;
        }
        else {
            var html = '<h2>Institutions</h2><table class="table table-striped" id="usersTable"><thead><tr><th>Name</th><th>City</th><th>State</th><th>Deleted?</th><th></th></tr></thead><tbody>';
            for( var i = 0; i < progs.length; i++ ){
                html += '<tr>';
                html += '<td>' + progs[i].InstitutionName + '</td>';
                html += '<td>' + progs[i].InstitutionCity + '</td>';
                html += '<td>' + progs[i].InstitutionState + '</td>';
                if(progs[i].Deleted == 0){
                    html += '<td>No</td>';
                }
                else {
                    html += '<td>Yes</td>';
                }
                html += '<td><a class="btn btn-primary btn-block" href="/institutions/display.php?id=' + progs[i].InstitutionId + '">Details</a><a class="btn btn-info btn-block" href="/institutions/edit.php?id=' + progs[i].InstitutionId + '">Edit</a></td>';
                html += '</tr>';
            }
            html += '</tbody></table>';
            return html;
        }
    }
    
    
});
EOT;

//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = "Display Institution Information";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//put custom/extra JS files
$page_params['js'][] = array("text" => $custom_js );
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();