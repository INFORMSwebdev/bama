<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:22 PM
 */

class Dataset extends AOREducationObject
{
    public static $table = "datasets";
    public static $primary_key = "DatasetId";
    public static $tableId = 12;
    public static $data_structure = array(
        'DatasetId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'DatasetName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'DatasetType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DatasetIntegrity' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DatasetFileType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DatasetUseDescription' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DatasetAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'AnalyticTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'BusinessTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );

    /**
     * add course - dataset association
     * @param $CourseId int
     * @returns int number of database rows affected by operation
     */
    public function assignToCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_datasets (CourseId, DatasetId) VALUES (:CourseId, $this->id)";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * delete course - dataset association
     * @param $CourseId int
     * @returns int number of database rows affected by operation
     */
    public function unassignFromCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "DELETE FROM course_datasets WHERE CourseId = :CourseId AND DatasetId = $this->id";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }
}