<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/19/2019
 * Time: 10:51 AM
 */

require_once( "../../init.php");

$redirect = (isset($_SESSION['admin_login_redirect'])) ? $_SESSION['admin_login_redirect'] : '/admin/index.php';

$errors = [];
$content = <<<EOT
<div class="flex-column">
    <h1>INFORMS Admin Login</h1>
</div>
<div class="flex-column">
    <p>Log in with your AA front end (self-service) credentials.</p>
</div>
<div class="container-fluid">
    <form class="needs-validation" method="post" novalidate id="admin_login_form">
        <div class="form-row">
            <label for="validationUsername">Username</label>
            <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
            <div class="valid-feedback">
                Looks good!
            </div>
            <div class="invalid-feedback">
                Please enter your username.
            </div>
        </div>
        <br />
        <div class="form-row">
            <label for="validationPassword">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
            <div class="valid-feedback">
                Looks good!
            </div>
             <div class="invalid-feedback">
                Please enter your password.
            </div>
        </div>
        <br />
        <button class="btn btn-primary" type="submit">Log in</button>
    </form>
</div>
EOT;

$custom_js = <<<EOT
$(function() {
  $('#admin_login_form').submit(function(e) {
    e.preventDefault();
    $.post( "/scripts/ajax_processAdminLogin.php", { username: $('#username').val(), password: $('#password').val()}, function( data ) {
      if (data.errors.length > 0) { 
        var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
        for (var i = 0; i < data.errors.length; i++) {
          msg +=  data.errors[i] + "\\r\\n";
        }
        alert( msg );
      }
      else if (data.success == 1) {
        window.location.href = "$redirect";
      }
    }, "json");
  });
});
EOT;

$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Login";
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();
