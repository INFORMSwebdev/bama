<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/5/2019
 * Time: 10:30 AM
 */

require_once("../init.php" );
$u = new Institution(9841);
//print_r( $u->getInstitutionAssignments());
print_r( $u->getContacts() );
echo "done";
