<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/25/2019
 * Time: 10:06 AM
 */

require_once '../../init.php';

$Token = filter_input( INPUT_GET, 'token');
if (!$Token) die( "missing required parameter: token");

$User = User::getUserByToken( $Token );
$_SESSION["password_set_for"] = $User->id;

$content = <<<EOT
<div class="flex-column">
	<h1>Set New Password</h1>
</div>
<div class="flex-column">
	<p>Password rules:</p>
	<ul>
	  <li>Minimum length: 8 characters.</li>
	  <li>Can contain any upper- or lower-case letter, number, or any special characters.</li>
	  <li>Your password does not have to include a mix of upper- and lower-case letters, numbers, and special characters, but it is highly recommended that you do so.</li>
	</ul>
</div>
<div class="container-fluid">
    <form id="setPasswordForm" method="POST">
      <div class="form-group">
        <label for="password">Password</label>
    		<input minlength="8" type="password" class="form-control" id="password" name="password" required />
      </div>
      <div class="form-group">
        <label for="password_confirm">Retype Password to Confirm</label>
    		<input type="password" class="form-control" id="password_confirm" name="password_confirm" required />
      </div>
      <div class="form-group">
        <input type="submit" value="Set Password" />
      </div>
    </form>
</div>
EOT;

$custom_js = <<<EOT
$(function() {
  $('#setPasswordForm').on( 'input', function(e) {
    var pass = $('#password');
    var pass_confirm = $('#password_confirm');
    if (pass.val() != pass_confirm.val()) pass_confirm[0].setCustomValidity("That did not match the password.");
    else pass_confirm[0].setCustomValidity("");
  });
  $('#setPasswordForm').on( 'submit', function(e) {
    var password = $('#password'), password_confirm = $('#password_confirm');
    if (this.checkValidity()) {
      $.post( "/scripts/ajax_setPassword.php", { 'password': password.val(), 'password_confirm': password_confirm.val() }, function(data) {
        if (data.errors.length > 0) {
          var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
          for (var i = 0; i < data.errors.length; i++) {
            msg +=  data.errors[i] + "\\r\\n";
          }
          alert( msg );
          e.preventDefault();
          e.stopPropagation();
          return false;
        }
        else if (data.result == 1) {
          alert( "Your password has been successfully set." );
          window.location.href="/users/login.php";
        }
        else alert(data);
      }, "json");
      return false;
    }
    else {
      e.preventDefault();
      e.stopPropagation();
      return false;
    }
  });
});
EOT;


$p_params = [];
$p_params['admin'] = TRUE;
$p_params['content'] = $content;
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();