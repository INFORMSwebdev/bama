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
        'DeliveryMethodId' => array('required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Delivery Method', 'editable' => TRUE),
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
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
        'LastModifiedDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Last Modified Date', 'editable' => FALSE ),
    );
    public static $full_text_columns = 'ProgramName, ProgramObjectives';
    public static $name_sql = 'ProgramName';

    public function assignContact( $ContactId ){
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO program_contacts (ProgramId, ContactId) VALUES ($this->id, :ContactId)";
        $params = array( array( ":ContactId", $ContactId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    public function assignCourse( $CourseId ){
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO program_courses (ProgramId, CourseId) VALUES ( $this->id, :CourseId)";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    public function getContact( $asObject = TRUE ) {
        $Contact = new Contact( $this->Attributes['ContactId'] );
        return $Contact;
    }

    public function getContacts( $asObjects = TRUE, $ApprovalStatusId = APPROVAL_TYPE_APPROVE ) {
        $db = new EduDB;
        $sql = <<<EOT
SELECT pc.ContactId
FROM program_contacts pc
JOIN contacts c ON pc.ContactId = c.ContactId
WHERE pc.Deleted = 0 AND c.Deleted = 0 AND ProgramId = $this->id 
EOT;
        if ($ApprovalStatusId) $sql .= " AND c.ApprovalStatusId = $ApprovalStatusId";
        $ContactIds = $db->queryColumn( $sql );
        if (!$asObjects) return $ContactIds;
        else {
            $Contacts = null;
            foreach($ContactIds as $ContactId ) $Contacts[] = new Contact($ContactId);
            return $Contacts;
        }
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

    public static function getAllPrograms( $active = TRUE, $asObjects = FALSE, $ApprovalStatusId=2 ){
        $programs = [];
        $db = new EduDB();
        $sql = "SELECT * FROM programs";
        $crit = '';
        if ($active !== null) $crit .= " Deleted = " . (($active == TRUE) ? "0" : "1");
        if ($ApprovalStatusId) $crit .= (($crit) ? " AND " : "") . " ApprovalStatusId = $ApprovalStatusId";
        if ($crit) $sql .= " WHERE " . $crit;
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

    public function getDeliveryMethod(){
        $db = new EduDB();

        if(empty($this->Attributes['DeliveryMethodId'])){
            return 'No Delivery Method set for this program.';
        }
        else {
            $sql = 'SELECT method FROM delivery_methods WHERE id=' . $this->Attributes['DeliveryMethodId'];
            //should only have the 1 delivery method
            $results = $db->queryColumn($sql);
            return $results[0];
        }
    }

    public function getDeliveryMethodOptions($first = NULL){
        $db = new EduDb();

        $sql = 'SELECT id, method FROM delivery_methods';
        $results = $db->query($sql);

        $helper = array();
        foreach($results as $r){
            $helper[] = array('value' => $r['id'], 'text' => $r['method']);
        }

        if(!empty($this->Attributes['DeliveryMethodId'])){
            //have the currently selected delivery method be currently selected, and we don't want an empty first option
            if(is_null($first)){
                return optionsHTML($helper, $this->Attributes['DeliveryMethodId'], TRUE, array(FALSE));
            }
            else {
                return optionsHTML($helper, $this->Attributes['DeliveryMethodId'], TRUE);
            }
        }
        else {
            //no current delivery method selected
            if(is_null($first)){
                return optionsHTML($helper, 10, TRUE, array(FALSE));
            }
            else {
                return optionsHTML($helper);
            }
        }
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

    public function getInstructors( $active = TRUE, $asObjects = FALSE){
        $instructors = [];
        $db = new EduDB();
        $sql = "SELECT i.InstructorId FROM instructors i INNER JOIN course_instructors ci on i.InstructorId = ci.InstructorId INNER JOIN program_courses pc on ci.CourseId = pc.CourseId WHERE pc.ProgramId = $this->id";
        if ($active !== null) $sql .= " AND i.Deleted = " . (($active == TRUE) ? "0" : "1");
        $insts = $db->queryColumn( $sql );
        if($asObjects){
            foreach($insts as $inst) {
                $instructors[] = new Instructor($inst);
            }
        }
        else {
            $instructors = $insts;
        }
        return $instructors;
    }

    public function getParent( $asObject = TRUE ) {
        if ($this->Attributes['CollegeId']) {
            if ($asObject) return new College( $this->Attributes['CollegeId']);
            else return $this->Attributes['CollegeId'];
        }
        else {
            if ($asObject) return new Institution( $this->Attributes['InstitutionId']);
            else return $this->Attributes['InstitutionId'];
        }
    }

    public function hasContact() {
        $has = FALSE;
        if ($this->Attributes['ContactId']) {
            $Contact = new Contact( $this->Attributes['ContactId'] );
            if ($Contact->valid && !$Contact->Attributes['Deleted']) $has = TRUE;
        }
        return $has;
    }

    public function hasContacts( $ApprovalStatusId = APPROVAL_TYPE_APPROVE ) {
        $Contacts = $this->getContacts( FALSE, $ApprovalStatusId );
        return (count($Contacts)) ? TRUE : FALSE;
    }

    public function hasCourses() {
        $db = new EduDB;
        $sql = "SELECT CourseId FROM program_courses WHERE ProgramId = $this->id AND Deleted = 0";
        $Ids = $db->query( $sql );
        return (count($Ids)) ? TRUE : FALSE;
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE program_contacts SET ProgramId = $this->id WHERE ProgramId = $OldId ";
        $db->exec( $sql );
        $sql = "UPDATE program_courses SET ProgramId = $this->id WHERE ProgramId = $OldId";
        $db->exec( $sql );
        return TRUE;
    }

    public function unassignAllContacts() {
        $db = new EduDB;
        $sql = "DELETE FROM program_contacts WHERE ProgramId = $this->id";
        return $db->exec( $sql );
    }

    public function unassignContact( $ContactId ) {

        $db = new EduDB();
        $sql = "DELETE FROM program_contacts WHERE ProgramId = $this->id AND ContactId = :ContactId";
        $params = array( array( ":ContactId", $ContactId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );

    }
}