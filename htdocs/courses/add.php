<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 9:55 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to add courses to the system.';
    header('Location: ../users/login.php');
    die;
}

//get the current user
if(is_numeric($_SESSION['loggedIn'])) {
    $user = new User($_SESSION['loggedIn']);
}
else {
    //I don't think this should ever occur, but just in case its here!
    //or the system account
    $user = new User(1);
}

$content = '';

//get select list of instructors
$instructors = Instructor::getInstructors();

$instructorListHelper = array();
foreach($instructors as $inst){
    $instructorListHelper[] = array('text' => $inst['InstructorFirstName'] . ' ' . $inst['InstructorLastName'], 'value' => $inst['InstructorId']);
}
//pass the name/value pairs to the file to get the generated HTML for a select list
include_once('/common/classes/optionsHTML.php');
$instructorListHTML = optionsHTML($instructorListHelper);

//display the form for adding course info to the user
$content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processCourseAddForm.php" method="POST">
        <div class="form-row">
            <h3>Course Details</h3>
        </div>
        <div class="form-row"> 
            <label for="courseTitle">Title</label>
            <input type="text" class="form-control" name="courseTitle" id="courseTitle" placeholder="Title of course" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="courseNumber">Number</label>
            <input type="text" class="form-control" name="courseNumber" id="courseNumber" placeholder="Number of course" aria-describedby="numberHelp" />
            <p id="numberHelp">Any alphanumeric characters are allowed.</p>
        </div>
        <div class="form-row">
            <label for="instructor">Select an Instructor</label>
		    <select class="form-control" name="instructor" id="instructor">
		        $instructorListHTML
            </select>
        </div>
        <br />
        <div class="form-row"> 
            <label for="deliveryMethod">Delivery Method</label>
            <input type="text" class="form-control" name="deliveryMethod" id="deliveryMethod" placeholder="How the course is delivered" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="capstoneProject">Has Capstone Project?</label>
            <input type="text" class="form-control" name="capstoneProject" id="capstoneProject" aria-describedby="capstoneHelp" placeholder="Yes or No" />
            <p id="capstoneHelp">Please input yes if there is a capstone project, or no if there is none as of now.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="courseText">Course Text</label>
            <textarea class="form-control" name="courseText" id="courseText" aria-describedby="textHelp"></textarea>
            <p id="textHelp">You can copy-paste the contents of a syllabus in this field.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="analyticTag">Analytics Tags</label>
            <input type="text" class="form-control" name="analyticTag" id="analyticTag" placeholder="E.g. data mining; data visualization; optimization; etc." />
        </div>
        <br />
        <div class="form-row"> 
            <label for="businessTag">Business Tags</label>
            <input type="text" class="form-control" name="businessTag" id="businessTag" placeholder="E.g. entertainment; marketing; healthcare; etc." />
        </div>
        <br />
        <div class="form-row">
            <button class="btn btn-warning" type="submit" name="add" value="add">Submit New Course</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This course will not be added to the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add New Course";
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