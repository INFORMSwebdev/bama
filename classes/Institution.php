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
    public static $tableId = 14;
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
        $db = new EduDb;
        $sql = "INSERT IGNORE INTO institution_admins (InstitutionId, UserId) VALUES ($this->id, :UserId)";
        $params = array( array( ":UserId", $UserId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
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

    public static function getInstitutionIds($active = TRUE){
        $db = new EduDB();
        $sql = "SELECT InstitutionId FROM institutions";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        return $db->query( $sql );

    }

    public function getPrograms() {

    }

    public function getUserAssignments( $asObjects = FALSE ) {
        $db = new EduDb;
        $sql = "SELECT UserID FROM institution_admins WHERE InstitutionId = $this->id";
        $users = $db->queryColumn( $sql );
        if ($asObjects)  {
            foreach( $users as &$user) $user = new User($user);
        }
        return $users;
    }
}


