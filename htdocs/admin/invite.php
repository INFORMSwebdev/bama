<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/21/2019
 * Time: 10:16 AM
 */

require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}

$content = <<<EOT
<div class="container">
	<div class="row">
		<h1>Analytics & OR Education Database ADMIN</h1>
    </div>
    <div class="row">
		<h2>Invite Institution Editor</h2>
    </div>
    <form id="inviteForm">
      <div class="form-row">
        <label for="firstName">First Name</label>
        <input type="text" class="form-control" name="firstName" id="firstName" placeholder="First Name" required>
        <div class="valid-feedback">
            Looks good!
        </div>
        <div class="invalid-feedback">
			Please enter the editor's first name.
		</div>
	  </div>
	  <div class="form-row">
        <label for="lastName">Last Name</label>
        <input type="text" class="form-control" name="lastName" id="lastName" placeholder="Last Name" required>
        <div class="valid-feedback">
            Looks good!
        </div>
        <div class="invalid-feedback">
			Please enter the editor's last name.
		</div>
	  </div>
    </form>
</div>
EOT;


$custom_js = <<<EOT
$(function() {
  
});
EOT;

$p_params = [];
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();