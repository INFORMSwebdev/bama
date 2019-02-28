<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/20/2019
 * Time: 2:14 PM
 */

require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}
$errors = [];
$content = <<<EOT
<div class="container">
	<div class="row">
		<h1>Analytics & OR Education Database ADMIN</h1>
    </div>
</div>

EOT;

$custom_js = <<<EOT
$(function() {
  
});
EOT;


$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN";
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();