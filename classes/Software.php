<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:24 PM
 */

class Software extends AOREducationObject
{
    public static $table = "softwares";
    public static $primary_key = "SoftwareId";
    public static $tableId = 20;
    public static $data_structure = array(
        'SoftwareId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Software ID', 'editable' => FALSE ),
        'SoftwareName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR, 'label' => 'Software Name', 'editable' => TRUE ),
        'SoftwarePublisher' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Software Publisher', 'editable' => TRUE ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE )
    );

    /**
     * add course - software association
     * @param $CourseId int
     * @return int number of database rows affected by operation
     */
    public function assignToCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_softwares (CourseId, SoftwareId) VALUES (:CourseId, $this->id)";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * delete course - software association
     * @param $CourseId int
     * @return int number of database rows affected by operation
     */
    public function unassignFromCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "DELETE FROM course_softwares WHERE CourseId = :CourseId AND SoftwareId = $this->id";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    public static function getAllSoftware($active = TRUE, $asObjects = FALSE ){
        $softwares = [];
        $db = new EduDB();
        $sql = "SELECT * FROM softwares";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $softs = $db->query( $sql );
        if ($asObjects) {
            foreach( $softs as $set) {
                $softwares[] = new Software($set);
            }
        }
        else {
            $softwares = $softs;
        }

        return $softwares;
    }
}