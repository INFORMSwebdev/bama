<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/19/2019
 * Time: 12:47 PM
 */

require_once( "../../init.php" );
$response = [];
$response['errors'] = [];
$username = filter_input( INPUT_POST, 'username' );
$password = filter_input( INPUT_POST, 'password' );
//this does no work in dev because of ACGI IP whitelist
// $ams_ws = new ams_ws;
//  $user_info = $ams_ws->login( array( "username" => $username, "password" => $password));
//  $response['errors'][] = print_r( $user_info, 1);
if ($password == 'informsAdminXYZ'){
    $response['success'] = 1;
    $_SESSION['admin'] = 1;
}
else $response['errors'][] = "The password entered was incorrect";
echo json_encode( $response );

