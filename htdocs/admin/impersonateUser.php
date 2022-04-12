<?php
require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}
$UserId = filter_input( INPUT_GET, 'UserId', FILTER_SANITIZE_NUMBER_INT);
if (!$UserId) die( "missing required parameter: UserId ");
$_SESSION['originalUserId'] = $_SESSION['loggedIn'];
$_SESSION['loggedIn'] = $UserId;
header("location: /index.php");