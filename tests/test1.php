<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/13/2019
 * Time: 11:54 AM
 */

require_once( "../init.php");
$u = new User(41);
print_r( $u->getInstitutionAssignments(1));

echo "done";