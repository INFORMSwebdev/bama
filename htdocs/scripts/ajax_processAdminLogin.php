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
$username = trim( filter_input( INPUT_POST, 'username' ) );
$password = filter_input( INPUT_POST, 'password' );
$is_admin = FALSE;
if (isset($aes['sso_enabled']) && $aes['sso_enabled']==1) {
    $ams_ws = new ams_ws;
    $user_info = $ams_ws->login( array( "username" => $username, "password" => $password));
    if ($user_info) {
        $cust_id = $user_info['custid'];
        if (in_array($cust_id, $aes['admin_users'])) {
            $is_admin = TRUE;
        }
        else {
            $response['errors'][] = "Login successful but you are not included in the list of authorized admins.";
        }
    }
    else {
        $response['errors'][] = "Login failed due to invalid username and/or password.";
    }
}
elseif ($password == $aes['non_sso_admin_password']) { // sso_enabled is set to 0
        $is_admin = TRUE;
}
else $response['errors'][] = "The password entered was incorrect";

if ($is_admin) {
    $response['success'] = 1;
    $_SESSION['admin'] = 1;
    setcookie("aes_admin", 1, time()+(60*60*24*180), "/");
}

echo json_encode( $response );

