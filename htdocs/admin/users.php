<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/28/2019
 * Time: 10:22 AM
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
    <h2>Users</h2>
</div>

<div class="container-fluid" id="userTableContainer">
  <table id="usersTable" class="table-striped">
    <thead>
      <tr><th>First Name</th><th>Last Name</th><th>Username</th><th>Created</th><th>Institution</th><th>Comments</th><th>Action</th></tr>
    </thead>
    <tbody></tbody>
  </table>
</div>
EOT;

$custom_css = <<<EOT
table.dataTable tbody td.ctrl-col {
    padding: 8px 10px;
    white-space: nowrap;
}
EOT;

$custom_js = <<<EOT
$(function() {
  var usersTable = $('#usersTable').DataTable({
    "ajax": {
      "url": "/scripts/ajax_getUsers.php",
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
        "data": "UserId",
        "className": "ctrl-col",
        "render": function ( data, type, row, meta ) {
          var btn_edit = '<input type="image" src="/images/icon-edit.png" class="btn btn-edit" id="id_'+data+'" />';
          var btn_del = '<input type="image" src="/images/icon-delete.png" class="btn btn-delete" id="id_'+data+'" />';
          return btn_edit + btn_del;
        }
      }
    ]
  });
  $(document).on( 'click keyup', '.btn-edit', function(e) {
    var id = $(this).attr('id').substring(3);
    window.location.href="/admin/editUser.php?UserId="+id;
  });
  $(document).on( 'click keyup', '.btn-delete', function(e) {
    var conf = confirm( "Are you sure you want to delete this user?" );
    if (conf) {
      var id = $(this).attr('id').substring(3);
      $.post( "/scripts/ajax_deleteUser.php", { 'UserId': id }, function(data) {
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
      }, "json");
    }
  });
});
EOT;


$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Users";
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();
