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

$content = <<<EOT
<div class="row">
    <h1>Analytics & OR Education Database ADMIN</h1>
</div>
<div class="row">
    <h2>Add Institution</h2>
</div>
<div class="row">
  <p>Before adding an institution, you might want to check to make sure the 
  institution is not already in our database but is not being publicly displayed 
  because it has been marked "expired" or "deleted."</p>
  <p>Expired Institutions: <select id="expiredInsts"></select></p>
  <p>Deleted Institutions: <select id="deletedInsts"></select></p>
</div>
EOT;

$custom_css = <<<EOT

EOT;

$custom_js = <<<EOT
function fillInsts( selectID, filter, crit ) {
  var select = $('#'+selectID);
  select.empty();
  select.append( $('<option>Loading...</option>' ));
  select.prop( "disabled", "disabled" );
  $.getJSON( "/scripts/ajax_getInstitutions.php", { 'filter': filter, 'crit': crit }, function( data ) {
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
  fillInsts( 'expiredInsts', null, ['deleted'] );
});
EOT;


$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Add Institution";
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();