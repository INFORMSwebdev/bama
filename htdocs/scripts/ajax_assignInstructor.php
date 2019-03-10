<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/9/2019
 * Time: 6:09 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

//get CourseId
$courseId = filter_input( INPUT_POST, 'CourseId', FILTER_VALIDATE_INT );

if (!$courseId) {
    $errors[] = "Missing required parameter: CourseId";
}
else {
    $course = new Course($course);
}