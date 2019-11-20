<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/4/2019
 * Time: 10:59 AM
 */

require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}
$errors = [];

$state_options = '';
$db = new EduDB();
$sql = "SELECT * FROM states ORDER BY name";
$states = $db->query( $sql );
foreach( $states as $state ) {
    $state_options .= '<option value="'.$state['abbr'].'">'. $state['name'] .'</option>';
}

$region_options = '';
$sql = "SELECT id, name FROM region_dropdown";
$regions = $db->query( $sql );
foreach( $regions as $region ) {
    $region_options .= '<option value="'.$region['id'].'">'. $region['name'] .'</option>';
}

$content = <<<EOT
<div class="flex-column">
    <h1>Analytics & OR Education Database ADMIN</h1>
</div>
<div class="flex-column">
  <p>Before adding an institution, you might want to check to make sure the 
  institution is not already in our database but is not being publicly displayed 
  because it has been marked "expired" or "deleted."</p>
  <p>Expired Institutions: <select id="expiredInsts" disabled></select>
  <button id="btn-unexpire" class="btn btn-primary btn-sm" disabled>Unexpire</button></p>
  <p>Deleted Institutions: <select id="deletedInsts" disabled></select>
  <button id="btn-undelete" class="btn btn-primary btn-sm" disabled>Undelete</button></p>
</div>
<div class="flex-column">
    <h2>Add Institution</h2>
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="flex-column">
  <form id="form-addInstitution">
    <div class="form-group">
      <label for="InstitutionName">Institution Name</label><span class="text text-danger">*</span>
      <input type="text" class="form-control" id="InstitutionName" name="InstitutionName" placeholder="Institution Name" required />
    </div>
    <div class="form-group">
      <label for="InstitutionAddress">Street Address</label>
      <input type="text" class="form-control" id="InstitutionAddress" name="InstitutionAddress" placeholder="Street Address"/>
    </div>
    <div class="form-group">
      <label for="InstitutionCity">City</label>
      <input type="text" class="form-control" id="InstitutionCity" name="InstitutionCity" placeholder="City"/>
    </div>
    <div class="form-group">
      <label for="InstitutionState">State</label>
      <select class="form-control" id="InstitutionState" name="InstitutionState">$state_options</select>
    </div>
    <div class="form-group">
      <label for="InstitutionZip">Postal Code</label>
      <input type="text" class="form-control" id="InstitutionZip" name="InstitutionZip" placeholder="Institution Zip"/>
    </div>
    <div class="form-group">
      <label for="RegionId">Region</label>
      <select class="form-control" id="RegionId" name="RegionId" placeholder="Region">
        $region_options
      </select>
    </div>
    <div class="form-group">
      <label for="InstitutionName">Institution Contact Phone</label>
      <input type="text" class="form-control" id="InstitutionPhone" name="InstitutionPhone" placeholder="Institution Contact Phone"/>
    </div>
    <div class="form-group">
      <label for="InstitutionEmail">Institution Contact Email</label>
      <input type="email" class="form-control" id="InstitutionEmail" name="InstitutionEmail" placeholder="Institution Contact Email"/>
    </div>
    <div class="form-group">
      <label for="InstitutionAccess">Institution Website</label>
      <input type="text" class="form-control" id="InstitutionAccess" name="InstitutionAccess" placeholder="Institution Website"/>
    </div>
    <div class="form-group">
      <button class="btn btn-primary" id="btn-submitAddForm">Add Institution</button>
    </div>
  </form>
</div>
EOT;

$custom_css = <<<EOT

EOT;

$custom_js = <<<EOT
function ajaxResponseHandler( data ) {
  if (data.errors.length > 0 ) {
    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
    for (var i = 0; i < data.errors.length; i++) {
      msg +=  data.errors[i] + "\\r\\n";
    }
    alert( msg );
  }
  else if (data.msg) {
    alert( data.msg );
  }
  else alert( "Something went wrong." );
}
function fillInsts( selectID, filter, crits ) {
  var select = $('#'+selectID);
  select.empty();
  select.append( $('<option>Loading...</option>' ));
  select.prop( "disabled", "disabled" );
  $.getJSON( "/scripts/ajax_getInstitutions.php", { 'filter': filter, 'crits': crits }, function( data ) {
    select.empty();
    select.append( $('<option valueg="0">(no selection)</option>' ));
    for( var i = 0; i < data.insts.length; i++ ) {
      var opt = $('<option value="'+data.insts[i].InstitutionId+'">'+data.insts[i].InstitutionName+'</option>');
      select.append( opt );
    }
    select.prop( "disabled", false );
  });
}
$(function() {
  fillInsts( 'expiredInsts', null, ['expired'] );
  fillInsts( 'deletedInsts', null, ['deleted'] );
  $('#expiredInsts').on( 'change', function(e) {
    $('#btn-unexpire').attr( 'disabled', this.selectedIndex == 0 );
  });
  $('#deletedInsts').on( 'change', function(e) {
    $('#btn-undelete').attr( 'disabled', this.selectedIndex == 0 );
  });
  $('#btn-unexpire').on( 'click keyup', function(e) {
    var id = $('#expiredInsts').val();
    $.post( '/scripts/ajax_expire.php', { 'InstitutionId': id, 'Value': 0 }, function(data) {
      ajaxResponseHandler( data );
      if (data.msg) fillInsts( 'expiredInsts', null, ['expired'] );
    }, "json");
  });
  $('#btn-undelete').on( 'click keyup', function(e) {
    var id = $('#deletedInsts').val();
    $.post( '/scripts/ajax_delete.php', { 'InstitutionId': id, 'Value': 0 }, function(data) {
      ajaxResponseHandler( data );
      if (data.msg) fillInsts( 'deletedInsts', null, ['deleted'] );
    }, "json");
  });
  $('#form-addInstitution').submit( function(e) {
    e.preventDefault();
    
    $.post( "/scripts/ajax_addInstitution.php", $(this).serialize(), function(data) {
      ajaxResponseHandler( data );
    }, "json");
  });
  $('#InstitutionPhone').inputmask('999-999-9999');
});
EOT;


$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Add Institution";
$p_params['content'] = $content;
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'text' => $custom_js );
$p_params['js'][] = array('url' => 'https://rawgit.com/RobinHerbots/Inputmask/5.x/dist/jquery.inputmask.js' );
$wrapper = new wrapperBama($p_params);
$wrapper->html();