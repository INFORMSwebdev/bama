<?php
//todo Don't forget to update the production servers /common/settings/common.ini file's analytics_education section
# This page is for anonymous users to request access to the system
# so an INFORMS admin will be notified of the request and have to
# approve or deny it. If approved, the user will be added to the
# users table and the UserId will be assigned to the requested
# institutions' AdminId column

//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../../init.php';

//set up variables in case user actually needs them
$content = '';
$page_params = array();

# ToDo: remove the testing query string before deploying anywhere
$registerFormProcessor = '../processRegisterForm.php';
if(isset($_GET['testing'])){
    $registerFormProcessor .= '?testing';
    $_SESSION['admin'] = true;
}

//check if user is logged in as an institution admin
if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true){
    if(isset($_SESSION['admin']) && $_SESSION['admin'] == true){
        //user is an INFORMS admin, let them see the regular page content
    } else {
        //user is an institution admin who already has a user account
        header('Location: profile.php');
        die;
    }
}
# ToDo: Add in checks for session variables that contain error input from a previously submitted login page!!

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

$page_title = '';
$commentBoxLabel = 'Comments';

if((isset($_SESSION['admin']) && $_SESSION['admin'] != TRUE) || isset($_GET['testing'])){
    //user is an INFORMS admin OR the system is being tested
    $page_title = 'Create a new Institution Administrator';
}
else{
    //user is anonymous
    $page_title = 'Become an Institution Administrator';
    $commentBoxLabel = 'Justification';
}

//user is anonymous, show them the Request for Access form
//set the form that will be displayed to users
$content = <<<EOT
<div class="row">
	<h1>Request for Program Administrator</h1>
</div>
<div class="row">
	<p>Please fill this form to submit a request to become an Institution Administrator.</p>
</div>
<div class="row">
	<form action="{$registerFormProcessor}" method="post">
		<div class="form-group">
			<label for="Username">Email Address</label>
			<input type="text" class="form-control" name="Username" id="Username" aria-describedby="UserNameHelp" placeholder="Email address is the username." required />
			<small id="UserNameHelp" class="form-text text-muted">This is a separate login from an INFORMS account.</small>
		</div>
		<div class="form-group">
			<label for="FirstName">First Name</label>
			<input type="text" class="form-control" name="FirstName" id="FirstName" placeholder="First Name" required />
			<!--<small id="FirstNameHelp" class="form-text text-muted">We could add in help text for international people here if needed</small>-->
		</div>
		<div class="form-group">
			<label for="LastName">Last Name</label>
			<input type="text" class="form-control" name="LastName" id="LastName" placeholder="Last Name" required />
			<!--<small id="LastNameHelp" class="form-text text-muted">We could add in help text for international people here if needed</small>-->
		</div>
		<div class="form-group">
		    <label for="Institution">Institution (select one)</label>
		    <select class="form-control" id="Institution" name="Institution" aria-describedby="InstitutionHelp" required>
		        $instListHTML
            </select>
            <small id="InstitutionHelp" class="form-text text-muted">Select the institution that you wish to be an administrator for.</small>
            <small id="InstitutionOther" class="form-text text-warning">If you do not see your institution in the list, please select the 'Other' option and specify your institution in the Justification box below.</small>
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

//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = $page_title;
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
$page_params['active_menu_item'] = 'users';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();
