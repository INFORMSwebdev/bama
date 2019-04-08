<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/7/2019
 * Time: 12:14 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$ProgId = filter_input( INPUT_POST, 'ProgramId', FILTER_VALIDATE_INT );

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to delete instructors.';
}
else if (!$ProgId) {
    $errors[] = 'Missing required parameter: ProgramId.';
}
else {
    $prog = new Program( $ProgId );
    if (!$prog->valid) {
        $errors[] = 'The ProgramId provided does not correspond to an existing program.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);

        if($user->id == 1){
            $prog->Attributes['Deleted'] = 1;
            $result = $prog->save();

            if ($result) {
                $msg = "Program '{$prog->Attributes['ProgramName']}' successfully marked as deleted.";
            }
            else {
                $errors[] = "Program '{$prog->Attributes['ProgramName']}' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $prog->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
            if ($result) {
                $msg = "Program '{$prog->Attributes['ProgramName']}' submitted for deletion.";
            }
            else {
                $errors[] = "Program '{$prog->Attributes['ProgramName']}' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );