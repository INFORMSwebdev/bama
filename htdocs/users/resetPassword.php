<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/21/2019
 * Time: 12:54 PM
 */
require_once '../../init.php';

$content = <<<EOT
<div class="row">
	<h1>Password Reset</h1>
</div>
<div class="row">
	<p>Use the form below to request a password reset. An email will be sent to you with a link that 
	will allow you to set a new password. If you do not receive the password reset email within a few minutes, check 
	your spam/junk folder.</p>
</div>
<form id="passwordResetForm">
  <div class="form-group">
    <label for="email">Email Address</label>
		<input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required />
		<div class="valid-feedback">
			Looks good!
		</div>
		<div class="email invalid-feedback">
			Please enter your email address.
		</div>
  </div>
  <div class="form-group">
    <input type="submit" value="Reset Password" />
  </div>
</form>
EOT;

$custom_js = <<<EOT
$(function() {
  $('#passwordResetForm').submit( function(e) {
    if (!$('#email').val()) return false;
    e.preventDefault();
    $.post( "/scripts/ajax_passwordReset.php", { 'email': $('#email').val() }, function( data ) {
      if (data.errors.length > 0 ) {
        var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
        for (var i = 0; i < data.errors.length; i++) {
          msg +=  data.errors[i] + "\\r\\n";
        }
        alert( msg );
      }
      else if (data.msg) alert( data.msg );
      else alert( "Something went wrong." );
    }, "json");
  });
});
EOT;


$p_params = [];
$p_params['admin'] = TRUE;
$p_params['content'] = $content;
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();