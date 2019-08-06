<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:23 PM
 */

class Instructor extends AOREducationObject
{
    public static $table = "instructors";
    public static $primary_key = "InstructorId";
    public static $tableId = 15;
    public static $data_structure = array(
        'InstructorId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'InstructorId', 'editable' => FALSE ),
        'InstructorLastName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR, 'label' => 'Instructor Last Name', 'editable' => TRUE ),
        'InstructorFirstName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Instructor First Name', 'editable' => TRUE ),
        'InstructorPrefix' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Instructor Prefix', 'editable' => TRUE ),
        'InstructorEmail' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Instructor Email', 'editable' => TRUE ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
        'LastModifiedDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Last Modified Date', 'editable' => FALSE ),
    );
    public static $full_text_columns = 'InstructorLastName, InstructorFirstName, InstructorEmail';
    public static $name_sql = "CONCAT(InstructorFirstName,' ',InstructorLastName)";

    public function assignToCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_instructors (CourseId, InstructorId) VALUES (:CourseId, $this->id)";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    public static function getInstructors( $active = TRUE, $asObjects = FALSE ){
        $instructors = [];
        $db = new EduDB();
        $sql = "SELECT * FROM instructors";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $insts = $db->query( $sql );
        if ($asObjects) {
            foreach( $insts as $inst) {
                $instructors[] = new Instructor($inst);
            }
        }
        else {
            $instructors = $insts;
        }

        return $instructors;
    }

    public function getParent( $asObject = TRUE ) {
        $db = new EduDB;
        $sql = "SELECT CourseId FROM course_instructors WHERE InstructorId = $this->id";
        $CourseId = $db->queryItem( $sql );
        if ($asObject) return new Course( $CourseId );
        else return $CourseId;
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE course_instructors SET InstructorId = $this->id WHERE InstructorId = $OldId ";
        $db->exec( $sql );
        return TRUE;
    }
}