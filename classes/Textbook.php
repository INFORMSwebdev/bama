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
    public static $data_structure = array(
        'TextbookId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'TextbookName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'Authors' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'TextbookPublisher' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );
}