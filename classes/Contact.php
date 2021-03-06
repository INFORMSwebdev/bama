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
        'LastModifiedDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Last Modified Date', 'editable' => FALSE ),
    );
    public static $full_text_columns = 'ContactName, ContactEmail';
    public static $name_sql = 'ContactName';
    public static $parent_class = 'Program';
    public static $hidden_fields = ['OriginalRecordId'];

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

    public function getParent( $asObject = TRUE ) {
        $db = new EduDB;
        $sql = "SELECT ProgramId FROM program_contacts WHERE ContactId = $this->id";
        $ProgramId = $db->queryItem( $sql );
        if ($asObject) return new Program( $ProgramId );
        else return $ProgramId;
    }

    public function renderObject( $changed_keys = [] ) {
        $data_html = "";
        foreach( $this->Attributes as $key => $value ) {
            if (in_array( $key, self::$hidden_fields)) continue;
            // logic to render value differently based on type goes here
            switch ( $key ) {
                case 'ApprovalStatusId':
                    $value = AOREducationObject::getStatusLabelFromId( $value );
                    break;
                case 'Deleted':
                    $value = ($value) ? "Yes" : "No";
                    break;
            }

            if (!$value) $value = '&nbsp;';
            $changed_class = (in_array($key, $changed_keys)) ? ' changed' : '';
            $data_html .= '<div class="row data_row">';
            $data_html .= '<div class="data_label">' . Contact::$data_structure[$key]['label'] . '</div>';
            $data_html .= '<div class="data_value' . $changed_class . '">' . $value . '</div>';
            $data_html .= '</div>';
        }
        return $data_html;
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE program_contacts SET ContactId = $this->id WHERE ContactId = $OldId ";
        $db->exec( $sql );
        return TRUE;
    }
}