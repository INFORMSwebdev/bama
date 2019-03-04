<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 10:30 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to add instructors to the system.';
    header('Location: ../users/login.php');
    die;
}

//get current user
$user = new User($_SESSION['loggedIn']);

//check if there's a given courseId to associate the new instructor w/
$courseId = filter_input(INPUT_GET, 'courseId', FILTER_VALIDATE_INT);
$courseInputHTML = '';
if($courseId){
    $courseInputHTML = "<input type='hidden' id='courseId' name='courseId' value='$courseId' />";
}

//display the form for adding institution info to the user
$content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processInstructorAddForm.php" method="POST">
        <div class="form-row">
            <h3>Instructor Details</h3>
        </div>
        <div class="form-row"> 
            <label for="firstName">First Name</label>
            <input type="text" class="form-control" name="firstName" id="firstName" placeholder="First name of instructor" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="lastName">Last Name</label>
            <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Last name/surname of instructor" required />
        </div>
        <br />
        <div class="form-row">
            <label for="prefix">Prefix</label>
		    <input type="text" class="form-control" name="prefix" id="prefix" placeholder="e.g. Mister, Mr. Professor, Doctor, etc." />
        </div>
        <br />
        <div class="form-row"> 
            <label for="email">Email</label>
            <input type="text" class="form-control" name="email" id="email" placeholder="Email address of instructor" aria-describedby="emailHelp" />
            <p id="emailHelp">Only valid email addresses will be accepted (e.g. name@organization.com)</p>
        </div>
        <!--<br />-->
        <div class="form-row">
            {$courseInputHTML}
            <button class="btn btn-warning" type="submit" name="add" value="add">Submit New Instructor</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This institution will not be added to the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add New Instructor";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
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