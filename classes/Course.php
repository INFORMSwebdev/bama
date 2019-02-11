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
    public static $data_structure = array(
        'CourseId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'InstructorId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT ),
        'CourseNumber' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CourseTitle' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DeliveryMethod' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'HasCapstoneProject' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'SyllabusFile' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_LOB ),
        'SyllabusFilesize' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT ),
        'AnalyticTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'BusinessTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );
}