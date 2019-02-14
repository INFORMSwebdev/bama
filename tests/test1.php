<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/13/2019
 * Time: 11:54 AM
 */

require_once( "../init.php");
$u = new User(1);
echo $u->assignToInstitution(1);