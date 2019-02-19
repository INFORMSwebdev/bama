<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:21 PM
 */
class Course extends AOREducationObject
{
    public static $table = "courses";
    public static $primary_key = "CourseId";
    public static $data_structure = array(
        'CourseId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'InstructorId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT ),
        'CourseNumber' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CourseTitle' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DeliveryMethod' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'HasCapstoneProject' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'SyllabusFile' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_LOB ),
        'SyllabusFilesize' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT ),
        'AnalyticTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'BusinessTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );

    /**
     * add course - case study association
     * @param $CaseId int
     * @returns int number of database rows affected by operation
     */
    public function assignCaseStudy( $CaseId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_cases (CourseId, CaseId) VALUES ($this->id, :CaseId)";
        $params = array( array( ":CaseId", $CaseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * add course - dataset association
     * @param $DatasetId int
     * @returns int number of database rows affected by operation
     */
    public function assignDataset( $DatasetId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_datasets (CourseId, DatasetId) VALUES ($this->id, :DatasetId)";
        $params = array( array( ":DatasetId", $DatasetId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * add course - software association
     * @param $SoftwareId int
     * @returns int number of database rows affected by operation
     */
    public function assignSoftware( $SoftwareId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_softwares (CourseId, SoftwareId) VALUES ($this->id, :SoftwareId)";
        $params = array( array( ":SoftwareId", $SoftwareId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * add course - textbook association
     * @param $TextbookId int
     * @returns int number of database rows affected by operation
     */
    public function assignTextbook( $TextbookId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_textbooks (CourseId, TextbookId) VALUES ($this->id, :TextbookId)";
        $params = array( array( ":TextbookId", $TextbookId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * delete course - case study association
     * @param $CaseId int
     * @returns int number of database rows affected by operation
     */
    public function unassignCaseStudy( $CaseId ) {
        $db = new EduDB();
        $sql = "DELETE course_cases WHERE CourseId = $this->id AND CaseId = :CaseId)";
        $params = array( array( ":CaseId", $CaseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * deleete course - dataset association
     * @param $DatasetId int
     * @returns int number of database rows affected by operation
     */
    public function unassignDataset( $DatasetId ) {
        $db = new EduDB();
        $sql = "DELETE course_datasets WHERE CourseId = $this->id AND DatasetId = :DatasetId)";
        $params = array( array( ":DatasetId", $DatasetId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * delete course - software association
     * @param $SoftwareId int
     * @returns int number of database rows affected by operation
     */
    public function unassignSoftware( $SoftwareId ) {
        $db = new EduDB();
        $sql = "DELETE course_softwares WHERE CourseId = $this->id AND SoftwareId = :SoftwareId)";
        $params = array( array( ":SoftwareId", $SoftwareId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * delete course - textbook association
     * @param $TextbookId int
     * @returns int number of database rows affected by operation
     */
    public function unassignTextbook( $TextbookId ) {
        $db = new EduDB();
        $sql = "DELETE course_textbooks WHERE CourseId = $this->id AND TextbookId = :TextbookId)";
        $params = array( array( ":TextbookId", $TextbookId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }
}