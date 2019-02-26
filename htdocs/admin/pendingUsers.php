<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/26/2019
 * Time: 4:42 PM
 */

require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}

$content = <<<EOT
<div class="row">
    <h1>Analytics & OR Education Database ADMIN</h1>
</div>
<div class="row">
    <h2>Pending Users</h2>
</div>
<div class="row" id="userTableContainer">
  <table id="usersTable">
    <thead>
      <tr><th>First Name</th><th>Last Name</th><th>Username</th><th>Created</th><th>InstitutionId</th></tr>
    </thead>
    <tbody></tbody>
  </table>
</div>
EOT;

$custom_css = <<<EOT

EOT;

$custom_js = <<<EOT
$(function() {
  $('#usersTable').DataTable({
    "ajax": {
      "url": "/scripts/ajax_getPendingUsers.php",
      "dataSrc":""
    },
    "language": {
        "infoEmpty":     "My Custom Message On Empty Table"
    },
    "order": [[ 1, 'asc' ], [ 0, 'asc' ]],
    "columnDefs": [
      {
        "targets": 0,
        "data": "FirstName"
      },
      {
        "targets": 1,
        "data": "LastName"
      },
      {
        "targets": 2,
        "data": "Username"
      },
      {
        "targets": 3,
        "data": "Created"
      },
      {
        "targets": 4,
        "data": "InstitutionId"
      },
      { 
        "targets": 5,
        "orderable": false, 
        "data": "PendingUserId",
        "className": "ctrl-col",
        "render": function ( data, type, row, meta ) {
          var btn_edit = '<input class="btn-edit" type="image" src="/images/icon-edit.png" id="id_'+data+'" />';
          var btn_del = '<input class="btn-delete" type="image" src="/images/icon-delete.png" id="id_'+data+'" />';
          return btn_edit + btn_del;
        }
      }
    ]
  });
});
EOT;


$p_params = [];
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['css'][] = array( 'url' => 'https://common.informs.org/js/DataTables-1.9.4/media/css/jquery.dataTables.css' );
$p_params['js'][] = array( 'text' => $custom_js );
$p_params['js'][] = array( 'url' => 'https://common.informs.org/js/DataTables-1.9.4/media/js/jquery.dataTables.min.js' );
$wrapper = new wrapperBama($p_params);
$wrapper->html();
