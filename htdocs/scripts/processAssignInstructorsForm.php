<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/9/2019
 * Time: 9:57 PM
 */
//require the init file
require_once '../../init.php';

$checked = $_POST['instructorOption'];
$courseId = filter_input(INPUT_POST, 'courseId', FILTER_VALIDATE_INT);

if(!$courseId){
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'Invalid CourseId, could not assign instructors.';
    //redirect user to index?
    header('Location: /index.php');
    die;
}

$course = new Course($courseId);

if($checked){
    //go through the list and assign each selected instructor
    foreach($checked as $check){
        $course->assignInstructor($check);
    }

    $_SESSION['editMessage']['success'] = true;
    $_SESSION['editMessage']['text'] = 'All selected instructors have been assigned to the course.';
}
else {
    //unassign all the instructors from the course
    $course->unassignAllInstructors();
    $_SESSION['editMessage']['success'] = true;
    $_SESSION['editMessage']['text'] = 'All instructors have been un-assigned from this course.';
}

//redirect user to index?
header("Location: /courses/display.php?id=$courseId");
die;