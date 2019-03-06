<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:23 PM
 */

class Program extends AOREducationObject
{
    public static $table = "programs";
    public static $primary_key = "ProgramId";
    public static $tableId = 19;
    public static $data_structure = array(
        'ProgramId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Program ID', 'editable' => FALSE ),
        'InstitutionId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Institution ID', 'editable' => FALSE ),
        'CollegeId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'College ID', 'editable' => TRUE ),
        'ContactId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Contact ID', 'editable' => FALSE ),
        'ProgramName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Program Name', 'editable' => TRUE ),
        'ProgramType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Program Type', 'editable' => TRUE ),
        'DeliveryMethod' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Delivery Method', 'editable' => TRUE ),
        'ProgramAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Program Website', 'editable' => TRUE ),
        'ProgramObjectives' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Program Objectives', 'editable' => TRUE ),
        'FullTimeDuration' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Full-Time Duration', 'editable' => TRUE ),
        'PartTimeDuration' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Part-Time Duration', 'editable' => TRUE ),
        'TestingRequirement' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Testing Requirement(s)', 'editable' => TRUE ),
        'OtherRequirement' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Other Requirement(s)', 'editable' => TRUE ),
        'Credits' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Credits', 'editable' => TRUE ),
        'YearEstablished' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Year Established', 'editable' => TRUE ),
        'Scholarship' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Scholarship', 'editable' => TRUE ),
        'EstimatedResidentTuition' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Estimated Resident Tuition', 'editable' => TRUE ),
        'EstimatedNonresidentTuition' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Estimated Non-Resident Tuition', 'editable' => TRUE ),
        'CostPerCredit' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Cost per Credit', 'editable' => TRUE ),
        'ORFlag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'OR Flag', 'editable' => TRUE ),
        'AnalyticsFlag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Analytics Flag', 'editable' => TRUE ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE )
    );

    public function getContact( $asObject = TRUE ) {
        $Contact = new Contact( $this->Attributes['ContactId'] );
        return $Contact;
    }

    public function getTextbooks( $active = TRUE, $asObjects = FALSE ) {
        $booksOut = [];
        $db = new EduDB();
        $sql = "SELECT t.* FROM textbooks t INNER JOIN course_textbooks ct ON t.TextbookId = ct.TextbookId INNER JOIN program_courses pc ON pc.CourseId = ct.CourseId WHERE pc.ProgramId = $this->id";
        if ($active !== null) $sql .= " AND t.Deleted = " . (($active == TRUE) ? "0" : "1");
        $books = $db->query( $sql );
        if ($asObjects) {
            foreach( $books as $book) {
                $booksOut[] = new Textbook($book);
            }
        }
        else {
            $booksOut = $books;
        }
        return $booksOut;
    }

    public function getCourses( $active = TRUE, $asObjects = FALSE ) {
        $coursesOut = [];
        $db = new EduDB();
        $sql = "SELECT CourseId FROM program_courses WHERE ProgramId = $this->id";
        if ($active !== null) $sql .= " AND Deleted = " . (($active == TRUE) ? "0" : "1");
        $courses = $db->queryColumn( $sql );
        if($asObjects){
            foreach($courses as $course){
                $coursesOut[] = new Course($course);
            }
        }
        else {
            $coursesOut = $courses;
        }
        return $coursesOut;
    }

    public static function getAllPrograms( $active = TRUE, $asObjects = FALSE ){
        $programs = [];
        $db = new EduDB();
        $sql = "SELECT * FROM programs";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $progs = $db->query( $sql );
        if ($asObjects) {
            foreach( $progs as $prog) {
                $programs[] = new Program($prog);
            }
        }
        else {
            $programs = $progs;
        }

        return $programs;
    }

    public static function getAnalyticsPrograms( $active = TRUE, $asObjects = FALSE ){
        $programs = [];
        $db = new EduDB();
        $sql = "SELECT * FROM programs";
        if ($active !== null) $sql .= " WHERE AnalyticsFlag = 1 AND Deleted = " . (($active == TRUE) ? "0" : "1");
        $progs = $db->query( $sql );
        if ($asObjects) {
            foreach( $progs as $prog) {
                $programs[] = new Program($prog);
            }
        }
        else {
            $programs = $progs;
        }

        return $programs;
    }

    public static function getORPrograms( $active = TRUE, $asObjects = FALSE ){
        $programs = [];
        $db = new EduDB();
        $sql = "SELECT * FROM programs";
        if ($active !== null) $sql .= " WHERE ORFlag = 1 AND Deleted = " . (($active == TRUE) ? "0" : "1");
        $progs = $db->query( $sql );
        if ($asObjects) {
            foreach( $progs as $prog) {
                $programs[] = new Program($prog);
            }
        }
        else {
            $programs = $progs;
        }

        return $programs;
    }

    public static function getEditorPrograms( $userId, $active = TRUE, $asObjects = FALSE ){
        $programs = [];
        $db = new EduDB();
        $subQuery = "SELECT InstitutionId FROM institution_admins WHERE UserId = $userId";
        $sql = "SELECT * FROM programs WHERE InstitutionId IN ($subQuery)";
        if ($active !== null) $sql .= " AND Deleted = " . (($active == TRUE) ? "0" : "1");
        if($userId == 1){
            $sql = "SELECT * FROM programs";
            if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        }
        $progs = $db->query( $sql );
        if ($asObjects) {
            foreach( $progs as $prog) {
                $programs[] = new Program($prog);
            }
        }
        else {
            $programs = $progs;
        }

        return $programs;
    }

    public static function getAllProgramsAndInstitutions( $active = TRUE, $asObjects = FALSE ){
        $programs = [];
        $db = new EduDB();
        $sql = "SELECT * FROM programs p JOIN institutions i on p.InstitutionId = i.InstitutionId";
        if ($active !== null) $sql .= " WHERE p.Deleted = " . (($active == TRUE) ? "0" : "1");
        $sql .= " ORDER BY p.ProgramName, i.InstitutionName";
        $progs = $db->query( $sql );
        if ($asObjects) {
            foreach( $progs as $prog) {
                $programs[] = new Program($prog);
            }
        }
        else {
            $programs = $progs;
        }

        return $programs;
    }

    public function hasContact() {
        $has = FALSE;
        if ($this->Attributes['ContactId']) {
            $Contact = new Contact( $this->Attributes['ContactId'] );
            if ($Contact->valid && !$Contact->Attributes['Deleted']) $has = TRUE;
        }
        return $has;
    }

    public function hasCourses() {
        $db = new EduDB;
        $sql = "SELECT CourseId FROM program_courses WHERE ProgramId = $this->id AND Deleted = 0";
        $Ids = $db->query( $sql );
        return (count($Ids)) ? TRUE : FALSE;
    }
}