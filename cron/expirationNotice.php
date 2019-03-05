<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/4/2019
 * Time: 10:35 AM
 */

require_once( "../init.php");

$db = new EduDB();
$sql = "SELECT InstitutionId FROM institutions WHERE LastModifiedDate = DATE_ADD(NOW(), INTERVAL -10 DAY);";
$rows = $db->query( $sql );
print_r($rows);