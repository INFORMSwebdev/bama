<?php
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../../init.php';

//set up variables in case user actually needs them
$content = '';
$page_params = array();

$registerFormProcessor = '/scripts/processRegisterForm.php';

//check if user is logged in as an institution admin
if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true){
    if(isset($_SESSION['admin']) && $_SESSION['admin'] == true){
        //user is an INFORMS admin, let them see the regular page content
    }
    else {
        //user is an institution admin who already has a user account
        $_SESSION['registerMessage'] = 'You already have an account, you don\'t need to register again.';
        header('Location: profile.php');
        die;
    }
}

//get list of all institutions
$institutions = Institution::getInstitutions();
//turn that into an array of name/value pairs to pass to the optionsHTML.php file
$instListHelper = array();
foreach($institutions as $inst){
    $instListHelper[] = array('text' => $inst['InstitutionName'], 'value' => $inst['InstitutionId']);
}
$instListHelper[] = array('text' => 'Other', 'value' => 'Other');
//pass the name/value pairs to the file to get the generated HTML for a select list
include_once('/common/classes/optionsHTML.php');
$instListHTML = optionsHTML($instListHelper);

//user is anonymous
$page_title = 'Become an Institution Administrator';
$commentBoxLabel = 'Justification';

//user is anonymous, show them the Request for Access form
//set the form that will be displayed to users
$content = <<<EOT
<div class="flex-column">
	<h1>Request for Program Administrator</h1>
</div>
<div class="flex-column">
	<p>Please fill this form to submit a request to become an Institution Administrator. Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
	<form action="{$registerFormProcessor}" method="post">
		<div class="form-group">
			<label for="Username">Email Address</label><span class="text text-danger">*</span>
			<input type="text" class="form-control" name="Username" id="Username" aria-describedby="UserNameHelp" placeholder="Email address is the username." required />
			<small id="UserNameHelp" class="form-text text-muted">This is a separate login from an INFORMS account.</small>
		</div>
		<div class="form-group">
			<label for="FirstName">First Name</label><span class="text text-danger">*</span>
			<input type="text" class="form-control" name="FirstName" id="FirstName" placeholder="First Name" required />
			<!--<small id="FirstNameHelp" class="form-text text-muted">We could add in help text for international people here if needed</small>-->
		</div>
		<div class="form-group">
			<label for="LastName">Last Name</label><span class="text text-danger">*</span>
			<input type="text" class="form-control" name="LastName" id="LastName" placeholder="Last Name" required />
			<!--<small id="LastNameHelp" class="form-text text-muted">We could add in help text for international people here if needed</small>-->
		</div>
		
		<!--<div class="form-group">-->
		    <!--<label for="Institution">Institution (select one)</label><span class="text text-danger">*</span>-->
		    <!--<select class="form-control" id="Institution" name="Institution" aria-describedby="InstitutionHelp" required>-->
		        <!-- -->
            <!--</select>-->    
        <!--</div>-->
        
        <div class="form-group" id="instPickerContainer">
	        <div class="col-xs-6 form-group">
                <label for="inst">Institution</label>
                <select name="inst" id="inst" class="form-control"></select>
                <small id="InstitutionHelp" class="form-text text-muted">Select the institution that you wish to be an administrator for.</small>
                <small id="InstitutionOther" class="form-text text-warning">If you do not see your institution in the list, please select the 'Other' option and specify your institution in the Justification box below.</small>
            </div>
            <div class="col-xs-6 form-group">
                <label for="instFilter">Filter</label>
                <div id="instFilterContainer">
                    <input type="text" class="form-control"  id="instFilter" />
                    <button id="clearFilter" title="clear filter">X</button>
                </div>
            </div>
	    </div>
        
        
        <div class="form-group">
            <label for="Comments">{$commentBoxLabel}</label>
            <textarea class="form-control" id="Comments" name="Comments" rows="3"></textarea>
        </div>
		<div class="form-group">
			<button type="submit" class="btn btn-primary" value="Submit">Submit</button>
		</div>
	</form>
</div>
EOT;

$custom_css = <<<EOT
#instPickerContainer { display: grid; grid-template-columns: auto 250px; }
#instFilterContainer { position: relative; }
input#instFilter { padding-right: 25px; width: 100%; }
button#clearFilter { 
position:absolute; 
top:0; 
right: 0; 
width:25px; 
height: calc(2.25rem + 2px);
color: #f00; 
display: none;}

EOT;

$custom_js = <<<EOT
function fillInsts( filter ) {
  $('#inst').empty();
  $('#inst').append( $('<option>Loading...</option>' ));
  $('#inst').prop( "disabled", "disabled" );
  $.getJSON( "/scripts/ajax_getInstitutions.php", { 'filter': filter, 'crits': ['not-deleted','not-expired'] }, function( data ) {
    $('#inst').empty();
    $('#inst').append( $('<option value="0">(no selection)</option>' ));
    for( var i = 0; i < data.insts.length; i++ ) {
      var opt = $('<option value="'+data.insts[i].InstitutionId+'">'+data.insts[i].InstitutionName+'</option>');
      $('#inst').append( opt );
    }
    $('#inst').prop( "disabled", false );
  });
}
$(function() {
  fillInsts( null );
  $('#instFilter').on( 'click keyup', function (e) {
    if ($(this).val().length > 2 ) {
      fillInsts( $(this).val() );
      $('#clearFilter').show();
    }
  });
  $('#inviteForm').submit(function(e) {
    e.preventDefault();
    $.post( "/scripts/ajax_processInvite.php", $(this).serialize(), function(data) {
      if (data.errors.length > 0 ) {
        var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
        for (var i = 0; i < data.errors.length; i++) {
          msg +=  data.errors[i] + "\\r\\n";
        }
        alert( msg );
      }
      else if (data.msg) alert( data.msg );
      else alert( "Something went wrong." );
    }, "json");
  });
  $('#clearFilter').on( 'click keyup', function(e) {
    e.preventDefault();
    $('#instFilter').val( null );
    $('#clearFilter').hide();
    fillInsts( null );
  });
});
EOT;

//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = $page_title;
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//do not display the usual header/footer
$page_params['active_menu_item'] = 'users';
//put custom/extra css files, if used
$page_params['css'][] = array( 'text' => $custom_css );
$page_params['js'][] = array( 'text' => $custom_js );
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();
