<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/13/2019
 * Time: 11:54 AM
 */

require_once( "../init.php");
$u = User::getUserByEmail('david.wirth@informs.org');
echo $u->id;

echo PHP_EOL . "done";