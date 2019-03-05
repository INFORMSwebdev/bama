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
<div class="flex-column">
    <h1>Analytics & OR Education Database ADMIN</h1>
</div>
<div class="flex-column">
    <h2>Edit User</h2>
</div>
<div id="editorPane" class="container-fluid">
  <form id="userEditForm"> 
    <input type="hidden" name="UserId" value="{$UserId}"/>
      <div class="form-row">
          <label for="FirstName">First Name</label>
          <input type="text" class="form-control" name="FirstName" id="FirstName" placeholder="First Name" required value="{$FirstName}"/>
	   </div>
	   <div class="form-row">
          <label for="LastName">Last Name</label>
          <input type="text" class="form-control" name="LastName" id="LastName" placeholder="Last Name" required value="{$LastName}" />
	   </div>
	   <div class="form-row">
          <label for="Username">Username / Email Address</label>
          <input type="email" class="form-control" name="Username" id="Username" placeholder="Email Address" required value="{$Username}" />
	   </div>
	   <div class="form-row">
          <label for="Comments">Comments</label>
          <textarea class="form-control" name="Comments" id="Comments">{$Comments}</textarea>
	   </div>
	   <div>
          <label for="Created">Created</label>
          <div class="readonly" name="Created" id="Created">{$CreateDate}</div>
	   </div>
	   <div class="btn-toolbar">
          <input class="btn btn-primary btn-space" type="submit" value="Save" id="btn-submit" disabled />
          <input class="btn btn-secondary btn-space" type="button" value="Cancel" id="btn-cancel" disabled/>
	   </div>
  </form>
</div>
EOT;

$custom_css = <<<EOT
.readonly {
background-color: #f0f0f0;
padding: .375rem .75rem;
}
.btn-space { margin: 0 5px; }
.btn { width: 100px; }
EOT;

$custom_js = <<<EOT
$(function() {
    var saved_data = $('#userEditForm').serialize();
    $('#userEditForm').on( 'click keyup', function(e) {
      $('#btn-submit,#btn-cancel').attr( "disabled", ($(this).serialize() == saved_data));
    });
    $('#btn-cancel').on( 'click keyup', function(e) {
      $('#userEditForm')[0].reset();
    });
    $('#userEditForm').on( 'submit', function(e) {
      e.preventDefault();
       e.stopPropagation();
      if (this.checkValidity()) {
        $.post( "/scripts/ajax_adminEditUser.php", $(this).serialize(), function(data) {
          if (data.errors.length > 0) {
            var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
              for (var i = 0; i < data.errors.length; i++) {
                msg +=  data.errors[i] + "\\r\\n";
              }
              alert( msg );
              return false;
            }
            else if (data.success == 1) {
              alert( "The user record changes have been saved." );
            }
            else alert(data);
          }, "json");
      }
      else {
        
        return false;
      }
    });
});
EOT;

$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Edit User";
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();



