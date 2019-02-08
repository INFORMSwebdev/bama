<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:08 PM
 */

class Institution extends AOREducationObject {
    public static $table = "institutions";
    public static $primary_key = "InstitutionId";

    public static function getInstitutions( $active = TRUE, $asObjects = TRUE) {
        $institutions = [];
        $db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings");
        $sql = "SELECT InstitutionId FROM institutions";
        $sql .= " WHERE Deleted = " . ($active == TRUE) ? "0" : "1";
        $insts = $db->queryColumn( $sql );
        foreach( $insts as $inst){
            if ($asObjects) {
                $institutions[] = new Institution($inst);
            }
            else {
                $institutions[] = $inst;
            }
        }
        return $institutions;
    }
}


