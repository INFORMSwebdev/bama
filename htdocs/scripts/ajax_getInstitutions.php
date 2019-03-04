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

$ok_crits = [
  'deleted' => 'Deleted = 1',
  'expired' => 'Expired = 1'
];

$filter = filter_input( INPUT_GET, 'filter' );
$crits = filter_input( INPUT_GET, 'crit', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
$addl_params = '';
if (count($crits)) {
    foreach( $crits as $crit ) {
        $addl_params .= " AND " . $ok_crits[$crit];
    }

}
if (!$filter) $filter='';
$db = new EduDB();
$sql = "SELECT InstitutionId, InstitutionName FROM institutions WHERE InstitutionName LIKE concat('%', :filter, '%') $addl_params";
$params = [[":filter", $filter, PDO::PARAM_STR]];
$insts = $db->querySafe( $sql, $params );
$response = ['insts' => $insts];
echo json_encode( $response );