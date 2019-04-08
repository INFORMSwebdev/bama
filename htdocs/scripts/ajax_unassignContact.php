<?php
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$ContactId = filter_input(INPUT_POST, 'ContactId', FILTER_VALIDATE_INT);
$ProgramId = filter_input(INPUT_POST, 'ProgramId', FILTER_VALIDATE_INT);

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to un-assign contacts from programs.';
}
else if (!$ContactId) {
    $errors[] = 'Missing required parameter: ContactId.';
}
else if(!$ProgramId){
    $errors[] = 'Missing required parameter: ProgramId.';
}
else {
    $user = new User($_SESSION['loggedIn']);
    $prog = new Program($ProgramId);
    $con = new Contact($ContactId);

    $result = $prog->unassignContact($con->id);
    if($result){
        $msg = "Contact '{$con->Attributes['ContactName']}' successfully unassigned from {$prog->Attributes['ProgramName']}.";
    }
    else {
        $errors[] = "Contact could not be unassigned from program, alert IT dept.";
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );