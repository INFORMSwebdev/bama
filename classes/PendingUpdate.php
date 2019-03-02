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
        'UpdateId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'UpdateTypeId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'TableId' => array( 'required' => TRUE, 'datatype'=> PDO::PARAM_INT ),
        'RecordId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'UpdateContent' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'UserId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR )
    );

    public function approvalAction( $action ) {
        $this->update( 'ApprovalStatusId', $this->Attributes['ApprovalStatusId'] = $action );
        $Table = new Table( $this->Attributes['TableId'] );
        $Class = $Table->Attributes['ClassName'];
        if (!$Class) throw new Exception( "Table class not found." );
        if ( $action === APPROVAL_TYPE_APPROVE ) {
            switch ($this->Attributes['UpdateTypeId']) {
                case UPDATE_TYPE_INSERT:
                    $Class::create( unserialize($this->Attributes['UpdateContent']) );
                    break;
                case UPDATE_TYPE_UPDATE:
                    $Obj = new $Table->Attributes['ClassName']( $this->Attributes['RecordId'] );
                    $Obj->updateMultiple( unserialize($this->Attributes['UpdateContent']) );
                    break;
                case UPDATE_TYPE_DELETE:
                    $Obj = new $Table->Attributes['ClassName']( $this->Attributes['RecordId'] );
                    $Obj->update( "Deleted", 1);
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
}