<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/28/2019
 * Time: 10:30 AM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$errors = array();
$db = new EduDB;
$sql = "SELECT u.UserId, FirstName, LastName, Username, Comments, u.CreateDate Created, InstitutionName Institution   
  FROM users u 
  LEFT JOIN institution_admins ia ON u.UserId = ia.UserId 
  LEFT JOIN institutions i ON ia.InstitutionId = i.InstitutionId 
  WHERE u.UserId != 1 AND u.Deleted = 0 
  GROUP BY u.UserId";
$Users = $db->query( $sql );

header('Content-Type: application/json; charset=utf-8');
echo json_encode( $Users );