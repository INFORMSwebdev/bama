<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/4/2019
 * Time: 10:35 AM
 */

require_once( "../init.php");

echo date('m/d/Y h:i:s a', time()) . " : Beginning expiration check." . PHP_EOL;

$exp = $aes['data_expiration'];

$db = new EduDB();
$sql = "SELECT InstitutionId FROM institutions WHERE DATE(LastModifiedDate) = DATE(DATE_ADD(NOW(), INTERVAL -$exp DAY));";
$insts = $db->queryColumn( $sql );
foreach($insts as $inst) {
    $Institution = new Institution( $inst );
    $Institution->update( 'Deleted', 1 );
    echo "deleting InstitutionId = $inst" . PHP_EOL;
}
echo date('m/d/Y h:i:s a', time()) . " : Expiration check complete.". PHP_EOL;