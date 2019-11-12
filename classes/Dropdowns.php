<?php

require_once('/common/classes/optionsHTML.php');

class Dropdowns
{
    public static function getInstitutionRegionName($id){
        $db = new EduDb();
        $qry = 'SELECT name FROM region_dropdown WHERE id = ' . $id;
        return $db->queryColumn($qry);
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