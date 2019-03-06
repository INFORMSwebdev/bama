<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/27/2019
 * Time: 10:07 AM
 */

class PendingUser extends AOREducationObject
{
    public static $table = "pending_users";
    public static $primary_key = "PendingUserId";
    public static $tableId = 17;
    public static $data_structure = array(
        'PendingUserId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Pending User ID', 'editable' => FALSE ),
        'Username' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR, 'label' => 'Username / Email Address', 'editable' => FALSE ),
        'FirstName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'First Name', 'editable' => FALSE ),
        'LastName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Last Name', 'editable' => FALSE ),
        'InstitutionId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Institution ID', 'editable' => FALSE ),
        'Comments' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Comments', 'editable' => FALSE ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Approval Status', 'editable' => TRUE ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE )
    );

    public function approvalAction( $action ) {
        $this->update( 'ApprovalStatusId', $this->Attributes['ApprovalStatusId'] = $action );
        if ( $action === APPROVAL_TYPE_APPROVE ) {
            // first check to see if user with this email exists
            $exists = User::getUserByEmail( $this->Attributes['Username'] );
            if ($exists) {
                $this->update( 'ApprovalStatusId', APPROVAL_TYPE_REJECT );
                throw new Exception("Email address already in system.");
                return FALSE;
            }
            $UserId = User::create(
                [
                    'Username' => $this->Attributes['Username'],
                    'FirstName' => $this->Attributes['FirstName'],
                    'LastName' => $this->Attributes['LastName']
                ] );
            $User = new User( $UserId );

            if ($this->Attributes['InstitutionId']) $User->assignToInstitution( $this->Attributes['InstitutionId'] );
            $User->sendInviteEmail();
        }
        return TRUE;
    }

    public function approve() {
        return $this->approvalAction( APPROVAL_TYPE_APPROVE );
    }

    public function deny() { // this is just alias for reject method
        return $this->approvalAction( APPROVAL_TYPE_REJECT );
    }

    public function reject() {
        return $this->approvalAction( APPROVAL_TYPE_REJECT );
    }
}