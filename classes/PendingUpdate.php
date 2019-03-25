<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/27/2019
 * Time: 10:11 AM
 */

class PendingUpdate extends AOREducationObject
{
    public static $table = "pending_updates";
    public static $primary_key = "UpdateId";
    public static $tableId = 16;
    public static $data_structure = array(
        'UpdateId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Update ID', 'editable' => FALSE ),
        'UpdateTypeId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Update Type', 'editable' => FALSE ),
        'TableId' => array( 'required' => TRUE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Table ID', 'editable' => FALSE ),
        'RecordId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Record ID', 'editable' => FALSE ),
        'UpdateRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Update Record ID', 'editable' => FALSE ),
        'UpdateContent' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Update Content', 'editable' => FALSE ),
        'UserId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'User ID', 'editable' => FALSE ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Approval Status', 'editable' => TRUE ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE )
    );

    public function approvalAction( $action ) {
        $this->update( 'ApprovalStatusId', $this->Attributes['ApprovalStatusId'] = $action );
        $Table = new Table( $this->Attributes['TableId'] );
        $Class = $Table->Attributes['ClassName'];
        if (!$Class) throw new Exception( "Table class not found." );
        if ( $action === APPROVAL_TYPE_APPROVE ) {
            switch ($this->Attributes['UpdateTypeId']) {
                case UPDATE_TYPE_INSERT:
                    $obj = new $Class( $this->Attributes['UpdateRecordId'] );
                    $obj->update( 'ApprovalStatusId', APPROVAL_TYPE_APPROVE );
                    break;
                case UPDATE_TYPE_UPDATE:
                    $newObj = new $Class( $this->Attributes['UpdateRecordId'] );
                    $newObj->update( 'ApprovalStatusId', APPROVAL_TYPE_APPROVE );
                    $oldObj = new $Class( $this->Attributes['RecordId'] );
                    $oldObj->update( 'ApprovalStatusId', APPROVAL_TYPE_RETIRED );
                    $newObj->swapId( $oldObj->id );
                    break;
                case UPDATE_TYPE_DELETE:
                    $Obj = new $class( $this->Attributes['RecordId'] );
                    $Obj->update( "Deleted", 1);
                    $Obj->update( "ApprovalStatusId", APPROVAL_TYPE_DELETED );
                    break;
                default:
                    throw new Exception("invalid update type indicated");
                    return FALSE;
            }
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

    public function swapId( $OldId ) {
        // this will be overridden for any class that uses "join" tables to swap in the new record ID where the old ID was
        // if a class has no associations like that, just do nothing
        return TRUE;
    }
}