<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/28/2019
 * Time: 10:42 AM
 */

require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}
$errors = [];
$UserId = filter_input( INPUT_GET, 'UserId', FILTER_SANITIZE_NUMBER_INT);
if (!$UserId) die( "missing required parameter: UserId ");

$User = new User( $UserId );
foreach( $User->Attributes as $key => $value ) {
    $$key = filter_var( $value, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
}

$content = <<<EOT
<div class="row">
    <h1>Analytics & OR Education Database ADMIN</h1>
</div>
<div class="row">
    <h2>Edit User</h2>
</div>
<div id="editorPane">
  <form id="userEditForm"> 
      <div class="form-row">
          <label for="firstName">First Name</label>
          <input type="text" class="form-control" name="firstName" id="firstName" placeholder="First Name" required value="{$FirstName}"/>
	   </div>
	   <div class="form-row">
          <label for="lastName">Last Name</label>
          <input type="text" class="form-control" name="lastName" id="firstName" placeholder="First Name" required value="{$LastName}" />
	   </div>
  </form>
</div>
EOT;

$custom_css = <<<EOT

EOT;

$custom_js = <<<EOT

EOT;

$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Edit User";
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();



