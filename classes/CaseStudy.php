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
        'CaseId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'CaseTitle' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'CaseType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CaseUseDescription' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CaseAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'AnalyticTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'BusinessTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );

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
}