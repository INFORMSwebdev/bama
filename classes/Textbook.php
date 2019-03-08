<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:25 PM
 */

class Textbook extends AOREducationObject
{
    public static $table = "textbooks";
    public static $primary_key = "TextbookId";
    public static $tableId = 23;
    public static $data_structure = array(
        'TextbookId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Textbook ID', 'editable' => FALSE ),
        'TextbookName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR, 'label' => 'Textbook Title', 'editable' => TRUE ),
        'Authors' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Authors', 'editable' => TRUE ),
        'TextbookPublisher' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Publisher', 'editable' => TRUE ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
    );

    /**
     * add course - textbook association
     * @param $CourseId int
     * @return int number of database rows affected by operation
     */
    public function assignToCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO course_textbooks (CourseId, TextbookId) VALUES (:CourseId, $this->id)";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    /**
     * delete course - textbook association
     * @param $CourseId int
     * @return int number of database rows affected by operation
     */
    public function unassignFromCourse( $CourseId ) {
        $db = new EduDB();
        $sql = "DELETE FROM course_textbooks WHERE CourseId = :CourseId AND TextbookId = $this->id";
        $params = array( array( ":CourseId", $CourseId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    public static function getBooks( $active = TRUE, $asObjects = FALSE ){
        $books = [];
        $db = new EduDB();
        $sql = "SELECT * FROM textbooks";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $bookList = $db->query( $sql );
        if ($asObjects) {
            foreach( $bookList as $book) {
                $books[] = new Textbook($book);
            }
        }
        else {
            $books = $bookList;
        }

        return $books;
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE course_textbooks SET TextbookId = $this->id WHERE TextbookId = $OldId ";
        $db->exec( $sql );
        return TRUE;
    }
}