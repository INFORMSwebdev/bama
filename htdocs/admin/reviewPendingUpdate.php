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
    UPDATE_TYPE_INSERT => "Insert",
    UPDATE_TYPE_UPDATE => "Update",
    UPDATE_TYPE_DELETE => "Delete"
];

$UpdateType = $UpdateTypes[$Update->Attributes['UpdateTypeId']];

$data = unserialize( $Update->Attributes['UpdateContent'] );
$data_html = print_r($data,1);

$content = <<<EOT
<div class="row">
  <h1>Review Pending Update</h1>
</div>
<div class="row">
  <p>Update Type: <b>$UpdateType</b></p>
</div>
$data_html
<div class="row btn-toolbar">
    <button class="btn btn-primary btn-Approve" id="btn-Approve">Approve</button>
    <button class="btn btn-secondary btn-Reject" id="btn-Reject">Reject</button>
</div>
EOT;

$custom_css = <<<EOT
.btn-toolbar { margin-top: 20px; }
.btn {width: 125px; margin: 0 10px; }
EOT;

$custom_js = <<<EOT

EOT;



$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Review Pending Update";
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();