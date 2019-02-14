<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/13/2019
 * Time: 11:54 AM
 */

require_once( "../init.php");
$inst = new Institution(1);
print_r($inst);
$insts = Institution::getInstitutions(1,0);
print_r($insts);