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

$appStatusNew = APPROVAL_TYPE_NEW;
$appStatusApproved = APPROVAL_TYPE_APPROVE;
$appStatusRejected = APPROVAL_TYPE_REJECT;

$content = <<<EOT

<div class="flex-column">
    <h1>Analytics & OR Education Database ADMIN</h1>
</div>
<div class="flex-column">
    <h2>Pending Updates</h2>
</div>
<div class="flex-column form-group" id="statusFilterContainer"> 
    <span class="filter-label">Status Filter:</span>
    <div class="form-check-inline">
      <label class="form-check-label" for="cb-New">
        <input type="checkbox" id="cb-New" name="status[]" value="$appStatusNew" checked/>New
      </label>
    </div>
    <div class="form-check-inline">
      <label class="form-check-label" for="cb-Approved">
        <input type="checkbox" id="cb-Approved" name="status[]" value="$appStatusApproved" />Approved
      </label>
    </div>
    <div class="form-check-inline">
      <label class="form-check-label" for="cb-Rejected">
        <input type="checkbox" id="cb-Rejected" name="status[]" value="$appStatusRejected" />Rejected
      </label>
    </div>
</div>
<div class="container-fluid">
<table id="pendingUpdatesTable" class="table-striped">

  <thead> 
    <tr><th>Username</th><th>Category</th><th>Update Type</th><th>Created</th><th>Status</th><th>Action</th></tr>
  </thead>
  <tbody> 
  </tbody>
</table>
</div>
EOT;

$custom_css = <<<EOT
.filter-label { padding: 0 10px; }
.filter-control-group { margin: 5px 0; }
EOT;

$custom_js = <<<EOT
$(function() {
  $('#statusFilterContainer :input').click(function(e) {
    var statuses = decodeURIComponent( $('#statusFilterContainer :input:checked').serialize() );
    updatesTable.ajax.url( "/scripts/ajax_getPendingUpdates.php?"+statuses).load();
  });
  $(document).on( 'click keyup', '.btn-review', function(e) {
    var id = $(this).attr('id').substring(3);
    window.location.href="/admin/reviewPendingUpdate.php?UpdateId="+id;
  });
  var statuses = decodeURIComponent( $('#statusFilterContainer :input:checked').serialize() );
  var updatesTable = $('#pendingUpdatesTable').DataTable({
    "ajax": {
      "url": "/scripts/ajax_getPendingUpdates.php?"+statuses,
      "dataSrc":""
    },
    "order": [[3, 'desc']],
    "columnDefs": [
      {
        "targets": 0,
        "data": "Username"
      },
      {
        "targets": 1,
        "data": "Category"
      },
      {
        "targets": 2,
        "data": "UpdateType"
      },
      {
        "targets": 3,
        "data": "Created"
      },
      {
        "targets": 4,
        "data": "Status"
      },
      { 
        "targets": 5,
        "orderable": false, 
        "data": "UpdateId",
        "className": "ctrl-col",
        "render": function ( data, type, row, meta ) {
          var btn_review = '<button class="btn btn-primary btn-review btn-block" id="id_'+data+'">Review</button>';
          return btn_review;
        }
      }
    ]
  });
});
EOT;

$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Pending Updates";
$p_params['content'] = $content;
$p_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
$p_params['css'][] = array( 'text' => $custom_css );
$p_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();
