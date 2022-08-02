<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/21/2019
 * Time: 12:13 PM
 */

require_once( "../../init.php");
//if (!isset($_SESSION['admin']) && !isset($_SESSION['loggedIn'])) die( "unauthorized access" );

$ok_crits = [
    'deleted' => 'Deleted = 1',
    'expired' => 'Expired = 1',
    'not-deleted' => 'Deleted = 0',
    'not-expired' => 'Expired = 0',
];

$filter = filter_input( INPUT_GET, 'filter' );
$crits = filter_input( INPUT_GET, 'crits', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

$addl_params = '';
if ($crits && count($crits)) {
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
array_walk_recursive($response, function (&$entry) { $entry = mb_convert_encoding( $entry, 'UTF-8' ); });
header( "Content-Type: application/json" );
echo json_encode( $response );
