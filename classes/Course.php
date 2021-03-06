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
        'DeliveryMethodId' => array('required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Course Delivery', 'editable' => TRUE),
        /* 'DeliveryMethod' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Delivery Method', 'editable' => TRUE  ),*/
        /*'HasCapstoneProject' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Has Capstone Project', 'editable' => TRUE  ),*/
        /* 'CourseText' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Course Text', 'editable' => TRUE ),*/
        'ProgrammingLanguage' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Software/Programming Language', 'editable'=>TRUE ),
        /*'SyllabusFile' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_LOB, 'label' => 'Syllabus File', 'editable' => TRUE  ),
        'SyllabusFilesize' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Syllabus File Size', 'editable' => TRUE  ),*/
        /*'AnalyticTag' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Analytic Tag', 'editable' => TRUE  ),
        'BusinessTag' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Business Tag', 'editable' => TRUE  ),*/
        'CreateDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE  ),
        'Deleted' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE  ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
        'LastModifiedDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Last Modified Date', 'editable' => FALSE ),
    );
    public static $full_text_columns = 'CourseTitle, CourseText';
    public static $name_sql = 'CourseTitle';
    public static $parent_class = 'Program';
    public static $hidden_fields = ['OriginalRecordId','InstructorId'];

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

    public function assignTag( $TagId ) {
        $tag_count = $this->countTags();
        $ini = parse_ini_file( "/common/settings/common.ini", TRUE );
        $aes = $ini['analytics_education_settings'];
        $max_tags = $aes['max_course_tags'];
        if ($tag_count >= $max_tags) {
            $success = 0;
            throw new Exception("This course already has reach the max tag count of $max_tags.");
        }
        else {
            $db = new EduDB();
            $sql = "INSERT IGNORE INTO course_tags (CourseId, TagId) VALUES ($this->id, $TagId )";
            $success = $db->exec( $sql );
        }
        return $success;
    }

    public function assignTags( $tags = [] ) {
        if (!count($tags)) return FALSE;
        $this->unassignAllTags(); // remove any pre-existing tag associations
        $success = FALSE;
        $db = new EduDB;
        for ($i = 0; $i < min(4, count($tags)); $i++) {
            $sql = "INSERT IGNORE INTO course_tags (CourseId, TagId) VALUE ($this->id, $tags[$i])";
            $success = $db->exec( $sql );
        }
        return $success;
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

    public function countTags() {
        $db = new EduDB();
        $sql = "SELECT COUNT(*) FROM course_tags WHERE CourseID = $this->id";
        $count = $db->queryItem( $sql );
        return $count;
    }

    public static function getCourseDeliveryMethods() {
        $db = new EduDB;
        $sql = "SELECT * FROM course_delivery_methods  ORDER BY name";
        return $db->query( $sql );
    }

    public function getDeliveryMethod(){
        $db = new EduDB();

        if(empty($this->Attributes['DeliveryMethodId'])){
            return 'No Delivery Method set for this program.';
        }
        else {
            $sql = 'SELECT `name` FROM course_delivery_methods WHERE id=' . $this->Attributes['DeliveryMethodId'];
            //should only have the 1 delivery method
            $results = $db->queryColumn($sql);
            return $results[0];
        }
    }

    public function getDeliveryMethodLabel() {
        $db = new EduDb;
        $sql = "SELECT `name` FROM course_delivery_methods WHERE id={$this->Attributes['DeliveryMethodId']}";
        return $db->queryItem( $sql );
    }

    public function getDeliveryMethodOptions($first = NULL){
        $db = new EduDB();

        $sql = 'SELECT id, `name` FROM course_delivery_methods';
        $results = $db->query($sql);

        $helper = array();
        foreach($results as $r){
            $helper[] = array('value' => $r['id'], 'text' => $r['name']);
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
                return optionsHTML($helper, 0, TRUE, array(FALSE));
            }
            else {
                return optionsHTML($helper);
            }
        }
    }

    public function getInstructor() {
        if (!$this->hasInstructor()) return FALSE;
        $Instructor = new Instructor( $this->Attributes['InstructorId']);
        return $Instructor;
    }

    public function getInstructorLabel() {
        $db = new EduDB;
        $sql = "SELECT InstructorPrefix, InstructorFirstName, InstructorLastName FROM instructors WHERE InstructorId = {this->Attributes['InstructorId']}";
        $instructor = $db->queryRow( $sql );
        return trim( implode(" ", $instructor));
    }

    public function getInstructors( $asObjects = FALSE ) {
        $db = new EduDB;
        $Instructors = [];
        $sql = "SELECT ci.InstructorId FROM course_instructors ci JOIN instructors i ON ci.InstructorId = i.InstructorId WHERE CourseId = $this->id AND i.ApprovalStatusId = 2";
        $InstructorIds = $db->query_column( $sql );
        foreach( $InstructorIds as $InstructorId) {
            $Instructor = new Instructor( $InstructorId );
            if ($asObjects) $Instructors[] = $Instructor;
            else $Instructors[] = $Instructor->Attributes;
        }

        return $Instructors;
    }

    public function getParent( $asObject = TRUE ) {
        $db = new EduDB;
        $sql = "SELECT ProgramId FROM program_courses WHERE CourseId = $this->id";
        $ProgramId = $db->queryItem( $sql );
        //die($sql);
        if ($asObject) return new Program( $ProgramId );
        else return $ProgramId;
    }

    public function getTagIds() {
        $tags = $this->getTags();
        $tagIds = [];
        foreach( $tags as $tag ) $tagIds[] = $tag['id'];
        return $tagIds;
    }

    public function getTags() {
        $db = new EduDB;
        $sql = "SELECT id, name FROM course_tags ct JOIN course_tag_options cto ON ct.TagId = cto.id WHERE ct.CourseId = $this->id";
        $tags = $db->query( $sql );
        return $tags;
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
                case 'DeliveryMethodId':
                    $value = $this->getDeliveryMethodLabel();
                    break;
                case 'InstructorId':
                    $value = $this->getInstructorLabel();
                    break;
            }

            if (!$value) $value = '&nbsp;';
            $changed_class = (in_array($key, $changed_keys)) ? ' changed' : '';
            $data_html .= '<div class="row data_row">';
            $data_html .= '<div class="data_label">' . Course::$data_structure[$key]['label'] . '</div>';
            $data_html .= '<div class="data_value' . $changed_class . '">' . $value . '</div>';
            $data_html .= '</div>';
        }
        return $data_html;
    }

    public static function renderTagHTML( $checked = [] ) {
        $db = new EduDB;
        $sql = "SELECT * FROM course_tag_options ORDER BY name";
        $tags = $db->query( $sql );
        $html = '<div class="tag_container">';
        foreach( $tags as $tag ) {
            $html .= '<div class="option_row"><input type="checkbox" class="courses_option" name="CourseTags[]" ';
            $checked_val = (in_array($tag['id'], $checked)) ? 'checked="checked" ' : '' ;
            $html .= $checked_val . 'value="'.$tag['id'].'"><span>'.$tag['name'].'</span></div>';
        }
        $html .= '</div>';
        return $html;
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
        $db->exec( $sql );
        return TRUE;
    }

    /**
     * removes all records from course_instructors
     * @return int number of database rows affected by operation
     */
    public function unassignAllInstructors(){
        $db = new EduDB();
        $sql = "DELETE FROM course_instructors WHERE CourseId = $this->id";
        return $db->exec( $sql );
    }

    public function unassignAllTags() {
        $db = new EduDB;
        $sql = "DELETE FROM course_tags WHERE CourseId = $this->id";
        return $db->exec( $sql );
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

    public function unassignTag( $TagId ) {
        $db = new EduDB;
        $sql = "DELETE FROM course_tags WHERE CourseId = $this->id AND TagId = $TagId";
        return $db->exec( $sql ); //note: if there was no such row, this will return 0
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
        $sql = "SELECT TextbookId FROM textbooks WHERE TextbookId IN (SELECT TextbookId FROM course_textbooks WHERE CourseId = $this->id) AND ApprovalStatusId = 2";
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

    public function getProgram( $asObject = FALSE ){
        $db = new EduDB();
        $sql = "SELECT ProgramId FROM program_courses WHERE CourseId = $this->id";
        $programId = $db->queryColumn( $sql );
        if($asObject){
            return new Program($programId);
        }
        else {
            return $programId;
        }
    }

    public function getSoftware( $active = TRUE, $asObjects = FALSE ){
        $softOut = [];
        $db = new EduDB();
        $sql = "SELECT a.SoftwareId FROM course_softwares a JOIN softwares b on a.SoftwareId = b.SoftwareId WHERE a.CourseId = $this->id and b.ApprovalStatusId = 2";
        if ($active !== null) $sql .= " AND b.Deleted = " . (($active == TRUE) ? "0" : "1");
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
        $sql = "SELECT a.DatasetId FROM course_datasets a JOIN datasets b on a.DatasetId = b.DatasetId WHERE a.CourseId = $this->id and b.ApprovalStatusId = 2";
        if ($active !== null) $sql .= " AND b.Deleted = " . (($active == TRUE) ? "0" : "1");
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
        $sql = "SELECT a.CaseId FROM course_cases a JOIN cases b ON a.CaseId = b.CaseId WHERE a.CourseId = $this->id and b.ApprovalStatusId = 2";
        if ($active !== null) $sql .= " AND b.Deleted = " . (($active == TRUE) ? "0" : "1");
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