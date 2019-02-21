<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/21/2019
 * Time: 12:13 PM
 */

require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}

$filter = filter_input( INPUT_GET, 'filter' );
if (!$filter) $filter='';
$db = new EduDB();
$sql = "SELECT InstitutionId, InstitutionName FROM institutions WHERE InstitutionName LIKE concat('%', :filter, '%')";
$params = [[":filter", $filter, PDO::PARAM_STR]];
$insts = $db->querySafe( $sql, $params );
$response = ['insts' => $insts];
echo json_encode( $response );