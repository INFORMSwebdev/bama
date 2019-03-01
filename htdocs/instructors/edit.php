<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 10:31 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true) {
    //set up a message to display on the login page
    $_SESSION['logoutMessage'] = 'Please log in to edit instructor information.';
    //redirect to login page so user can log in
    header('Location: login.php');
    //don't want the script to keep executing after a redirect
    die;
}

//get the user
$user = new User($_SESSION['loggedIn']);

//get the instructorId
$instId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

//get the instructors this user can edit
$userInsts = $user->getInstructors();

//check to make sure we have a valid instructorId
if($instId){
    //check if user has permission to edit this instructor
    if (!in_array($instId, $userInsts)) {
        //set up the message to be red
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'You do not have permission to edit the specified instructor\'s information.';

        //redirect to index
        header('Location: /index.php');
        die;
    }

    //gather info to pre-fill form
    $instructor = new Instructor($instId);
    $fName = $instructor->Attributes['InstructorFirstName'];
    $lName = $instructor->Attributes['InstructorLastName'];
    $prefix = $instructor->Attributes['InstructorPrefix'];
    $email = $instructor->Attributes['InstructorEmail'];

    //display form to user
    $content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processInstructorAddForm.php" method="POST">
        <div class="form-row">
            <h3>Instructor Details</h3>
        </div>
        <div class="form-row"> 
            <label for="firstName">First Name</label>
            <input type="text" class="form-control" name="firstName" id="firstName" placeholder="First name of instructor" value="{$fName}" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="lastName">Last Name</label>
            <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Last name/surname of instructor" value="{$lName}" required />
        </div>
        <br />
        <div class="form-row">
            <label for="prefix">Prefix</label>
		    <input type="text" class="form-control" name="prefix" id="prefix" placeholder="e.g. Mister, Mr. Professor, Doctor, etc." value="{$prefix}" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="email">Email</label>
            <input type="text" class="form-control" name="email" id="email" placeholder="Email address of instructor" aria-describedby="emailHelp" value="{$email}" />
            <p id="emailHelp">Only valid email addresses will be accepted (e.g. name@organization.com)</p>
        </div>
        <!--<br />-->
        <div class="form-row">
            <input type="hidden" id="instId" name="instId" value="{$instId}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This Instructor</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This institution will not be added to the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;
}
else {
    include_once('/common/classes/optionsHTML.php');

    //display a list of instructors to the user for them to select from THAT THEY HAVE PERMISSION TO EDIT
    $instListHelper = array();
    foreach($userInsts as $foo){
        $inst = new Instructor($foo);
        $instListHelper[] = array('text' => $inst->Attributes['InstructorFirstName'] . ' ' . $inst->Attributes['InstructorLastName'], 'value' => $inst->Attributes['InstructorId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    $instListHTML = optionsHTML($instListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>Edit Instructor Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Course">Select an Instructor to edit</label>
		    <select class="form-control" name="Course" id="Course" onchange="self.location='edit.php?id='+this.options[this.selectedIndex].value">
		        {$instListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Instructor";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
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