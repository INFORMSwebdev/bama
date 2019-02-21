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
	  <div class="form-row">
        <label for="email">Email Address</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" required>
        <div class="valid-feedback">
            Looks good!
        </div>
        <div class="invalid-feedback">
			Please enter the editor's email address.
		</div>
	  </div>
	  <div class="form-row">
	     <div class="col-xs-6 form-group">
        <label for="inst">Institution</label>
        <select name="inst" id="inst" class="form-control"></select>
        </div>
        	     <div class="col-xs-6 form-group">

        <label for="instFilter">Filter</label>
        <input type="text" class="form-control"  id="instFilter" />
        </div>
	  </div>
	  <div class="form-row">
        <input type="submit" class="form-control" name="btn-sendInvite" id="btn-sendInvite" value="Send Invite"/>
	  </div>
    </form>
</div>
EOT;


$custom_js = <<<EOT
function fillInsts( filter ) {
  $('#inst').empty();
  $.getJSON( "/scripts/ajax_getInstitutions.php", { 'filter': filter }, function( data ) {
    for( var i = 0; i < data.insts.length; i++ ) {
      var opt = $('<option value="'+data.insts[i].InstitutionId+'">'+data.insts[i].InstitutionName+'</option>');
      $('#inst').append( opt );
    }
  });
}
$(function() {
  fillInsts( null );
  $('#instFilter').on( 'click keyup', function (e) {
    if ($(this).val().length > 3 ) fillInsts( $(this).val() );
  });
  $('#inviteForm').submit(function(e) {
    e.preventDefault();
    alert("this doesn't go anywhere yet");
  });
});
EOT;

$p_params = [];
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();