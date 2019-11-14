<?php

require_once('/common/classes/optionsHTML.php');

class Dropdowns
{

    public static function getCollegeTypeOptions($curSel = NULL){
        $db = new EduDb();
        $qry = 'SELECT id, name FROM college_type_dropdown';
        $cols = $db->query($qry);
        if($cols){
            $optionHelper = array();
            foreach($cols as $c){
                $optionHelper[] = array('text' => $c['name'], 'value' => $c['id']);
            }

            if(empty($curSel)){
                return optionsHTML($optionHelper);
            } else {
                return optionsHTML($optionHelper, $curSel, TRUE, array(TRUE, ''));
            }
        }
    }

    public static function getCollegeTypeName($id){
        $db = new EduDb();
        $qry = 'SELECT name FROM college_type_dropdown WHERE id = ' . $id;
        return $db->queryColumn($qry)[0];
    }

    public static function getInstitutionRegionName($id){
        $db = new EduDb();
        $qry = 'SELECT name FROM region_dropdown WHERE id = ' . $id;
        return $db->queryColumn($qry)[0];
    }

    public static function getRegionOptionsHTML($curSelection = NULL){
        $db = new EduDb();
        $qry = 'SELECT id,name FROM region_dropdown';
        $regions = $db->query($qry, PDO::FETCH_ASSOC);
        if($regions){
            $optionHelper = array();
            foreach($regions as $r){
                $optionHelper[] = array('text' => $r['name'], 'value' => $r['id']);
            }

            if(empty($curSelection)){
                return optionsHTML($optionHelper);
            } else {
                return optionsHTML($optionHelper, $curSelection, TRUE, array(TRUE, ''));
            }
        }
    }
}