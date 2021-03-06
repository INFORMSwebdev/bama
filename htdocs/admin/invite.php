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

	<div class="flex-column">
		<h1>Analytics & OR Education Database ADMIN</h1>
    </div>
    <div class="flex-column">
		<h2>Invite User</h2>
    </div>
    <div class="container-fluid">
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
        <label for="email">Email Address/Username</label>
        <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" required>
        <small id="UserNameHelp" class="form-text text-muted">This is a separate login from an INFORMS account. Email address is the username.</small>
        <div class="valid-feedback">
            Looks good!
        </div>
        <div class="invalid-feedback">
			Please enter the editor's email address.
		</div>
	  </div>
	  <div class="form-row" id="instPickerContainer">
	     <div class="col-xs-6 form-group">
        <label for="inst">Institution</label>
        <select name="inst" id="inst" class="form-control"></select>
        </div>
        <div class="col-xs-6 form-group">
          <label for="instFilter">Filter</label>
          <div id="instFilterContainer">
            <input type="text" class="form-control"  id="instFilter" />
            <button id="clearFilter" title="clear filter">X</button>
          </div>
        </div>
	  </div>
	  <div class="form-row">
        <input type="submit" name="btn-sendInvite" id="btn-sendInvite" value="Send Invite"/>
	  </div>
    </form>
</div>
EOT;

$custom_css = <<<EOT
#instPickerContainer { display: grid; grid-template-columns: auto 250px; }
#instFilterContainer { position: relative; }
input#instFilter { padding-right: 25px; width: 100%; }
button#clearFilter { 
position:absolute; 
top:0; 
right: 0; 
width:25px; 
height: calc(2.25rem + 2px);
color: #f00; 
display: none;}

EOT;

$custom_js = <<<EOT
function fillInsts( filter ) {
  $('#inst').empty();
  $('#inst').append( $('<option>Loading...</option>' ));
  $('#inst').prop( "disabled", "disabled" );
  $.getJSON( "/scripts/ajax_getInstitutions.php", { 'filter': filter, 'crits': ['not-deleted','not-expired'] }, function( data ) {
    $('#inst').empty();
    $('#inst').append( $('<option value="0">(no selection)</option>' ));
    for( var i = 0; i < data.insts.length; i++ ) {
      var opt = $('<option value="'+data.insts[i].InstitutionId+'">'+data.insts[i].InstitutionName+'</option>');
      $('#inst').append( opt );
    }
    $('#inst').prop( "disabled", false );
  });
}
$(function() {
  fillInsts( null );
  $('#instFilter').on( 'click keyup', function (e) {
    if ($(this).val().length > 2 ) {
      fillInsts( $(this).val() );
      $('#clearFilter').show();
    }
  });
  $('#inviteForm').submit(function(e) {
    e.preventDefault();
    $.post( "/scripts/ajax_processInvite.php", $(this).serialize(), function(data) {
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
  $('#clearFilter').on( 'click keyup', function(e) {
    e.preventDefault();
    $('#instFilter').val( null );
    $('#clearFilter').hide();
    fillInsts( null );
  });
});
EOT;

$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Invite User";
$p_params['content'] = $content;
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();