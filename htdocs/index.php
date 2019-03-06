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
        $('#delete').on( "click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            var conf = confirm( "Are you sure you want to delete this institution? This will deleted everything under the institution as well." );
            if( conf ){
                //let the form submit to the processor
                $('#deleteForm').submit();
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
        html += '<form action="/scripts/processInstitutionDeleteButton.php" method="POST" id="deleteForm"><input type="hidden" name="id" id="id" value="' + progs[0].InstitutionId + '" /><button id="delete" name="delete" type="submit" class="btn btn-danger">Delete this Institution</button></form>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        //colleges tab
        html += '<div class="tab-pane fade" id="tabProgram" role="tabpanel" aria-labelledby="programDetails">';
        html += '<div class="card-body">';
        html += '<h3>Program Details</h3>';
        html += '<h4>Name</h4>';
        html += '<p>' + progs[0].ProgramName + '</p>';
        html += '<h4>Type</h4>';
        html += '<p>' + progs[0].ProgramType + '</p>';
        html += '<h4>Delivery Method</h4>';
        html += '<p>' + progs[0].ProgramDelivery + '</p>';
        html += '<h4>Access</h4>';
        html += '<p>' + progs[0].ProgramAccess + '</p>';
        html += '<h4>Objectives</h4>';
        html += '<p>' + progs[0].ProgramObjectives + '</p>';
        html += '<h4>Full Time Duration</h4>';
        html += '<p>' + progs[0].ProgramFullTime + '</p>';
        html += '<h4>Part Time Duration</h4>';
        html += '<p>' + progs[0].ProgramPartTime + '</p>';
        html += '<h4>Testing Requirements</h4>';
        html += '<p>' + progs[0].ProgramTestingRequirements + '</p>';
        html += '<h4>Other Requirements</h4>';
        html += '<p>' + progs[0].ProgramOtherRequirements + '</p>';
        html += '<h4>Year Established</h4>';
        html += '<p>' + progs[0].ProgramEstablished + '</p>';
        html += '<h4>Scholarship</h4>';
        html += '<p>' + progs[0].ProgramScholarship + '</p>';
        html += '<h4>Credits</h4>';
        html += '<p>' + progs[0].ProgramCredits + '</p>';
        html += '<h4>Estimated Resident Tuition</h4>';
        html += '<p>' + progs[0].ProgramResidentTuition + '</p>';
        html += '<h4>Estimated Non-Resident Tuition</h4>';
        html += '<p>' + progs[0].ProgramNonResidentTuition + '</p>';
        html += '<h4>Cost Per Credit</h4>';
        html += '<p>' + progs[0].ProgramCostPerCredit + '</p>';
        html += '<h4>Analytics or O.R. Program</h4>';
        html += '<p>' + progs[0].ProgramAnalyticsOR + '</p>';
        html += '<h4>This Record Created On</h4>';
        html += '<p>' + progs[0].ProgramCreated + '</p>';
        html += '<h3>Contact Details</h3>';
        html += '<h4>Name</h4>';
        html += '<p>' + progs[0].ContactName + '</p>';
        html += '<h4>Title</h4>';
        html += '<p>' + progs[0].ContactTitle + '</p>';
        html += '<h4>Phone</h4>';
        html += '<p>' + progs[0].ContactPhone + '</p>';
        html += '<h4>Email</h4>';
        html += '<p>' + progs[0].ContactEmail + '</p>';
        html += '</div>';
        html += '</div>';
        //programs tab
        html += '<div class="tab-pane fade" id="tabCollege" role="tabpanel" aria-labelledby="collegeDetails">';
        html += '<div class="card-body">';
        html += '<h3>Name</h3>';
        html += '<p>' + progs[0].CollegeName + '</p>';
        html += '<h3>Type</h3>';
        html += '<p>' + progs[0].CollegeType + '</p>';
        html += '<h3>Created</h3>';
        html += '<p>' + progs[0].CollegeCreated + '</p>';
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