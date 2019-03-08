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

$customJS = '';

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

    # ToDo: figure out what to do about the instructor field in this form

    //set up the form to serve on the page
    $content = <<<EOT
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h2>Course Edit</h2>
            <ul class="nav nav-tabs card-header-tabs" id="cardNav" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="courseDetails" href="#tabCourse" data-toggle="tab" aria-selected="true" aria-controls="tabCourse">Course Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="courseInstructors" href="#tabInstructors" data-toggle="tab" aria-selected="false" aria-controls="tabInstructors">Instructors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="courseSoftware" href="#tabSoftware" data-toggle="tab" aria-selected="false" aria-controls="tabSoftware">Software</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="courseTextbooks" href="#tabTextbooks" data-toggle="tab" aria-selected="false" aria-controls="tabTextbooks">Textbooks</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="courseStudies" href="#tabStudies" data-toggle="tab" aria-selected="false" aria-controls="tabStudies">Case Studies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="courseDatasets" href="#tabDatasets" data-toggle="tab" aria-selected="false" aria-controls="tabDatasets">Datasets</a>
                </li>
            </ul>
        </div>
        <div class="tab-content" id="CourseTabContent">
            <div class="tab-pane fade show active" id="tabCourse" role="tabpanel" aria-labelledby="courseDetails">
                <div class="card-body">
                    <form>
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
                        <!--<br/>-->
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
                            <textarea class="form-control" name="courseText" id="courseText" aria-describedby="textHelp">{$co->Attributes['CourseText']}</textarea>
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
            </div>
            <div class="tab-pane fade" id="tabInstructors" role="tabpanel" aria-labelledby="courseInstructors">
                <div class="card-body">
                    <h3>Instructors</h3>
                </div>
            </div>
            <div class="tab-pane fade" id="tabSoftware" role="tabpanel" aria-labelledby="courseSoftware">
                <div class="card-body">
                    <h3>Software</h3>
                </div>
            </div>
            <div class="tab-pane fade" id="tabTextbooks" role="tabpanel" aria-labelledby="courseTextbooks">
                <div class="card-body">
                    <h3>Textbooks</h3>
                </div>
            </div>
            <div class="tab-pane fade" id="tabStudies" role="tabpanel" aria-labelledby="courseStudies">
                <div class="card-body">
                    <h3>Case Studies</h3>
                </div>
            </div>
            <div class="tab-pane fade" id="tabDatasets" role="tabpanel" aria-labelledby="courseDatasets">
                <div class="card-body">
                    <h3>Datasets</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="instructorModal" tabindex="-1" role="form" aria-labelledby="instructorModalTitle" aria-hidden="true">
    <div class="modal-dialog" role="form">
        <div class="modal-content">
            <div class="modal-header"> 
                <div class="modal-title" id="instructorModalTitle">Add New Instructor</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"> 
                <form id="instructorAddForm">
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
		                <input type="text" class="form-control" name="prefix" id="prefix" placeholder="e.g. Mister, Mr., Professor, Doctor, etc." />
                    </div>
                    <br />
                    <div class="form-row"> 
                        <label for="email">Email</label>
                        <input type="text" class="form-control" name="email" id="email" placeholder="Email address of instructor" aria-describedby="emailHelp" />
                        <p id="emailHelp">Only valid email addresses will be accepted (e.g. name@organization.com)</p>
                    </div>
                    <!--<br />-->
                    <div class="form-row">
                        <input type="hidden" id="courseId" name="courseId" value="{$co->id}" />
                        <button class="btn btn-warning" type="submit" name="addInstructor" id="instructorSubmit" value="addInstructor">Submit New Instructor</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer"> 
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
EOT;

    $customJs = <<<EOT
$(function() {
    $(document).on( 'click', '.btn-addInstructor', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#instructorModal').modal('toggle');
    });
    $(document).on( 'click', '#instructorSubmit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        //get form info
        var firstName = $('#firstName').val();
        var lastName = $('#lastName').val();
        var prefix = $('#prefix').val();
        var email = $('#email').val();
        $.post( "/scripts/ajax_addInstructorFromCourse.php", { 'FirstName': firstName, 'LastName': lastName, 'Prefix': prefix, 'Email': email }, function(data) {
            //close the modal
            $('#instructorModal').modal('toggle');
            //alert( data );
            if (data.errors.length > 0 ) {
                var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                for (var i = 0; i < data.errors.length; i++) {
                    msg +=  data.errors[i] + "\\r\\n";
                }
                //alert( msg );
                //$('#message').html('<p>' + msg + '</p>').removeClass('d-hidden').addClass('alert alert-danger');
                $('#message').html('<p>' + msg + '</p>');
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
        }, "json"); //, "json"
    });
});
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

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Course";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['js'][] = array( 'text' => $customJs );
//$page_params['active_menu_item'] = 'home';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();