<?php
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../init.php';

# ToDo: remove this testing stuff before actual deployment!
if (isset($_GET['testing'])) {
    $_SESSION['loggedIn'] = 41;
}

$content = '';
$custom_js = '';

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
} else {
    //user is already logged in, get their userID from the session
    $user = new User($_SESSION['loggedIn']);
    $userName = $user->Attributes['Username'];
    $content .= <<<EOT
<div class="jumbotron">
	<h1 class="display-4">Welcome $userName!</h1>
	<p class="lead">Below are the programs you are an administrator of.</p>
	<p>Please give the list a couple seconds to load.</p>
</div>
<div class="container-fluid" id="programList">
    <!-- program info goes here when returned via ajax -->
</div>
EOT;
    //ajax related javascript
    $custom_js = <<<EOT
$(function() {
    $.get( "/scripts/ajax_displayEditorPrograms.php", function( data ) {
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
        $('#usersTable').DataTable();
      }
    }, "json");
  
  function processProgramList(progs, origin){
    if(progs.length < 1) {
        //there are no programs in the passed list
        var foo = '<p class="text text-danger">No programs available to display right now, please try again later.</p>';
        return foo;
    } else {
        var html= '<h2>My Programs</h2><table class="table" id="usersTable"><thead><tr><th>Name</th><th>Institution</th><th>Type</th><th>Delivery Method</th><th>Testing Requirements</th><th>Link</th><th></th></tr></thead><tbody>';
        for( var i = 0; i < progs.length; i++ ){
            html += '<tr>';
            html += '<td>' + progs[i].ProgramName + '</td>';
            html += '<td><a href="/institutions/display.php?id=' + progs[i].InstitutionId + '">' + progs[i].InstitutionName + '</a></td>';
            html += '<td>' + progs[i].ProgramType + '</td>';
            html += '<td>' + progs[i].DeliveryMethod + '</td>';
            html += '<td>' + progs[i].TestingRequirement + '</td>';
            html += '<td><a target="_blank" class="text-wrap" href="' + progs[i].ProgramAccess + '">' + progs[i].ProgramAccess + '</a></td>';
            html += '<td><a class="btn btn-primary" href="/programs/display.php?id=' + progs[i].ProgramId + '">Details</a><a class="btn btn-info" href="/programs/edit.php?id=' + progs[i].ProgramId + '">Edit</a></td>';
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

EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Programs Dashboard";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
$page_params['js'][] = array( 'text' => $custom_js );
$page_params['css'][] = array( 'text' => $custom_css );
//$page_params['css'][] = array( 'url' => 'https://common.informs.org/js/DataTables-1.9.4/media/css/jquery.dataTables.css' );
//$page_params['js'][] = array( 'url' => 'https://common.informs.org/js/DataTables-1.9.4/media/js/jquery.dataTables.min.js' );
$page_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
$page_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
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