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
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true) {
    //set up a message to display on the login page
    $_SESSION['logoutMessage'] = 'Please log in to edit course information.';
    //redirect to login page so user can log in
    header('Location: login.php');
    //don't want the script to keep executing after a redirect
    die;
}

//get the CourseId
$courseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

//get the user
$user = new User($_SESSION['loggedIn']);

//get courses user has permission to edit
$userCourses = $user->getCourses();

//get the options maker, its gonna be needed
include_once('/common/classes/optionsHTML.php');

if($courseId){
    //check if user has permission to edit this course
    if (!in_array($courseId, $userCourses) && !isset($_SESSION['admin'])) {
        //set up the message to be red
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'You do not have permission to edit the specified course\'s information.';

        //redirect to index
        header('Location: /index.php');
        die;
    }

    //get course details to put in form
    $co = new Course($courseId);

    $instructorId = $co->Attributes['InstructorId'];
    //get list of instructors
    $instructors = Instructor::getInstructors();
    $instructorListHelper = array();
    foreach($instructors as $inst){
        $instructorListHelper[] = array('text' => $inst['InstructorFirstName'] . ' ' . $inst['InstructorLastName'], 'value' => $inst['InstructorId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    $instructorListHTML = optionsHTML($instructorListHelper);
    //if an instructor is set, make the select list default to that selection
    if(isset($instructorId)){
        $instructorListHTML = str_replace('<option value="' . $instructorId . '">', '<option value="' . $instructorId . '" selected>', $instructorListHTML);
    }

    //set up the form to serve on the page
    $content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processCourseEditForm.php" method="POST">
        <div class="form-row">
            <h3>Course Details</h3>
        </div>
        <div class="form-row"> 
            <label for="courseTitle">Title</label>
            <input type="text" class="form-control" name="courseTitle" id="courseTitle" value="{$co->Attributes['CourseTitle']}" placeholder="Title of course" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="courseNumber">Number</label>
            <input type="text" class="form-control" name="courseNumber" id="courseNumber" value="{$co->Attributes['CourseNumber']}" placeholder="Number of course" aria-describedby="numberHelp" />
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
            <input type="text" class="form-control" name="deliveryMethod" id="deliveryMethod" value="{$co->Attributes['DeliveryMethod']}" placeholder="How the course is delivered" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="capstoneProject">Has Capstone Project?</label>
            <input type="text" class="form-control" name="capstoneProject" id="capstoneProject" value="{$co->Attributes['HasCapstoneProject']}" aria-describedby="capstoneHelp" placeholder="Yes or No" />
            <p id="capstoneHelp">Please input yes if there is a capstone project, or no if there is none as of now.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="courseText">Course Text</label>
            <textarea class="form-control" name="courseText" id="courseText" value="{$co->Attributes['CourseText']}" aria-describedby="textHelp"></textarea>
            <p id="textHelp">You can copy-paste the contents of a syllabus in this field.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="analyticTag">Analytics Tags</label>
            <input type="text" class="form-control" name="analyticTag" id="analyticTag" value="{$co->Attributes['AnalyticTag']}" placeholder="E.g. data mining; data visualization; optimization; etc." />
        </div>
        <br />
        <div class="form-row"> 
            <label for="businessTag">Business Tags</label>
            <input type="text" class="form-control" name="businessTag" id="businessTag" value="{$co->Attributes['BusinessTag']}" placeholder="E.g. entertainment; marketing; healthcare; etc." />
        </div>
        <br />
        <div class="form-row">
            <input type="hidden" id="courseId" name="courseId" value="{$courseId}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This Course</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">These changes will not take effect until they have been approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;
}
else {
    //course id either not an integer or not present in query string

    //display a list of courses to the user for them to select from THAT THEY HAVE PERMISSION TO EDIT
    $courseListHelper = array();
    foreach($userCourses as $courseFoo){
        $course = new Course($courseFoo);
        $courseListHelper[] = array('text' => $course->Attributes['CourseTitle'], 'value' => $course->Attributes['CourseId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    $courseListHTML = optionsHTML($courseListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>My Courses</h2>
    <p>Inside the list below are all the courses you have permissions to edit.</p>
</div>
<div class="flex-column">
    <h2>Edit Course Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Course">Select a Course to edit</label>
		    <select class="form-control" name="Course" id="Course" onchange="self.location='edit.php?id='+this.options[this.selectedIndex].value">
		        {$courseListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Course";
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