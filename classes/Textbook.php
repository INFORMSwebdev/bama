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
        'TextbookId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'TextbookName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'Authors' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'TextbookPublisher' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
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
}