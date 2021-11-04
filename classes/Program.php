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
        /*'ContactId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Contact ID', 'editable' => FALSE ),*/
        'ProgramName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Program Name', 'editable' => TRUE ),
        /*'ProgramType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Program Type', 'editable' => TRUE ),*/
        'ProgramTypeId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Program Type', 'editable' => TRUE ),
        /*'DeliveryMethod' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Delivery Method', 'editable' => TRUE ),*/
        'DeliveryMethodId' => array('required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Delivery Method', 'editable' => TRUE),
        'ProgramAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Program Website', 'editable' => TRUE ),
        'ProgramObjectives' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Program Objectives', 'editable' => TRUE ),
        /*'FullTimeDuration' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Full-Time Duration', 'editable' => TRUE ),*/
        /*'FullTimeDurationId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Full-Time Duration', 'editable' => TRUE ),*/
        'FullTimeDurationInt' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Full-Time Duration', 'editable' => TRUE ),
        /*'PartTimeDuration' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Part-Time Duration', 'editable' => TRUE ),*/
        /*'PartTimeDurationId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Part-Time Duration', 'editable' => TRUE ),*/
        'PartTimeDurationInt' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Part-Time Duration', 'editable' => TRUE ),
        /*'TestingRequirement' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Testing Requirement(s)', 'editable' => TRUE ),*/
        'OtherRequirement' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Other Requirement(s)', 'editable' => TRUE ),
        'Credits' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Credit Hours', 'editable' => TRUE ),
        'YearEstablished' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Year Established', 'editable' => TRUE ),
        'Scholarship' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Financial Assistance', 'editable' => TRUE ),
        'EstimatedResidentTuition' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Estimated Resident Tuition', 'editable' => TRUE ),
        'EstimatedNonresidentTuition' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Estimated Non-Resident Tuition', 'editable' => TRUE ),
        /*'CostPerCredit' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Cost per Credit', 'editable' => TRUE ),*/
        'Waiver' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Waiver', 'editable' => TRUE ),
       /* 'ORFlag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'OR Flag', 'editable' => TRUE ),*/
       /* 'AnalyticsFlag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Analytics Flag', 'editable' => TRUE ),*/
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
        'LastModifiedDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Last Modified Date', 'editable' => FALSE ),
    );
    public static $full_text_columns = 'ProgramName, ProgramObjectives';
    public static $name_sql = 'ProgramName';
    public static $parent_class = 'Institution';
    public static $hidden_fields = ['OriginalRecordId'];

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

    public function assignTag( $TagId ) {
        $tag_count = $this->countTags();
        $ini = parse_ini_file( "/common/settings/common.ini", TRUE );
        $aes = $ini['analytics_education_settings'];
        $max_tags = $aes['max_program_tags'];
        if ($tag_count >= $max_tags) {
            $success = 0;
            throw new Exception("This program already has reach the max tag count of $max_tags.");
        }
        else {
            $db = new EduDB();
            $sql = "INSERT IGNORE INTO program_tags (ProgramId, TagId) VALUES ($this->id, $TagId )";
            $success = $db->exec( $sql );
        }
        return $success;
    }

    public function assignTags( $tagIDs ) {
        if (!count($tagIDs)) return FALSE;
        $this->unassignAllTags(); // remove any pre-existing tag associations
        $success = FALSE;
        $db = new EduDB;
        for ($i = 0; $i < min(3, count($tagIDs)); $i++) {
            $sql = "INSERT IGNORE INTO program_tags (ProgramId, TagId) VALUE ($this->id, $tagIDs[$i])";
            $success = $db->exec( $sql );
        }
        return $success;
    }

    public function assignTestingRequirement( $reqID ) {
        $db = new EduDB;
        $sql = "INSERT IGNORE INTO program_testing_requirements (program_id, requirement_id ) VALUES( $this->id, $reqID )";
        return $db->exec( $sql );
    }

    public function assignTestingRequirements( $reqIDs ) {
        if (!count($reqIDs)) return FALSE;
        $this->unassignAllTestingRequirements();
        foreach( $reqIDs as $reqID ) $this->assignTestingRequirement( $reqID );
        return TRUE;
    }

    public function countTags() {
        $db = new EduDB();
        $sql = "SELECT COUNT(*) FROM program_tags WHERE ProgramID = $this->id";
        $count = $db->queryItem( $sql );
        return $count;
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

    public function getCourses( $active = TRUE, $asObjects = FALSE ) {
        $coursesOut = [];
        $db = new EduDB();
        $sql = "SELECT a.CourseId FROM program_courses a join courses b on a.CourseId = b.CourseId WHERE a.ProgramId = $this->id and b.ApprovalStatusId = 2";
        if ($active !== null) $sql .= " AND b.Deleted = " . (($active == TRUE) ? "0" : "1");
        $courses = $db->queryColumn( $sql );
        if($asObjects){
            foreach($courses as $course){
                $coursesOut[] = new Course( $course );
            }
        }
        else {
            $coursesOut = $courses;
        }
        return $coursesOut;
    }

    public function getDeliveryMethod(){
        $db = new EduDB();

        if(empty($this->Attributes['DeliveryMethodId'])){
            return 'No Delivery Method set for this program.';
        }
        else {
            $sql = 'SELECT method FROM delivery_methods WHERE id=' . $this->Attributes['DeliveryMethodId'];
            //should only have the 1 delivery method
            $result = $db->queryItem($sql);
            return $result;
        }
    }

    public function getDeliveryMethodOptions($first = NULL){
        $db = new EduDB();

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

    public function getFullTimeDurationLabel() {
        $db = new EduDB;
        $sql = "SELECT FullTimeDurationInt FROM programs WHERE ProgramId = {$this->id}";
        $val = $db->queryItem( $sql );
        $label = "";
        if ($val > 0) $label = $val . " months";
        return $label;
    }

    public static function getFullTimeDurationOptionHTML( $selected = NULL ) {
        $html = '';
        $durations = self::getFullTimeDurations();
        foreach( $durations as $duration) {
            $selVal = ($duration['id'] == $selected) ? ' selected="selected" ' : '';
            $html .= '<option value="'.$duration['id'].'" '.$selVal.'>'.$duration['name'].'</option>';
        }
        return $html;
    }

    public static function getFullTimeDurations() {
        $db = new EduDB;
        $sql = "SELECT * FROM fulltime_program_duration_options ORDER BY id";
        return $db->query( $sql );
    }

    public function getInstructors( $active = TRUE, $asObjects = FALSE){
        $instructors = [];
        $db = new EduDB();
        $sql = "SELECT i.InstructorId FROM instructors i INNER JOIN course_instructors ci on i.InstructorId = ci.InstructorId INNER JOIN program_courses pc on ci.CourseId = pc.CourseId WHERE pc.ProgramId = $this->id AND i.ApprovalStatusId=2";
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

    public function getFullTimeDuration(){
        $db = new EduDB();
        $sql = 'SELECT FullTimeDurationId FROM programs WHERE ProgramId = ' . $this->Attributes['ProgramId'];
        return $db->queryItem($sql);
    }

    public function getPartTimeDurationLabel() {
        $db = new EduDB;
        $sql = "SELECT PartTimeDurationInt FROM programs WHERE ProgramId = {$this->id}";
        $val = $db->queryItem( $sql );
        $label = "";
        if ($val > 0) $label = $val . " months";
        return $label;
    }

    public function getPartTimeDuration(){
        $db = new EduDB();
        $sql = 'SELECT PartTimeDurationId FROM programs WHERE ProgramId = ' . $this->Attributes['ProgramId'];
        return $db->queryItem($sql);
    }

    public static function getPartTimeDurationOptionHTML( $selected = NULL ) {
        $html = '';
        $durations = self::getPartTimeDurations();
        foreach( $durations as $duration) {
            $selVal = ($duration['id'] == $selected) ? ' selected="selected" ' : '';
            $html .= '<option value="'.$duration['id'].'" '.$selVal.'>'.$duration['name'].'</option>';
        }
        return $html;
    }

    public static function getPartTimeDurations() {
        $db = new EduDB;
        $sql = "SELECT * FROM parttime_program_duration_options ORDER BY id";
        return $db->query( $sql );
    }

    public function getProgramTypeLabel() {
        $db = new EduDB;
        $sql = "SELECT `name` FROM program_type_options WHERE id = " . $this->Attributes['ProgramTypeId'];
        return $db->queryItem( $sql );
    }

    public static function getProgramTypes() {
        // ToDo: update this?
        $db = new EduDB;
        $sql = "SELECT DISTINCT ProgramType FROM programs WHERE ProgramType > '' AND ApprovalStatusId = " . APPROVAL_TYPE_APPROVE . ' ORDER BY ProgramType';
        return $db->queryColumn( $sql );
    }

    public function getTagLabels() {
        $db = new EduDB;
        $sql = "SELECT pto.`name` FROM program_tags pt JOIN program_tag_options pto ON pt.TagId = pto.id WHERE pt.ProgramId = $this->id order by `name`";
        $tags = $db->queryColumn( $sql );
        return $tags;
    }

    public function getTags( $justIDs = TRUE ) {
        $db = new EduDB;
        $sql = "SELECT TagId, name FROM program_tags pt JOIN program_tag_options pto ON pt.TagId = pto.id WHERE pt.ProgramId = $this->id ORDER BY name";
        $tags = $db->query( $sql );
        if ($justIDs) {
            $ids = [];
            foreach( $tags as $tag ) $ids[] = $tag['TagId'];
            $tags = $ids;
        }
        return $tags;
    }

    public function getTestingRequirements( $labelsOnly = FALSE ) {
        $db = new EduDB;
        $sql = <<<EOT
SELECT ptro.id, ptro.`name` 
FROM programs 
JOIN program_testing_requirements prt ON prt.program_id = programs.ProgramId 
JOIN program_testing_requirement_options ptro ON ptro.id = prt.requirement_id 
WHERE programs.ProgramId = $this->id
EOT;
        $rows = $db->query( $sql );
        if ($labelsOnly) {
            $result = [];
            foreach( $rows as $row ) $result[] = $row['name'];
        }
        else $result = $rows;
        return $result;
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

    public function getType(){
        $db = new EduDB;
        $sql = 'SELECT `name` FROM program_type_options WHERE id = ' . $this->Attributes['ProgramTypeId'];
        return $db->queryItem( $sql );
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
                    $value = $this->getDeliveryMethod();
                    break;
                case 'FullTimeDurationId':
                    $value = $this->getFullTimeDurationLabel();
                    break;
                case 'PartTimeDurationId':
                    $value = $this->getPartTimeDurationLabel();
                    break;
                case 'ProgramTypeId':
                    $value = $this->getProgramTypeLabel();
                    break;
                case 'Waiver':
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

    public static function renderProgramTypeOptionHTML( $selected = null ) {
        $db = new EduDB;
        $sql = "SELECT id, name FROM program_type_options ORDER BY name";
        $types = $db->query( $sql );
        $html = '';
        foreach( $types as $type) {
            $selVal = ($type['id'] == $selected ) ? ' selected="selected" ' : '';
            $html .= '<option value="'.$type['id'].'"'.$selVal.'>'.$type['name'].'</option>';
        }
        return $html;
    }

    public static function renderProgramTypesOptions( $selected = null ) {
        $types = self::getProgramTypes();
        $html = '';
        foreach( $types as $type ) {
            $selectedVal = ($selected == $type) ? ' selected="selected" ' : '';
            $html .= '<option value="'.$type.'"'.$selectedVal.'>'.$type.'</option>';
        }
        return $html;
    }

    public static function renderTagHTML( $checked = [] ) {
        $db = new EduDB;
        $sql = "SELECT * FROM program_tag_options ORDER BY name";
        $tags = $db->query( $sql );
        $html = '<div class="tag_container">';
        foreach( $tags as $tag ) {
            $html .= '<div class="option_row"><input type="checkbox" class="programs_option" name="ProgramTags[]" ';
            $checked_val = (in_array($tag['id'], $checked)) ? 'checked="checked" ' : '' ;
            $html .= $checked_val . 'value="'.$tag['id'].'"><span>'.$tag['name'].'</span></div>';
        }
        $html .= '</div>';
        return $html;
    }

    public static function renderTestingRequirementsHTML( $checked = [] ) {
        $db = new EduDB;
        $sql = "SELECT * FROM program_testing_requirement_options ORDER BY IF(name='Other',1,0),name";
        $tags = $db->query( $sql );
        $html = '<div class="tag_container">';
        foreach( $tags as $tag ) {
            $html .= '<div class="option_row"><input type="checkbox" class="reqs_option" name="TestingRequirements[]" ';
            $checked_val = (in_array($tag['id'], $checked)) ? 'checked="checked" ' : '' ;
            $html .= $checked_val . 'value="'.$tag['id'].'"><span>'.$tag['name'].'</span></div>';
        }
        $html .= '</div>';
        return $html;
    }

    public static function renderReadOnlyTestingRequirementsHTML( $checked = [] ) {
        $db = new EduDB;
        $sql = "SELECT * FROM program_testing_requirement_options ORDER BY name";
        $tags = $db->query( $sql );
        $html = '<div class="tag_container">';
        foreach( $tags as $tag ) {
            $html .= '<div class="option_row"><input type="checkbox" class="reqs_option" name="TestingRequirements[]" ';
            $checked_val = (in_array($tag['id'], $checked)) ? 'checked="checked" ' : '' ;
            $html .= $checked_val . 'value="'.$tag['id'].'" disabled><span>'.$tag['name'].'</span></div>';
        }
        $html .= '</div>';
        return $html;
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

    public function unassignAllTags() {
        $db = new EduDB;
        $sql = "DELETE FROM program_tags WHERE ProgramId = $this->id";
        return $db->exec( $sql );
    }

    public function unassignAllTestingRequirements() {
        $db = new EduDB;
        $sql = "DELETE FROM program_testing_requirements WHERE program_id = $this->id";
        return $db->exec( $sql );
    }

    public function unassignContact( $ContactId ) {

        $db = new EduDB();
        $sql = "DELETE FROM program_contacts WHERE ProgramId = $this->id AND ContactId = :ContactId";
        $params = array( array( ":ContactId", $ContactId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );

    }

    public function unassignTag( $TagId ) {
        $db = new EduDB;
        $sql = "DELETE FROM program_tags WHERE ProgramyeajId = $this->id AND TagId = $TagId";
        return $db->exec( $sql ); //note: if there was no such row, this will return 0
    }
}