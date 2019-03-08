<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:20 PM
 */

class Contact extends AOREducationObject
{
    public static $table = "contacts";
    public static $primary_key = "ContactId";
    public static $tableId = 6;
    public static $data_structure = array(
        'ContactId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Contact ID', 'editable' => FALSE  ),
        'ContactName' => array( 'required' => TRUE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Contact Name', 'editable' => TRUE  ),
        'ContactTitle' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Contact Title', 'editable' => TRUE  ),
        'ContactPhone' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Contact Phone', 'editable' => TRUE  ),
        'ContactEmail' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Contact Email', 'editable' => TRUE  ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE  ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE  ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
    );

    public function setInstitutionContact( $InstitutionId ){
      // this can be accomplished by updating the institution object's ContactId attribute so not adding code for this until later
    }

    public static function getAllContacts( $active = TRUE, $asObjects = FALSE ){
        $contacts = [];
        $db = new EduDB();
        $sql = "SELECT * FROM contacts";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $conts = $db->query( $sql );
        if ($asObjects) {
            foreach( $conts as $cont) {
                $contacts[] = new Contact($cont);
            }
        }
        else {
            $contacts = $conts;
        }

        return $contacts;
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE program_contacts SET ContactId = $this->id WHERE ContactId = $OldId ";
        $db->exec( $sql );
        return TRUE;
    }
}