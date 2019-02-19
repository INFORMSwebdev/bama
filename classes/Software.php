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
    public static $data_structure = array(
        'SoftwareId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'SoftwareName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'SoftwarePublisher' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );

    /**
     * add course - software association
     * @param $CourseId int
     * @returns int number of database rows affected by operation
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
     * @returns int number of database rows affected by operation
     */
    public function unassignFromCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "DELETE FROM course_softwares WHERE CourseId = :CourseId AND SoftwareId = $this->id";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }
}