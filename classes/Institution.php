<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:08 PM
 */

class Institution extends AOREducationObject {
    public static $table = "institutions";
    public static $primary_key = "InstitutionId";
    public static $data_structure = array(
        'InstitutionId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'InstitutionName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'InstitutionAddress' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstitutionCity' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstitutionState' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstitutionZip' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstitutionRegion' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstitutionPhone' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstitutionEmail' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstitutionAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );

    public function assignAdmin( $UserId ) {

    }

    public function getColleges() {

    }

    public static function getInstitutions( $active = TRUE, $asObjects = FALSE) {
        $institutions = [];
        $db = new EduDB();
        $sql = "SELECT * FROM institutions";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $insts = $db->query( $sql );
        if ($asObjects) {
            foreach( $insts as $inst) {
                $institutions[] = new Institution($inst);
            }
        }
        else {
            $institutions = $insts;
        }

        return $institutions;
    }

    public function getPrograms() {

    }
}


