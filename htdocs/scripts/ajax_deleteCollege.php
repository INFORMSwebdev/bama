<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/7/2019
 * Time: 12:18 PM
 */
require_once( "../../init.php" );

$response = [];
$errors = [];
$msg = '';

$CollegeId = filter_input( INPUT_POST, 'CollegeId', FILTER_VALIDATE_INT );

if(!isset($_SESSION['loggedIn']) || !is_numeric($_SESSION['loggedIn'])){
    $errors[] = 'You must be logged in to delete colleges.';
}
else if (!$CollegeId) {
    $errors[] = 'Missing required parameter: CollegeId.';
}
else {
    $college = new College( $CollegeId );
    if (!$college->valid) {
        $errors[] = 'The ProgramId provided does not correspond to an existing program.';
    }
    else {
        //get the userId
        $user = new User($_SESSION['loggedIn']);

        if($user->id == 1){
            $college->Attributes['Deleted'] = 1;
            $result = $college->save();
            if ($result) {
                $msg = "College '{$college->Attributes['CollegeName']}' successfully marked as deleted.";
            }
            else {
                $errors[] = "College '{$college->Attributes['CollegeName']}' could not be deleted, alert IT dept.";
            }
        }
        else {
            $result = $college->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
            if ($result) {
                $msg = "College '{$college->Attributes['CollegeName']}' submitted for deletion.";
            }
            else {
                $errors[] = "College '{$college->Attributes['CollegeName']}' could not be deleted, alert IT dept.";
            }
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );