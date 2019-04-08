<?php
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$CollegeId = filter_input(INPUT_POST, 'CollegeId', FILTER_VALIDATE_INT);
$ProgramId = filter_input(INPUT_POST, 'ProgramId', FILTER_VALIDATE_INT);

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to un-assign colleges from programs.';
}
else if (!$CollegeId) {
    $errors[] = 'Missing required parameter: CollegeId.';
}
else if(!$ProgramId){
    $errors[] = 'Missing required parameter: ProgramId.';
}
else {
    $user = new User($_SESSION('loggedIn'));
    $prog = new Program($ProgramId);
    $col = new College($CollegeId);

    $prog->Attributes['CollegeId'] = null;

    if($user->id == 1){
        $result = $prog->save();

        if($result){
            $msg = "College {$col->Attributes['CollegeName']} successfully un-assigned from {$prog->Attributes['ProgramName']}.";
        }
        else {
            $errors[] = "College could not be unassigned from program, alert IT dept.";
        }
    }
    else {
        $result = $prog->createPendingUpdate(UPDATE_TYPE_UPDATE, $user->id);

        if ($result) {
            $msg = "College {$col->Attributes['CollegeName']} submitted for un-assignment from {$prog->Attributes['ProgramName']} and awaiting approval by an INFORMS administrator.";
        }
        else {
            $errors[] = "College could not be unassigned from program, alert IT dept.";
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );