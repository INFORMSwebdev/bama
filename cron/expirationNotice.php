<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/4/2019
 * Time: 10:35 AM
 */

require_once( "../init.php");

$exp = $aes['data_expiration'];
$notice_window = $aes['notice_days'];
$days = $exp + $notice_window;
$target_date = new DateTime('now');
$target_date->modify( "-$days day");
echo date('m/d/Y h:i:s a', time()) . " : Beginning expiration notice for last modified = ".$target_date->format('Y-m-d' ) . PHP_EOL;

$db = new EduDB();
$sql = "SELECT InstitutionId FROM institutions WHERE DATE(LastModifiedDate) = DATE(DATE_ADD(NOW(), INTERVAL -$days DAY));";
$insts = $db->queryColumn( $sql );
foreach($insts as $inst) {
    $Institution = new Institution( $inst );
    $Institution->sendExpirationNotice();
    echo "sending notice to contact(s) for InstitutionId = $inst" . PHP_EOL;
}
echo date('m/d/Y h:i:s a', time()) . " : Expiration notice check complete.". PHP_EOL;