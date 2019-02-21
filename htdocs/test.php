<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/5/2019
 * Time: 10:30 AM
 */

require_once("/common/classes/pdo_db_single.php");
require_once( "../classes/AOREducationObject.php");
require_once ("../classes/EduDB.php");
require_once( "../classes/User.php");
$u = new User(41);
//print_r( $u->getInstitutionAssignments());
print_r( $u->wtf());
echo "done";
