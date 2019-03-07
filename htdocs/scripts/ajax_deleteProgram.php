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

if (!$ProgId) $errors[] = "Missing required parameter: ProgramId";
else {
    $prog = new Program( $ProgId );
    if (!$prog->valid) $errors[] = "The ProgramId provided does not correspond to an existing program.";
    else {
        //get the userId
        if(isset($_SESSION['loggedIn'])){
            $user = new User($_SESSION['loggedIn']);
        }
        else {
            //this should never happen, but just in case:
            $user = new User(1);
        }
        $result = $prog->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
        if ($result){
            $msg = "Program '{$prog->Attributes['ProgramName']}' submitted for deletion.";
        }
        else {
            $errors[] = "Program '{$prog->Attributes['ProgramName']}' could not be deleted, alert IT dept.";
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );