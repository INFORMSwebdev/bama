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
        'InstructorId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'InstructorLastName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'InstructorFirstName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstructorPrefix' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstructorEmail' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );
}