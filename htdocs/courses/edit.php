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
if(isset($_SESSION['admin']) && $_SESSION['admin'] == true){
    $userCourses = Course::getAllCourses();
}
else {
    $userCourses = $user->getCourses();
}

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

    //get the current course instructors
    $courseInstructors = $co->getInstructors();

    //$instructor = $co->Attributes['InstructorId'];

    //get list of instructors
    $instructors = $user->getInstructors(TRUE);
    $instructorListHelper = '';
    if($instructors){
        foreach($instructors as $instr){
            $inst = new Instructor($instr);
            $name = $inst->Attributes['InstructorFirstName'] . ' ' . $inst->Attributes['InstructorLastName'];
            $instructorListHelper .= "<input type='checkbox' class='form-check-input' value='{$inst->id}' id='id_{$inst->id}'><label class='form-check-label'>{$name}</label>";
        }
        if($courseInstructors) {
            foreach ($courseInstructors as $cI) {
                //if (in_array("value='{$cI}'")) ;
                $instructorListHelper = str_replace("value='" . $cI['InstructorId'] . "'", "value='" . $cI['InstructorId'] . "' checked ", $instructorListHelper);
            }
        }
    }
    else {
        $instructorListHelper = "No instructors are associated with your institution. Please click the \'Create New Instructor\' button.";
    }

    //pass the name/value pairs to the file to get the generated HTML for a select list


    //if an instructor is set, make the select list default to that selection
    $instructorListHTML = $instructorListHelper;
    if(isset($instructorId)){
        //$instructorListHTML = str_replace('<option value="' . $instructorId . '">', '<option value="' . $instructorId . '" selected>', $instructorListHTML);
    }
    else {
        //$instructorListHTML = optionsHTML($instructorListHelper);
    }

    $deliveryMethods = $co->getDeliveryMethodOptions();

    $tags = $co->getTagIds();
    $tagHTML = Course::renderTagHTML( $tags );

    //set up the form to serve on the page
    $content = <<<EOT
