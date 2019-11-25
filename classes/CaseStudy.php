<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:13 PM
 */

class CaseStudy extends AOREducationObject {

    public static $table = "cases";
    public static $primary_key = "CaseId";
    public static $tableId = 4;
    public static $data_structure = array(
        'CaseId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Case Study ID', 'editable' => FALSE ),
        'CaseTitle' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR, 'label' => 'Case Study Title', 'editable' => TRUE ),
        'CaseType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Case Study Type', 'editable' => TRUE ),
        'CaseUseDescription' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Case Study Use Description', 'editable' => TRUE  ),
        'CaseAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Case Study Access', 'editable' => TRUE  ),
        'AnalyticTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Analytic Tag', 'editable' => TRUE  ),
        'BusinessTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Business Tag', 'editable' => TRUE  ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE  ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE  ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
        'LastModifiedDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Last Modified Date', 'editable' => FALSE ),
    );
    public static $full_text_columns = 'CaseTitle, CaseUseDescription, AnalyticTag, BusinessTag';
    public static $name_sql = 'CaseTitle';
    public static $parent_class = 'Course';
    public static $hidden_fields = ['OriginalRecordId'];

    /**
     * add course - case study association
     * @param $CourseId int
     * @return int number of database rows affected by operation
     */
    public function assignToCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_cases (CourseId, CaseId) VALUES (:CourseId, $this->id)";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * delete course - case study association
     * @param $CourseId int
     * @return int number of database rows affected by operation
     */
    public function unassignFromCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "DELETE FROM course_cases  WHERE CourseId = :CourseId AND CaseId = $this->id";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    public static function getCaseStudies( $active = TRUE, $asObjects = FALSE ){
        $casesOut = [];
        $db = new EduDB();
        $sql = "SELECT * FROM cases";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $cases = $db->query( $sql );
        if ($asObjects) {
            foreach( $cases as $case) {
                $casesOut[] = new CaseStudy($case);
            }
        }
        else {
            $casesOut = $cases;
        }

        return $casesOut;
    }

    public function getParent( $asObject = TRUE ) {
        $db = new EduDB;
        $sql = "SELECT CourseId FROM course_cases WHERE CaseId = $this->id";
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
            $data_html .= '<div class="data_label">' . CaseStudy::$data_structure[$key]['label'] . '</div>';
            $data_html .= '<div class="data_value' . $changed_class . '">' . $value . '</div>';
            $data_html .= '</div>';
        }
        return $data_html;
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE course_cases SET CaseId = $this->id WHERE CaseId = $OldId ";
        $db->exec( $sql );
        return TRUE;
    }
}