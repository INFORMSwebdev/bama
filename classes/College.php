<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:19 PM
 */

class College extends AOREducationObject
{
    public static $table = "colleges";
    public static $primary_key = "CollegeId";
    public static $tableId = 5;
    public static $data_structure = array(
        'CollegeId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'College ID', 'editable' => FALSE ),
        'InstitutionId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Institution', 'editable' => FALSE ),
        'CollegeName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'College Name', 'editable' => TRUE ),
        //'CollegeType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'College Type', 'editable' => TRUE ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
        'LastModifiedDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Last Modified Date', 'editable' => FALSE ),
        'TypeId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'College Type', 'editable' => TRUE ),
        'OtherType' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Other Type', 'editable' => TRUE ),
    );
    public static $full_text_columns = 'CollegeName';
    public static $name_sql = 'CollegeName';
    public static $parent_class = 'Institution';
    public static $hidden_fields = ['OriginalRecordId', 'OtherType'];

    public static function getAllColleges( $active = TRUE, $asObjects = FALSE ){
        $colleges = [];
        $db = new EduDB();
        $sql = "SELECT * FROM colleges";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $colls = $db->query( $sql );
        if ($asObjects) {
            foreach( $colls as $col) {
                $colleges[] = new College($col);
            }
        }
        else {
            $colleges = $colls;
        }

        return $colleges;
    }

    public function getCollegeTypeLabel() {
        $db = new EduDb;
        $sql = "SELECT name FROM college_type_dropdown WHERE id = {$this->Attributes['TypeId']}";
        $Type = $db->queryItem( $sql );
        if ($Type === "Other") {
            $Type = "Other&mdash;" . $this->Attributes['OtherType'];
        }
        return $Type;
    }

    public function getName() {
        return $this->Attributes['CollegeName'];
    }

    public static function getNameById( $id ) {
        $db = new EduDB;
        $sql = "SELECT CollegeName FROM colleges WHERE CollegeId = $id ";
        return $db->queryItem( $sql );
    }

    public function getType(){
        $db = new EduDb();
        $sql = 'SELECT name FROM college_type_dropdown a INNER JOIN colleges b ON a.id = b.TypeId WHERE b.COllegeId = ' . $this->id;
        $results = $db->queryColumn($sql);
        return $results[0];
    }

    public function getOtherType(){
        $db = new EduDb();
        $sql = 'SELECT OtherType FROM colleges WHERE CollegeId = ' . $this->id;
        $results = $db->queryColumn($sql);
        return $results[0];
    }

    public function getParent( $asObject = TRUE ) {
        /*$db = new EduDB;
        $sql = "SELECT InstitutionId FROM colleges WHERE CollegeId = $this->id";
        $InstitutionId = $db->queryItem( $sql );*/
        $InstitutionId = $this->Attributes['InstitutionId'];
        if ($asObject) return new Institution( $InstitutionId );
        else return $InstitutionId;
    }

    public function hasPrograms() {
        $db = new EduDB;
        $sql = "SELECT ProgramId FROM programs WHERE CollegeId = $this->id";
        $ProgramIds = $db->query( $sql );
        return (count($ProgramIds)) ? TRUE : FALSE;
    }

    public function renderObject( $changed_keys = [] ) {
        $data_html = "";
        foreach( $this->Attributes as $key => $value ) {
            if (in_array( $key, self::$hidden_fields)) continue;
            // logic to render value differently based on type goes here
            switch ( $key ) {
                case 'TypeId':
                    $value = $this->getCollegeTypeLabel();
                    if (in_array( 'OtherType', $changed_keys)) $changed_keys[] = 'TypeId';
                    break;
                case 'ApprovalStatusId':
                    $value = AOREducationObject::getStatusLabelFromId( $value );
                    break;
                case 'Deleted':
                    $value = ($value) ? "Yes" : "No";
                    break;
                case 'InstitutionId':
                    $value = Institution::getNameById( $value );
                    break;
                default:
                    $value = $value; // I know this line is unnecessary but putting it in so the logic here is more clear
            }

            if (!$value) $value = '&nbsp;';
            $changed_class = (in_array($key, $changed_keys)) ? ' changed' : '';
            $data_html .= '<div class="row data_row">';
            $data_html .= '<div class="data_label">' . College::$data_structure[$key]['label'] . '</div>';
            $data_html .= '<div class="data_value' . $changed_class . '">' . $value . '</div>';
            $data_html .= '</div>';
        }
        return $data_html;
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE programs SET CollegeId = $this->id WHERE CollegeId = $OldId";
        $db->exec( $sql );
        return TRUE;
    }
}