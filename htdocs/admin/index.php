<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/20/2019
 * Time: 2:14 PM
 */

require_once( "../../init.php");
if (!isset($_SESSION['admin'])) {
    header( "Location: /users/admin_login.php" );
    exit;
}
$errors = [];

$appStatusNew = APPROVAL_TYPE_NEW;
$appStatusApproved = APPROVAL_TYPE_APPROVE;
$appStatusRejected = APPROVAL_TYPE_REJECT;
$appStatusRetired = APPROVAL_TYPE_RETIRED;
$appStatusDeleted = APPROVAL_TYPE_DELETED;

$content = <<<EOT
<div class="container">
	<div class="flex-column">
		<h1>Analytics & OR Education Database ADMIN</h1>
    </div>
</div>
<div>
    <p><a href="/admin/users.php">List Users</a></p>
    <p><a href="/admin/invite.php">Invite User</a></p>
    <p><a href="/admin/pendingUsers.php">Pending Users</a></p>
    <p><a href="/admin/pendingUpdates.php">Pending Updates</a></p>
    <p><a href="/admin/addInstitution.php">Add Institution</a></p>
</div>
<div id="searchFormContainer"> 
  <div class="flex-column form-group" id="statusFilterContainer"> 
    <span class="filter-label">Status Filter:</span>
    <div class="form-check-inline">
      <label class="form-check-label" for="cb-New">
        <input type="checkbox" id="cb-New" name="status[]" value="$appStatusNew" />New
      </label>
    </div>
    <div class="form-check-inline">
      <label class="form-check-label" for="cb-Approved">
        <input type="checkbox" id="cb-Approved" name="status[]" value="$appStatusApproved" checked />Approved
      </label>
    </div>
    <div class="form-check-inline">
      <label class="form-check-label" for="cb-Rejected">
        <input type="checkbox" id="cb-Rejected" name="status[]" value="$appStatusRejected" />Rejected
      </label>
    </div>
    <div class="form-check-inline">
      <label class="form-check-label" for="cb-Rejected">
        <input type="checkbox" id="cb-Rejected" name="status[]" value="$appStatusRetired" />Rejected
      </label>
    </div>
    <div class="form-check-inline">
      <label class="form-check-label" for="cb-Rejected">
        <input type="checkbox" id="cb-Rejected" name="status[]" value="$appStatusDeleted" />Rejected
      </label>
    </div>
  </div>
</div>
EOT;

$custom_js = <<<EOT
$(function() {
  
});
EOT;


$p_params = [];
$p_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN";
$p_params['content'] = $content;
$p_params['admin'] = TRUE;
$p_params['js'][] = array( 'text' => $custom_js );
$wrapper = new wrapperBama($p_params);
$wrapper->html();