<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:23 PM
 */

class Program extends AOREducationObject
{
    public static $table = "programs";
    public static $primary_key = "ProgramId";
    public static $data_structure = array(
        'ProgramId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'InstitutionId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'CollegeId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'ContactId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'ProgramName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'ProgramType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DeliveryMethod' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'ProgramAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'ProgramObjectives' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'FullTimeDuration' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'PartTimeDuration' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'TestingRequirement' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'OtherRequirement' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Credits' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'YearEstablished' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'Scholarship' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'EstimatedResidentTuition' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'EstimatedNonresidentTuition' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CostPerCredit' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'ORFlag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'AnalyticsFlag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );

    public function getCourses() {

    }

    public static function getAllPrograms( $active = TRUE, $asObjects = FALSE){
        $programs = [];
        $db = new EduDB();
        $sql = "SELECT * FROM programs";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $progs = $db->query( $sql );
        if ($asObjects) {
            foreach( $progs as $inst) {
                $programs[] = new Institution($inst);
            }
        }
        else {
            $programs = $progs;
        }

        return $programs;
    }
}