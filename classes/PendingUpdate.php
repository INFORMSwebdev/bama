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
            $updateContent = unserialize($this->Attributes['UpdateContent']);
            // TODO: Add code such that contact updates contained in program updates and
            //  instructor updates contained in courses get written, or else they will
            //  get thrown out during the insert/update process
            switch ($this->Attributes['UpdateTypeId']) {
                case UPDATE_TYPE_INSERT:
                    $NewId = $Class::create( $updateContent );
                    if ($Class == 'Program') {
                         $ContactId = Contact::create( $updateContent );
                        $Program = new Program( $NewId );
                         $Program->update( 'ContactId', $ContactId );
                    }
                    elseif ($Class == 'Course') {
                        $InstructorId = Instructor::create( $updateContent );
                        $Course = new Course( $NewId );
                        $Course->update( 'InstructorId', $InstructorId );
                    }
                    break;
                case UPDATE_TYPE_UPDATE:
                    $Obj = new $Table->Attributes['ClassName']( $this->Attributes['RecordId'] );
                    $Obj->updateMultiple( $updateContent );
                    /*if ($Class == 'Program') {
                        $Contact = new Contact( $Obj->Attributes['ContactId']);
                        $Contact->updateMultiple( $updateContent );
                    }
                    elseif ($Class == 'Course') {
                        $Course = new Course( $Obj->Attributes['InstructorId'] );
                        $Course->updateMultiple( $updateContent );
                    }*/
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