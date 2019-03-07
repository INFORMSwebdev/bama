<?php
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../init.php';

$content = '';
$custom_js = '';

if(isset($_SESSION['editMessage'])){
    //set up the alert color
    if($_SESSION['editMessage']['success'] == true){
        //successful insert into pending_updates table
        $content = '<div class="alert alert-success">';
    }
    else {
        //unsuccessful insert
        $content = '<div class="alert alert-danger">';
    }
    //add message to alert
    $content .= "<p>{$_SESSION['editMessage']['text']}</p></div>";

    //clear out the session variable after its' use
    $_SESSION['editMessage'] = null;
}

//check if user is logged in, if not then redirect them to the login page; GET string is only used for testing purposes
if ((!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] != true)) {
    //user is not logged in
    $content .= <<<EOT
<div class="jumbotron">
    <h1>Welcome to the Analytics &amp; Operations Research Eduction Admin Site!</h1>
    <p class="lead">Please log in to make updates to your program's information.</p>
    <a class="btn btn-primary btn-block" href="/users/login.php" role="button">Log In</a>
</div>
<div class="flex-column" id="programList">
    <!-- program info goes here when returned via ajax. -->
</div>
EOT;
}
else {
    //user is already logged in, get their userID from the session
    $user = new User($_SESSION['loggedIn']);
    $userName = $user->Attributes['Username'];
    if(isset($_SESSION['admin'])){
        $contentHelp = "<p>Welcome $userName! Below are all institutions. You can use the Search box to find records quickly.</p>";
    }
    else {
        $contentHelp = "<p>Welcome $userName! Below is the institution you administrate.</p><p>Please keep in mind that any updates will require INFORMS Administrator approval before changes are reflected on this site.</p>";
    }
    $content .= <<<EOT
<div class="flex-column">
	{$contentHelp}
</div>
<div class="container-fluid" id="programList">
    <!-- program info goes here when returned via ajax -->
</div>
EOT;
    //ajax related javascript
    $custom_js = <<<EOT
$(function() {
    $('#programList').html('<p>Loading data, please wait&hellip;</p>');
    $.getJSON( "/scripts/ajax_displayEditorInstitutions.php", function( data ) {
      if (data.errors.length > 0) { 
        var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
        for (var i = 0; i < data.errors.length; i++) {
          msg +=  data.errors[i] + "\\r\\n";
        }
        alert( msg );
      }
      else if (data.success == 1) {
        //redirect
        //window.location.href = "/admin/index.php";
        //process the returned info into HTML
        var helper = processProgramList(data.institutions);
        //display the returned info in the div
        $('#programList').html(helper);
        $('#usersTable').DataTable();
        $('#instDelete').on( "click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            var conf = confirm( "Are you sure you want to delete this institution? This will deleted everything under the institution as well." );
            if( conf ){
                //let the form submit to the processor
                $('#instDeleteForm').submit();
            }
            //otherwise do nothing
        });
        $('#programDelete').on( "click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            var conf = confirm( "Are you sure you want to delete this program? This will deleted everything under the program as well." );
            if( conf ){
                //let the form submit to the processor
                $('#progDeleteForm').submit();
            }
            //otherwise do nothing
        });
        $('#collegeDelete').on( "click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            var conf = confirm( "Are you sure you want to delete this college? This will deleted everything under the college as well." );
            if( conf ){
                //let the form submit to the processor
                $('#collegeDeleteForm').submit();
            }
            //otherwise do nothing
        });
      }
    });
  
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
        html += '<h4>Access</h4>';
        if(progs[0].InstitutionAccess.indexOf("www") < 0){
            html += '<p>' + progs[0].InstitutionAccess + '</p>';
        }
        else{
            html += '<p><a href="' + progs[0].InstitutionAccess + '" target="_blank">' + progs[0].InstitutionAccess + '</a></p>';
        }
        html += '<h3>Last Modified</h3>';
        html += '<p>' + progs[0].LastModifiedDate + '</p>';
        html += '<div class="btn-group">';
        html += '<a role="button" class="btn btn-warning mr-3" href="/institutions/edit.php?id=' + progs[0].InstitutionId + '">Edit this Institution</a>';
        html += '<form action="/scripts/processInstitutionDeleteButton.php" method="POST" id="instDeleteForm"><input type="hidden" name="id" id="id" value="' + progs[0].InstitutionId + '" /><button id="instDelete" name="instDelete" type="submit" class="btn btn-danger">Delete this Institution</button></form>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        
        //programs tab
        html += '<div class="tab-pane fade" id="tabProgram" role="tabpanel" aria-labelledby="programDetails">';
        html += '<div class="card-body">';
        for(var x = 0; x < progs.length; x++){
            //there is either only 1 program returned or a message that no programs available
            if(progs[x].programs[0].length == 1){
                //no programs for this institution
                html += '<p>No programs are currently assigned to this institution.</p>';
            }
            else {
                html += '<div class="card-deck">';
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
                    html += '<p>' + progs[x].programs.ProgramOtherRequirements + '</p>';
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
                    html += '<h3>Contact Details</h3>';
                    html += '<h4>Name</h4>';
                    html += '<p>' + progs[x].programs[i].ContactName + '</p>';
                    html += '<h4>Title</h4>';
                    html += '<p>' + progs[x].programs[i].ContactTitle + '</p>';
                    html += '<h4>Phone</h4>';
                    html += '<p>' + progs[x].programs[i].ContactPhone + '</p>';
                    html += '<h4>Email</h4>';
                    html += '<p>' + progs[x].programs[i].ContactEmail + '</p>';
                    html += '<div class="btn-group">';
                    html += '<a role="button" class="btn btn-warning mr-3" href="/programs/edit.php?id=' + progs[x].programs[i].ProgramId + '">Edit this Program</a>';
                    html += '<form action="/scripts/processProgramDeleteButton.php" method="POST" id="progDeleteForm"><input type="hidden" name="id" id="id" value="' + progs[x].colleges[i].ProgramId + '" /><button id="programDelete" name="programDelete" type="submit" class="btn btn-danger">Delete this Program</button></form>';
                    html += '</div>'; //button-group
                    html += '</div>'; //body
                    html += '</div>'; //tab-pane
                    
                    //courses tab
                    html += '<div class="tab-pane fade" id="tabCourse' + i + '" role="tabpanel" aria-labelledby="courseDetails' + i + '">';
                    html += '<div class="card-body">';
                    
                    html += '</div>'; //card-body
                    html += '</div>' //tab-pane;
                    html += '</div>'; //tab-content
                    html += '</div>'; //card
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
            if(progs[x].colleges[0].length == 1){
                html += '<p>No colleges are currently assigned to this institution.</p>';
            }
            else {
                html += '<div class="card-deck">';
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
                    html += '<a role="button" class="btn btn-warning mr-3" href="/colleges/edit.php?id=' + progs[x].colleges[i].CollegeId + '">Edit this College</a>';
                    html += '<form action="/scripts/processCollegeDeleteButton.php" method="POST" id="collegeDeleteForm"><input type="hidden" name="id" id="id" value="' + progs[x].colleges[i].CollegeId + '" /><button id="collegeDelete" name="collegeDelete" type="submit" class="btn btn-danger">Delete this College</button></form>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
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
    //update this stuff to be institution display info instead of program
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
}

$custom_css = <<<EOT
td.responsiveCol {
    overflow-wrap: break-word;
    max-width: 250px;
}
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Programs Dashboard";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['js'][] = array( 'text' => $custom_js );
$page_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
$page_params['css'][] = array( 'text' => $custom_css );
$page_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
//$page_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js');
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