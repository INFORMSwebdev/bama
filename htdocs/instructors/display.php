<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 10:30 AM
 */
//require the init file
require_once '../../init.php';

//get the caseId
$instructorId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

//check to make sure we have an Id to work with
if(empty($instructorId)) {
    //get list of instructor to display
    $instructors = Instructor::getInstructors();
    $instListHelper = array();
    foreach($instructors as $s){
        $instListHelper[] = array('text' => $s['InstructorFirstName'] . ' ' . $s['InstructorLastName'], 'value' => $s['InstructorId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $instListHTML = optionsHTML($instListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>View Instructor Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="course">Select an Instructor</label>
		    <select class="form-control" name="dataset" id="dataset" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        {$instListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}
else {
    # ToDo: determine if we want to display more info about the instructor on this page, like which institution/college/whatever they instruct at
    //display info about the specified instructor
    $instruc = new Instructor($instructorId);
    $firstName = $instruc->Attributes['InstructorFirstName'];
    $lastName = $instruc->Attributes['InstructorLastName'];
    $prefix = $instruc->Attributes['InstructorPrefix'];
    if(!isset($prefix) || empty($prefix)){
        $prefix = '';
    }
    $email = $instruc->Attributes['InstructorEmail'];
    $emailHTML = '';
    if(!isset($email) || empty($email)){
        $emailHTML = '<p>No email address on record for this instructor.</p>';
    }
    else {
        $emailHTML = "<p><a href='mailto:$email'>$email</a></p>";
    }

    $content = <<<EOT
<div class="card">
    <div class="card-header"> 
        <h2 class="display2">{$prefix} {$firstName} {$lastName}</h2>
    </div>
    <div class="card-body"> 
        <h3>Email</h3>
        {$emailHTML}
    </div>
</div>
EOT;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "View Instructor Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//$page_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
//$page_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
//$page_params['js'][] = array( 'text' => $customJS );
//$page_params['js'][] = array( 'text' => $custom_js );
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