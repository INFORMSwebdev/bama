<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/1/2019
 * Time: 12:58 PM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$errors = array();
$statusFilter = '';
$statuses = filter_input( INPUT_GET, 'status', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
if (count($statuses)) $statusFilter = " WHERE pu.ApprovalStatusId IN (".implode(",", $statuses).")";

$db = new EduDB;
$sql = "SELECT UpdateId, Username, TableName Category, UpdateTypeDescription UpdateType, pu.CreateDate Created, StatusName Status   
  FROM pending_updates pu 
  LEFT JOIN users u on pu.UserId = u.UserId 
  LEFT JOIN table_lookup t ON pu.TableId = t.TableId 
  LEFT JOIN update_types ut ON pu.UpdateTypeId = ut.UpdateTypeId 
  LEFT JOIN approval_status app ON pu.ApprovalStatusId = app.ApprovalStatusId 
  $statusFilter 
  ORDER BY pu.CreateDate DESC";
$PendingUsers = $db->query( $sql );

header('Content-Type: application/json; charset=utf-8');
echo json_encode( $PendingUsers );