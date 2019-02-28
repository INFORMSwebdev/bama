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

EOT;

$custom_js = <<<EOT
$(function() {
  var usersTable = $('#usersTable').DataTable({
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
          var btn_edit = '<button class="btn btn-primary btn-approve btn-block" id="id_'+data+'">Approve</button>';
          var btn_del = '<button class="btn btn-secondary btn-reject btn-block" id="id_'+data+'">Reject</button>';
          return btn_edit + btn_del;
        }
      }
    ]
  });
  $(document).on( 'click keyup', '.btn-approve,.btn-reject', function(e) {
    var id = $(this).attr('id').substring(3);
    var action = $(this).hasClass('btn-approve') ? 2 : 3;
    $.post( '/scripts/ajax_approveUser.php', { 'PendingUserId': id, 'action': action }, function(data) {
      if (data.errors.length > 0 ) {
        var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
        for (var i = 0; i < data.errors.length; i++) {
          msg +=  data.errors[i] + "\\r\\n";
        }
        alert( msg );
      }
      else if (data.msg) {
        alert( data.msg );
        usersTable.ajax.reload();
      }
      else alert( "Something went wrong." );
    }, 'json');
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
