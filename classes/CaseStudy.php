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
    public function assignToCourse( $CourseId ) {

    }

    public function unassignFromCourse( $CourseId ) {

    }
}