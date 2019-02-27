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
      <tr><th>First Name</th><th>Last Name</th><th>Username</th><th>Created</th><th>Institution</th><th>Comments</th><th>Approval</th></tr>
    </thead>
    <tbody></tbody>
  </table>
</div>
EOT;

$custom_css = <<<EOT
.btn-approve, .btn-deny {
  width: 80px;
}
EOT;

$custom_js = <<<EOT
$(function() {
  $('#usersTable').DataTable({
    "ajax": {
      "url": "/scripts/ajax_getPendingUsers.php",
      "dataSrc":""
    },
    "order": [[3, 'desc'],[ 1, 'asc' ], [ 0, 'asc' ]],
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
        "data": "Institution"
      },
      {
        "targets": 5,
        "data": "Comments"
      },
      { 
        "targets": 6,
        "orderable": false, 
        "data": "PendingUserId",
        "className": "ctrl-col",
        "render": function ( data, type, row, meta ) {
          var btn_edit = '<button class="btn-approve" id="id_'+data+'">Approve</button>';
          var btn_del = '<button class="btn-deny" id="id_'+data+'">Deny</button>';
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
$p_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();
