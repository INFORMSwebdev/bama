<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/1/2019
 * Time: 3:15 PM
 */

require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}

$UpdateId = filter_input( INPUT_GET, 'UpdateId', FILTER_SANITIZE_NUMBER_INT );
if (!$UpdateId) die( "Missing required parameter: UpdateId");

$Update = new PendingUpdate( $UpdateId );
$UpdateTypes = [
    UPDATE_TYPE_INSERT => "Add",
    UPDATE_TYPE_UPDATE => "Update",
    UPDATE_TYPE_DELETE => "Delete"
];

$UpdateType = $UpdateTypes[$Update->Attributes['UpdateTypeId']];

$Table = new Table( $Update->Attributes['TableId'] );
$Class = $Table->Attributes['ClassName'];

$data = unserialize( $Update->Attributes['UpdateContent'] );
$data_html = '';
// TODO:  we really should make the column names have friendly descriptions, and columns holding foreign keys should  have their values translated into friendly values
foreach( $data as $key => $value ) {
    if (!$value) $value = '&nbsp;';
    $data_html .= '<div class="row data_row">';
    $data_html .= '<div class="data_label">' . $Class::$data_structure[$key]['label'] . '</div>';
    $data_html .= '<div class="data_value">' . $value . '</div>';
    $data_html .= '</div>';
}
$thing = $Class::createInstance( $data );
if ($UpdateType == 'Update' || $UpdateType == 'Delete') {
    $thing->id = $thing->Attributes[$Class::$primary_key];
}

$ancestry_html = $thing->getAncestry();

$content = <<<EOT
<div class="flex-column">
  <h1>Review Pending Update</h1>
</div>
<div class="container-fluid">
    <p>Update Type: $UpdateType</p>
    $ancestry_html
    <h3>$Class</h3>
    
    $data_html
    <div class="row btn-toolbar">
        <button class="btn btn-primary btn-Approve" id="btn-Approve">Approve</button>
        <button class="btn btn-secondary btn-Reject" id="btn-Reject">Reject</button>
    </div>
</div>
EOT;

$custom_css = <<<EOT
.btn-toolbar { margin-top: 20px; }
.btn {width: 125px; margin: 0 10px; }
.data_row { margin: 8px 0; }
.data_label, .data_value { display: block; width: 100%; }
.data_label { font-weight: bold; }
.data_value { padding: 0 10px; }
EOT;

$approve = APPROVAL_TYPE_APPROVE;
$reject = APPROVAL_TYPE_REJECT;
$custom_js = <<<EOT
$(function() {
  $('#btn-Approve, #btn-Reject').on( 'click keyup', function(e) {
    var action = ($(this).hasClass('btn-Approve')) ? $approve : $reject;
    $.post( '/scripts/ajax_ApprovalAction.php', { 'action': action, 'UpdateId': $UpdateId }, function(data) {
      if (data.errors.length > 0 ) {
        var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
        for (var i = 0; i < data.errors.length; i++) {
          msg +=  data.errors[i] + "\\r\\n";
        }
        alert( msg );
      }
      else if (data.msg) {
        alert( data.msg );
        window.location.href="/admin/pendingUpdates.php";
      }
      else alert( "Something went wrong." );
    }, "json");
  });
});
EOT;

$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Review Pending Update";
$p_params['content'] = $content;
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();