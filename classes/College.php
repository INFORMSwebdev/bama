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
        'InstitutionId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Institution ID', 'editable' => FALSE ),
        'CollegeName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'College Name', 'editable' => TRUE ),
        'CollegeType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'College Type', 'editable' => TRUE ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Delete', 'editable' => FALSE )
    );

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

    public function hasPrograms() {
        $db = new EduDB;
        $sql = "SELECT ProgramId FROM programs WHERE CollegeId = $this->id";
        $ProgramIds = $db->query( $sql );
        return (count($ProgramIds)) ? TRUE : FALSE;
    }
}