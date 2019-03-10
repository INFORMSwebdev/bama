<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/9/2019
 * Time: 6:44 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$programId = filter_input(INPUT_GET, 'ProgramId', FILTER_VALIDATE_INT);
$courseId = filter_input(INPUT_GET, 'CourseId', FILTER_VALIDATE_INT);

if (!$programId) {
    $errors[] = "Missing required parameter: ProgramId";
}
else {
    $prog = new Program($programId);

    $result = $prog->getInstructors(TRUE, TRUE);

    $instructors = [];
    if ($result){

        if($courseId){
            $course = new Course($courseId);
            $courseInstructors = $course->getInstructors();
        }

        foreach($result as $inst){
            $instHelp = [];
            if($courseInstructors){
                //if(in_array())
            }
            else {

            }
            $instHelp['InstructorId'] = $inst->id;
            $instHelp['InstructorFirstName'] = $inst->Attributes['InstructorFirstName'];
            $instHelp['InstructorLastName'] = $inst->Attributes['InstructorLastName'];
            $instructors[] = $instHelp;
        }
        $response['instructors'] = $instructors;
    }
    else {
        $errors[] = "No instructors available for the $prog->Attributes['ProgramName'] program.";
    }
}

//set up response
$response['msg'] = $msg;
$response['errors'] = $errors;
//send response
echo json_encode($response);