<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/26/2019
 * Time: 5:23 PM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$errors = array();
$db = new EduDB;
$sql = "SELECT * FROM pending_users";
$PendingUsers = $db->query( $sql );

header('Content-Type: application/json; charset=utf-8');
echo json_encode( $PendingUsers );