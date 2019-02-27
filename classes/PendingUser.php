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
        'PendingUserId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'Username' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'FirstName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'LastName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstitutionId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'Comments' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR )
    );

    public function approvalAction( $action ) {
      if ($action === APPROVAL_TYPE_APPROVE) {

      }
      else {

      }
    }

    public function approve() {
        return $this->approvalAction( APPROVAL_TYPE_APPROVE );
    }

    public function deny() {
        return $this->approvalAction( APPROVAL_TYPE_REJECT );
    }

    public function reject() {
        return $this->approvalAction( APPROVAL_TYPE_REJECT );
    }
}