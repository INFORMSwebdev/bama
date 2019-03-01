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
     * @return int number of database rows affected by operation
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
     * @return int number of database rows affected by operation
     */
    public function unassignFromCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "DELETE FROM course_datasets WHERE CourseId = :CourseId AND DatasetId = $this->id";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }


    /**
     * @param bool $active true = records not marked as Deleted, false = records marked as Deleted
     * @param bool $asObjects true = array of dataset objects, false = associative array of dataset records
     * @return array DatasetId's if asObjects = false | Dataset objects if asObjects = true
     */
    public static function getAllDatasets($active = TRUE, $asObjects = FALSE ){
        $datasets = [];
        $db = new EduDB();
        $sql = "SELECT * FROM datasets";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $sets = $db->query( $sql );
        if ($asObjects) {
            foreach( $sets as $set) {
                $datasets[] = new Dataset($set);
            }
        }
        else {
            $datasets = $sets;
        }

        return $datasets;
    }
}