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
        'DatasetId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => "Dataset ID" ),
        'DatasetName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR, 'label' => "Dataset Name" ),
        'DatasetType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => "Dataset Type" ),
        'DatasetIntegrity' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => "Dataset Integrity" ),
        'DatasetFileType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => "Dataset File Type" ),
        'DatasetUseDescription' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => "Dataset Use Description" ),
        'DatasetAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => "Dataset Access" ),
        'AnalyticTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => "Analytic Tag" ),
        'BusinessTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => "Business Tag" ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => "Create Date" ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => "Deleted" ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
        'LastModifiedDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Last Modified Date', 'editable' => FALSE ),
    );
    public static $full_text_columns = 'DatasetName, DatasetUseDescription';
    public static $name_sql = 'DatasetName';
    public static $parent_class = 'Course';
    public static $hidden_fields = ['OriginalRecordId'];

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

    public function getParent( $asObject = TRUE ) {
        $db = new EduDB;
        $sql = "SELECT CourseId FROM course_datasets WHERE DatasetId = $this->id";
        $CourseId = $db->queryItem( $sql );
        if ($asObject) return new Course( $CourseId );
        else return $CourseId;
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
            $data_html .= '<div class="data_label">' . self::$data_structure[$key]['label'] . '</div>';
            $data_html .= '<div class="data_value' . $changed_class . '">' . $value . '</div>';
            $data_html .= '</div>';
        }
        return $data_html;
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE course_datasets SET DatasetId = $this->id WHERE DatasetId = $OldId ";
        $db->exec( $sql );
        return TRUE;
    }
}