<div class="flex-column">
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h2>Edit Course Details</h2>
        </div>
        <div class="card-body">
            <form action="/scripts/processCourseEditForm.php" method="POST">
                <div class="form-row"> 
                    <label for="courseTitle">Title</label><span class="text text-danger">*</span>
                    <input type="text" class="form-control" name="courseTitle" id="courseTitle" value="{$co->Attributes['CourseTitle']}" placeholder="Title of course" required />
                </div>
                <br />
                <div class="form-row"> 
                    <label for="courseNumber">Number</label>
                    <input type="text" class="form-control" name="courseNumber" id="courseNumber" value="{$co->Attributes['CourseNumber']}" placeholder="e.g. MSB101" aria-describedby="numberHelp" />
                    <p id="numberHelp">Any alphanumeric characters are allowed.</p>
                </div>
                <!--<br/>-->
                <div class="form-row"> 
                    <label for="deliveryMethod">Delivery Method</label>
                    <select name="deliveryMethod" id="deliveryMethod" class="form-control">
                        {$deliveryMethods}
                    </select>
                </div>
                <br />
                <div class="form-row"> 
                    <label for="programmingLanguage">Software/Programming Language</label>
                    <textarea class="form-control" name="programmingLanguage" id="programminLanguage" aria-describedby="textHelp">{$co->Attributes['ProgrammingLanguage']}</textarea>
                    <p id="textHelp">Describe any software or programming language requirements relevant to this course.</p>
                </div>
                <br/>
                <div class="form-row"> 
                    <h3>Course Tags</h3>
                </div>
                <div class="form-row"> 
                    $tagHTML
                </div>
                <!--<div class="form-row"> 
                    <label for="capstoneProject">Has Capstone Project?</label>
                    <input type="text" class="form-control" name="capstoneProject" id="capstoneProject" value="{$co->Attributes['HasCapstoneProject']}" aria-describedby="capstoneHelp" placeholder="Yes or No" />
                    <p id="capstoneHelp">Please input yes if there is a capstone project, or no if there is none as of now.</p>
                </div>-->
                <!--<br />-->
                <!--<div class="form-row"> 
                    <label for="courseText">Course Text</label>
                    <textarea class="form-control" name="courseText" id="courseText" aria-describedby="textHelp">{$co->Attributes['CourseText']}</textarea>
                    <p id="textHelp">You can copy-paste the contents of a syllabus in this field.</p>
                </div>-->
                <!--<br />-->
                <!--<div class="form-row"> 
                    <label for="analyticTag">Analytics Tags</label>
                    <input type="text" class="form-control" name="analyticTag" id="analyticTag" value="{$co->Attributes['AnalyticTag']}" placeholder="E.g. data mining; data visualization; optimization; etc." />
                </div>
                <br />
                <div class="form-row"> 
                    <label for="businessTag">Business Tags</label>
                    <input type="text" class="form-control" name="businessTag" id="businessTag" value="{$co->Attributes['BusinessTag']}" placeholder="E.g. entertainment; marketing; healthcare; etc." />
                </div>
                <br />-->
                <div class="form-row">
                    <input type="hidden" id="courseId" name="courseId" value="{$courseId}" />
                    <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
                    <button class="btn btn-danger btn-course-delete" type="submit" name="delete" id="id_{$courseId}" value="delete">Delete This Course</button>
                </div>
                <!--<br />-->
                <div class="form-row">
                    <p class="lead">These changes will not take effect until they have been approved by an INFORMS administrator.</p>
                </div>
            </form>
        </div>
        <div class="card-footer"> 
            <div class="btn-group"> 
                <a role="button" class="btn btn-outline-primary" href="/instructors/add.php?courseId={$courseId}">Add an Instructor</a>
                <a role="button" class="btn btn-outline-primary" href="/datasets/add.php?courseId={$courseId}">Add a Dataset</a>
                <a role="button" class="btn btn-outline-primary" href="/cases/add.php?courseId={$courseId}">Add a Case Study</a>
                <a role="button" class="btn btn-outline-primary" href="/textbooks/add.php?courseId={$courseId}">Add a Textbook</a>
                <a role="button" class="btn btn-outline-primary" href="/software/add.php?courseId={$courseId}">Add Software</a>
            </div>
        </div>        
    </div>
</div>
<br />
<div class="flex-column">
    <a href="/courses/display.php?id={$co->id}" role="button" class="btn btn-primary">View Course Details Page</a>
</div>
EOT;
}
else {
    //course id either not an integer or not present in query string

    //display a list of courses to the user for them to select from THAT THEY HAVE PERMISSION TO EDIT
    $courseListHelper = array();
    foreach($userCourses as $courseFoo){
        if(isset($_SESSION['admin'])){
            $courseListHelper[] = array('text' => $courseFoo['CourseTitle'], 'value' => $courseFoo['CourseId']);
        }
        else {
            $course = new Course($courseFoo);
            $courseListHelper[] = array('text' => $course->Attributes['CourseTitle'], 'value' => $course->Attributes['CourseId']);
        }
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

$customJS = <<<EOT
$(function() {
    //course delete button functionality
    $(document).on( 'click', '.btn-course-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this course?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteCourse.php", { 'CourseId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                        //alert( msg );
                        $('#message').html('<p>' + msg + '</p>')
                        $('#message').addClass('alert alert-danger');
                        $('#message').show();
                    }
                else if (data.msg) {
                    //alert( data.msg );
                    $('#message').html('<p>' + data.msg + '</p>');
                    if(data.msg.includes('submitted')){
                        $('#message').addClass('alert alert-success');
                    }
                    else {
                        $('#message').addClass('alert alert-danger');
                    }
                    $('#message').show();
                }
            }, "json");
        }
    });
});
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Course";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['js'][] = array( 'text' => $customJS );
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();