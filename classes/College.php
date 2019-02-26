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
        'CollegeId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'InstitutionId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'CollegeName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CollegeType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
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
}