<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/9/2019
 * Time: 9:07 PM
 */
//require the init file
require_once '../../init.php';

//get the courseId
$courseId = filter_input(INPUT_GET, 'courseId', FILTER_VALIDATE_INT);

if(!$courseId){
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'Invalid CourseId, please try again.';

    header('Location: index.php' );
    die;
}

$content = '';

//get list of instructors
$course = new Course($courseId);
$progId = $course->getProgram();
$prog = new Program($progId[0]);
$instructors = $prog->getInstructors();

if($instructors){
    //set up the info to put into checkboxes
    $instructorChecklist = '';

    $courseInsts = $course->getInstructors();

    foreach($instructors as $inst){

        $foo = new Instructor($inst);
        $name = $foo->Attributes['InstructorFirstName'] . ' '  . $foo->Attributes['InstructorLastName'];

        if(!$courseInsts) {
            $instructorChecklist .= <<<EOT
<div class="form-group">
    <div class="form-check">
        <input class="form-check-input" name="instructorOption" type="checkbox" value="{$foo->id}" id="id_{$foo->id}">
        <label class="form-check-label" for="id_{$foo->id}">{$name}</label>
    </div>
</div>
EOT;
        }
        else {
            foreach($courseInsts as $derp) {
                if ($derp['InstructorId'] == $foo->id) {
                    $instructorChecklist .= <<<EOT
<div class="form-group">
    <div class="form-check">
        <input class="form-check-input" name="instructorOption" type="checkbox" value="{$foo->id}" id="id_{$foo->id}" checked>
        <label class="form-check-label" for="id_{$foo->id}">{$name}</label>
    </div>
</div>
EOT;
                } else {
                    $instructorChecklist .= <<<EOT
<div class="form-group">
    <div class="form-check">
        <input class="form-check-input" name="instructorOption[]" type="checkbox" value="{$foo->id}" id="id_{$foo->id}">
        <label class="form-check-label" for="id_{$foo->id}">{$name}</label>
    </div>
</div>
EOT;
                }
            }
        }
    }

    $content = <<<EOT
    <div class="container-fluid">
<form action="/scripts/processAssignInstructorsForm.php" method="POST"> 
    <div class="form-row"> 
        <h2>Instructors</h2>    
    </div>
    <div class="form-row"> 
        <p>Select as many instructors to assign to this course as needed.</p>    
    </div>
    {$instructorChecklist}
    <div class="form-group">
        <input type="hidden" id="courseId" name="courseId" value="{$courseId}" /> 
        <button class="btn btn-primary btn-block" type="submit" name="assignInstructors" id="assignButton">Submit Assignments</button>
    </div>
</form>
</div>
EOT;
}
else {
    $content = <<<EOT
<div class="flex-column">
    <p>There are no instructors in any course in your program. Please add one using the button below.</p>
    <a href="/instructors/add.php?courseId={$courseId}" class="btn btn-primary btn-block">Add Instructor</a>
</div>
EOT;

}
//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Assign Instructors to Course";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();