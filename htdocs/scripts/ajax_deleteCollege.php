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

if (!$CollegeId) $errors[] = "Missing required parameter: CollegeId";
else {
    $college = new College( $CollegeId );
    if (!$college->valid) $errors[] = "The ProgramId provided does not correspond to an existing program.";
    else {
        //get the userId
        if(isset($_SESSION['loggedIn'])){
            $user = new User($_SESSION['loggedIn']);
        }
        else {
            //this should never happen, but just in case:
            $user = new User(1);
        }
        $result = $college->createPendingUpdate(UPDATE_TYPE_DELETE, $user->id);
        if ($result){
            $msg = "College '{$college->Attributes['CollegeName']}' submitted for deletion.";
        }
        else {
            $errors[] = "College '{$college->Attributes['CollegeName']}' could not be deleted, alert IT dept.";
        }
    }
}

$response['msg'] = $msg;
$response['errors'] = $errors;
echo json_encode( $response );