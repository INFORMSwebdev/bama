<?php
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../init.php';

# ToDo: remove this testing stuff before actual deployment!
if (isset($_GET['testing'])) {
    $_SESSION['loggedIn'] = 41;
}

$content = '';

# ToDo: add in check for messages to display that come from other pages/scripts
if(isset($_SESSION['editMessage'])){
    //set up the alert color
    if($_SESSION['editMessage']['success'] == true){
        //successful insert into pending_updates table
        $content = '<div class="alert alert-success">';
    } else {
        //unsuccessful insert
        $content = '<div class="alert alert-danger">';
    }
    //add message to alert
    $content .= "<p>{$_SESSION['editMessage']['text']}</p></div>";

    //clear out the session variable after its' use
    $_SESSION['editMessage'] = null;
}

//check if user is logged in, if not then redirect them to the login page; GET string is only used for testing purposes
# ToDo: remove the GET string from this test before actual use
if ((!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] != true)) {
    //user is not logged in
    $content .= <<<EOT
<div class="jumbotron">
    <h1>Welcome to the Analytics &amp; Operations Research Eduction Program Listing Site!</h1>
    <p class="lead">You will find information on many different Analytics and Operations Research (O.R.) programs offered by universities around the U.S.</p>
    <hr class="my-4" />
    <a class="btn btn-primary" href="#" name="all" id="all" role="button">Display All Programs</a>
    <a class="btn btn-primary" href="#" name="analytics" id="analytics" role="button">Display All Analytics Programs</a>
    <a class="btn btn-primary" href="#" name="or" id="or" role="button">Display All O.R. Programs</a>
</div>
<div class="flex-column" id="programList">
    <!-- program info goes here when returned via ajax. -->
</div>
EOT;
} else {
    //user is already logged in, get their userID from the session
    $user = new User($_SESSION['loggedIn']);
    $userName = $user->Attributes['Username'];
    $content .= <<<EOT
<div class="jumbotron">
	<h1 class="display-4">Welcome $userName!</h1>
	<p class="lead">Message can go here about system</p>
	<hr class="my-4" />
	<a class="btn btn-primary" href="#" name="mine" id="mine" role="button">Display my programs</a>
	<a class="btn btn-primary" href="#" name="all" id="all" role="button">Display All Programs</a>
	<a class="btn btn-primary" href="#" name="analytics" id="analytics" role="button">Display All Analytics programs</a>
	<a class="btn btn-primary" href="#" name="or" id="or" role="button">Display All O.R. programs</a>
</div>
<div class="flex-column" id="programList">
    <!-- program info goes here when returned via ajax. Might have to add d-block to the class to circumvent flex display. -->
</div>
EOT;
}
//ajax related javascript
$custom_js = <<<EOT
$(function() {
  $('#all').click(function(e) {
    e.preventDefault();
    $.get( "/scripts/ajax_displayAllPrograms.php", function( data ) {
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
        var helper = processProgramList(data.programs, 'all');
        //display the returned info in the div
        $('#programList').html(helper);
      }
    }, "json");
  });
  
  $('#mine').click(function(e) {
    e.preventDefault();
    $.get( "/scripts/ajax_displayEditorPrograms.php", function( data ) {
      if (data.errors.length > 0) { 
        var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
        for (var i = 0; i < data.errors.length; i++) {
          msg +=  data.errors[i] + "\\r\\n";
        }
        alert( msg );
      }
      else if (data.success == 1) {
        //process the returned info into HTML
        var helper = processProgramList(data.programs, 'mine');
        //display the returned info in the div
        $('#programList').html(helper);
      }
    }, "json");
  });
  
  $('#analytics').click(function(e) {
    e.preventDefault();
    $.get( "/scripts/ajax_displayAnalyticsPrograms.php", function( data ) {
      if (data.errors.length > 0) { 
        var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
        for (var i = 0; i < data.errors.length; i++) {
          msg +=  data.errors[i] + "\\r\\n";
        }
        alert( msg );
      }
      else if (data.success == 1) {
        //process the returned info into HTML
        var helper = processProgramList(data.programs, 'analytics');
        //display the returned info in the div
        $('#programList').html(helper);
      }
    }, "json");
  });
  
  $('#or').click(function(e) {
    e.preventDefault();
    $.get( "/scripts/ajax_displayORPrograms.php", function( data ) {
      if (data.errors.length > 0) { 
        var msg;
        if(data.errors[0].indexOf('Operations Research') > 0){
            //only contains the 1 message that there are currently no OR programs, so just display that in the div
            msg = '<p class="text text-info">' + data.errors[0] + '</p>';
            $('#programList').html(msg);
        }
        else {
            msg = 'One or more errors were encountered:\\r\\n\\r\\n';
            for (var i = 0; i < data.errors.length; i++) {
                msg +=  data.errors[i] + "\\r\\n";
            }
            alert( msg );
        }
      }
      else if (data.success == 1) {
        //process the returned info into HTML
        var helper = processProgramList(data.programs, 'or');
        //display the returned info in the div
        $('#programList').html(helper);
      }
    }, "json");
  });
  
  function processProgramList(progs, origin){
    if(progs.length < 1) {
        //there are no programs in the passed list
        var foo = '<p class="text text-danger">No programs available to display right now, please try again later.</p>';
        return foo;
    } else {
        var html;
        if(origin == 'or') {
            html = '<h2>Operations Research Programs</h2>';
        } else if (origin == 'analytics') {
            html = '<h2>Analytics Programs</h2>';
        } else if (origin == 'all') {
            html = '<h2>All Programs</h2>';
        } else if (origin == 'mine') {
            html = '<h2>My Programs</h2>';
            
        } else {
            html = '<h2>Programs</h2>';
        }
        html += '<table class="table"><thead><tr><th>Name</th><th>Institution</th><th>Type</th><th>Delivery Method</th><th>Testing Requirements</th><th>Link</th><th>View</th></tr></thead>';
        for( var i = 0; i < progs.length; i++ ){
            html += '<tr>';
            html += '<td>' + progs[i].ProgramName + '</td>';
            html += '<td><a href="/institutions/display.php?id=' + progs[i].InstitutionId + '">' + progs[i].InstitutionName + '</a></td>';
            html += '<td>' + progs[i].ProgramType + '</td>';
            html += '<td>' + progs[i].DeliveryMethod + '</td>';
            html += '<td>' + progs[i].TestingRequirement + '</td>';
            html += '<td><a target="_blank" href="' + progs[i].ProgramAccess + '">' + progs[i].ProgramAccess + '</a></td>';
            if( origin == 'mine' ){
                html += '<td><a class="btn btn-info" href="/programs/edit.php?id=' + progs[i].ProgramId + '">Edit</a></td>';
            } else {
                html += '<td><a class="btn btn-info" href="/programs/display.php?id=' + progs[i].ProgramId + '">More Details</a></td>';
            }
            html += '</tr>';
        }
        return html;
    }
  }
});
EOT;

# ToDo: make sure the access links wrap in the table properly!!

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Programs Dashboard";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
$page_params['js'][] = array( 'text' => $custom_js );
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