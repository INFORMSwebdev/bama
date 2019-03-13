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
$sql = "SELECT PendingUserId, FirstName, LastName, Username, Comments, pu.CreateDate Created, InstitutionName Institution   
  FROM pending_users pu 
  LEFT JOIN institutions i ON pu.InstitutionId = i.InstitutionId 
  WHERE pu.ApprovalStatusId < 2";
$PendingUsers = $db->query( $sql );

header('Content-Type: application/json; charset=utf-8');
echo json_encode( $PendingUsers );