<?php
require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}

$_SESSION['loggedIn'] = $_SESSION['originalUserId'];
$_SESSION['originalUserId'] = null;
header("location: /index.php");