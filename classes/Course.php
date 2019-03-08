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
    public static $tableId = 11;
    public static $data_structure = array(
        'CourseId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Course ID', 'editable' => FALSE  ),
        'InstructorId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'InstructorID', 'editable' => TRUE  ),
        'CourseNumber' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Course Number', 'editable' => TRUE  ),
        'CourseTitle' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR, 'label' => 'Course Title', 'editable' => TRUE  ),
        'DeliveryMethod' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Delivery Method', 'editable' => TRUE  ),
        'HasCapstoneProject' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Has Capstone Project', 'editable' => TRUE  ),
        'CourseText' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Course Text', 'editable' => TRUE ),
        'SyllabusFile' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_LOB, 'label' => 'Syllabus File', 'editable' => TRUE  ),
        'SyllabusFilesize' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Syllabus File Size', 'editable' => TRUE  ),
        'AnalyticTag' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Analytic Tag', 'editable' => TRUE  ),
        'BusinessTag' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Business Tag', 'editable' => TRUE  ),
        'CreateDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE  ),
        'Deleted' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE  )
    );

    /**
     * add course - case study association
     * @param $CaseId int
     * @return int number of database rows affected by operation
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
     * @return int number of database rows affected by operation
     */
    public function assignDataset( $DatasetId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_datasets (CourseId, DatasetId) VALUES ($this->id, :DatasetId)";
        $params = array( array( ":DatasetId", $DatasetId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    public function assignInstructor( $InstructorId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_instructors (CourseId, InstructorId) VALUES ($this->id, :InstructorId)";
        $params = array( array( ":InstructorId", $InstructorId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * add course - software association
     * @param $SoftwareId int
     * @return int number of database rows affected by operation
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
     * @return int number of database rows affected by operation
     */
    public function assignTextbook( $TextbookId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_textbooks (CourseId, TextbookId) VALUES ($this->id, :TextbookId)";
        $params = array( array( ":TextbookId", $TextbookId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    public function getInstructor() {
        if (!$this->hasInstructor()) return FALSE;
        $Instructor = new Instructor( $this->Attributes['InstructorId']);
        return $Instructor;
    }

    public function getInstructors( $asObjects ) {
        $db = new EduDB;
        $Instructors = [];
        $sql = "SELECT InstructorId FROM course_instructors WHERE CourseId = $this->id";
        $InstructorIds = $db->query( $sql );
        foreach( $InstructorIds as $InstructorId) {
            $Instructor = new Instructor( $InstructorId );
            if ($asObject) $Instructors[] = $Instructor;
            else $Instructors[] = $Instructor->Attributes;
        }

        return $Instructors;
    }

    public function hasCases() {
        $db = new EduDB;
        $sql = "SELECT CaseId FROM course_cases WHERE CourseId = $this->id AND Deleted = 0";
        $Ids = $db->query( $sql );
        return (count($Ids)) ? TRUE : FALSE;
    }

    public function hasDatasets() {
        $db = new EduDB;
        $sql = "SELECT DatasetId FROM course_datasets WHERE CourseId = $this->id AND Deleted = 0";
        $Ids = $db->query( $sql );
        return (count($Ids)) ? TRUE : FALSE;
    }

    public function hasInstructor() {
        $hasInstructor = FALSE;
        if ($this->Attributes['InstructorId']) {
            $Instructor = new Instructor( $this->Attributes['InstructorId'] );
            if ($Instructor->valid && !$Instructor->Attributes['Deleted']) $hasInstructor = TRUE;
        }
        return $hasInstructor;
    }

    public function hasInstructors() {
        $Instructors = $this->getInstructors();
        return (count($Instructors)) ? TRUE : FALSE;
    }

    public function hasSoftwares() {
        $db = new EduDB;
        $sql = "SELECT SoftwareId FROM course_softwares WHERE CourseId = $this->id AND Deleted = 0";
        $Ids = $db->query( $sql );
        return (count($Ids)) ? TRUE : FALSE;
    }

    public function hasTextbooks() {
        $db = new EduDB;
        $sql = "SELECT TextbookId FROM course_textbooks WHERE CourseId = $this->id AND Deleted = 0";
        $Ids = $db->query( $sql );
        return (count($Ids)) ? TRUE : FALSE;
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE course_cases SET CourseId = $this->id WHERE CourseId = $OldId ";
        $db->exec( $sql );
        $sql = "UPDATE course_datasets SET CourseId = $this->id WHERE CourseId = $OldId ";
        $db->exec( $sql );
        $sql = "UPDATE course_instructors SET CourseId = $this->id WHERE CourseId = $OldId ";
        $db->exec( $sql );
        $sql = "UPDATE course_softwares SET CourseId = $this->id WHERE CourseId = $OldId ";
        $db->exec( $sql );
        $sql = "UPDATE course_textbooks SET CourseId = $this->id WHERE CourseId = $OldId ";
        $db->exec( $sql );
        $sql = "UPDATE program_courses SET CourseId = $this->id WHERE CourseId = $OldId";
        return TRUE;
    }

    /**
     * delete course - case study association
     * @param $CaseId int
     * @return int number of database rows affected by operation
     */
    public function unassignCaseStudy( $CaseId ) {
        $db = new EduDB();
        $sql = "DELETE course_cases WHERE CourseId = $this->id AND CaseId = :CaseId)";
        $params = array( array( ":CaseId", $CaseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * delete course - dataset association
     * @param $DatasetId int
     * @return int number of database rows affected by operation
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
     * @return int number of database rows affected by operation
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
     * @return int number of database rows affected by operation
     */
    public function unassignTextbook( $TextbookId ) {
        $db = new EduDB();
        $sql = "DELETE course_textbooks WHERE CourseId = $this->id AND TextbookId = :TextbookId)";
        $params = array( array( ":TextbookId", $TextbookId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }


    /**
     * Get list of books tied to this course
     */
    public function getBooks( $active = TRUE, $asObjects = FALSE ){
        $booksOut = [];
        $db = new EduDB();
        $sql = "SELECT TextbookId FROM course_textbooks WHERE CourseId = $this->id";
        if ($active !== null) $sql .= " AND Deleted = " . (($active == TRUE) ? "0" : "1");
        $books = $db->queryColumn( $sql );
        if($asObjects){
            foreach($books as $book){
                $booksOut[] = new Textbook($book);
            }
        }
        else {
            $booksOut = $books;
        }
        return $booksOut;
    }

    public function getSoftware( $active = TRUE, $asObjects = FALSE ){
        $softOut = [];
        $db = new EduDB();
        $sql = "SELECT SoftwareId FROM course_softwares WHERE CourseId = $this->id";
        if ($active !== null) $sql .= " AND Deleted = " . (($active == TRUE) ? "0" : "1");
        $softs = $db->queryColumn( $sql );
        if($asObjects){
            foreach($softs as $soft){
                $softOut[] = new Software($soft);
            }
        }
        else {
            $softOut = $softs;
        }
        return $softOut;
    }

    public function getDatasets( $active = TRUE, $asObjects = FALSE ){
        $dataOut = [];
        $db = new EduDB();
        $sql = "SELECT DatasetId FROM course_datasets WHERE CourseId = $this->id";
        if ($active !== null) $sql .= " AND Deleted = " . (($active == TRUE) ? "0" : "1");
        $sets = $db->queryColumn( $sql );
        if($asObjects){
            foreach($sets as $set){
                $dataOut[] = new Dataset($set);
            }
        }
        else {
            $dataOut = $sets;
        }
        return $dataOut;
    }

    public function getCases( $active = TRUE, $asObjects = FALSE ){
        $casesOut = [];
        $db = new EduDB();
        $sql = "SELECT CaseId FROM course_cases WHERE CourseId = $this->id";
        if ($active !== null) $sql .= " AND Deleted = " . (($active == TRUE) ? "0" : "1");
        $cases = $db->queryColumn( $sql );
        if($asObjects){
            foreach($cases as $case){
                $casesOut[] = new CaseStudy($case);
            }
        }
        else {
            $casesOut = $cases;
        }
        return $casesOut;
    }

    public static function getAllCourses( $active = TRUE, $asObjects = FALSE){
        $courses = [];
        $db = new EduDB();
        $sql = "SELECT * FROM courses";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $courseList = $db->query( $sql );
        if ($asObjects) {
            foreach( $courseList as $course) {
                $courses[] = new Course($course);
            }
        }
        else {
            $courses = $courseList;
        }

        return $courses;
    }
}