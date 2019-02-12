<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:24 PM
 */

class Software extends AOREducationObject
{
    public static $table = "softwares";
    public static $primary_key = "SoftwareId";
    public static $data_structure = array(
        'SoftwareId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'SoftwareName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'SoftwarePublisher' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );

    public function assignToCourse( $CourseId ) {

    }

    public function unassignFromCourse( $CourseId ) {

    }
}