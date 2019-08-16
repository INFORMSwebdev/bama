<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/2/2019
 * Time: 2:04 PM
 */

require_once( "../../init.php" );
if(!isset($_SESSION['admin']) || !$_SESSION['admin']) die("unauthorized access");

$response = [];
$errors = [];
$msg = '';

$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_NUMBER_INT );
if (!$action) die ( "missing required parameter: action" );
$UpdateId = filter_input( INPUT_POST, 'UpdateId', FILTER_SANITIZE_NUMBER_INT );
if (!$UpdateId) die( "missing required parameter: UpdateId" );

$PendingUpdate = new PendingUpdate( $UpdateId );
try {
    $result = $PendingUpdate->approvalAction( $action );
    $actionDesc = ($action == APPROVAL_TYPE_APPROVE) ? "approved" : "rejected";
    $msg = "Update $actionDesc.";
    // TO DO: send mail to editor to notify about approval
    $e_params = [];
    $recipients = [];
    $TableId = $PendingUpdate->Attributes['TableId'];
    $Table = new Table( $TableId );
    $Class = $Table->Attributes['ClassName'];
    $params = unserialize( $PendingUpdate->Attributes['UpdateContent']);
    if (isset($params[$Class::$primary_key])) $RecordId = $params[$Class::$primary_key];
    //$RecordId = ($PendingUpdate->Attributes['UpdateTypeId']==UPDATE_TYPE_DELETE) ? $PendingUpdate->Attributes['RecordId'] : $PendingUpdate->Attributes['UpdateRecordId'];
    //if ($PendingUpdate->Attributes['UpdateTypeId'] == UPDATE_TYPE_INSERT) $RecordId = $result;
    $Obj = new $Class( $RecordId );

    if ($Class=='Institution') {
        $inst = $Obj;
    }
    else {
        $O_ancestry = $Obj->getAncestry( FALSE );
        $inst = $O_ancestry[count($O_ancestry)-1];
    }

    $Users = $inst->getUserAssignments( TRUE );
    foreach ($Users as $User) {
        if (filter_var( $User->Attributes['Username'], FILTER_VALIDATE_EMAIL)) {
            $recipients[] = $User->Attributes['Username'];
        }
    }
    $approved = ($action==APPROVAL_TYPE_APPROVE)? "approved" : "denied";
    $details = '<br /><br />';
    $S_ancestry = $Obj->getAncestry();
    $details .= '<p>'.$S_ancestry.'</p>';
    foreach( $Class::$data_structure AS $key => $props ) {
        $details .= $props['label'] . ": ".$Obj->Attributes[$key]."<br/>";
    }
    if (count($recipients)) {
        $e_params['to'] = implode(",", $recipients );
        $e_params['subject'] = "Analytics and Operations Research Education Database - Pending Update Approval";
        $e_params['body_html'] = <<<EOT
<p>The submitted update request has been $approved.</p>
$details
<p>Please address questions and concerns to <a href="mailto:educationresources@informs.org">educationresources@informs.org</a>.</p>
EOT;
        $email = new email($e_params);
        $email->send();
    }
    $e_params = [];
    $e_params['to'] = ADMIN_EMAIL;
    $e_params['subject'] = "Analytics and Operations Research Education Database - Pending Update Approval";
    $e_params['body_html'] = <<<EOT
<p>The submitted update request was $approved.</p>
$details
EOT;
    $email = new email($e_params);
    $email->send();
}
catch (Exception $e) {
    $errors[] = $e->getMessage();
}

$response['errors'] = $errors;
$response['msg'] = $msg;
echo json_encode( $response );