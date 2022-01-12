<?php

require_once('/common/classes/optionsHTML.php');

class Dropdowns
{

    public static function getCollegeTypeOptions($curSel = NULL){
        $db = new EduDB();
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
        if (!$id) return 'unknown';
        $db = new EduDB();
        $qry = 'SELECT name FROM college_type_dropdown WHERE id = ' . $id;
        return $db->queryColumn($qry)[0];
    }

    public static function getCountryOptionsHTML( $selected = 'USA' )
    {
        $db = new EduDB;
        $sql = "SELECT ISO_alpha3_Code, Country_or_Area FROM un_country_region_data ORDER BY Country_or_Area";
        return optionsHTML($db->query($sql), $selected, TRUE, [FALSE]);
    }

    public static function getProgramFullTimeDurationOptionsHTML($curSelection = NULL){
        $db = new EduDB();
        $qry = 'SELECT id, name FROM fulltime_program_duration_options';
        $types = $db->query($qry, PDO::FETCH_ASSOC);
        if($types){
            $optionHelper = array();
            foreach($types as $t){
                $optionHelper[] = array('text' => $t['name'], 'value' => $t['id']);
            }

            if(empty($curSelection)){
                return optionsHTML($optionHelper);
            } else {
                return optionsHTML($optionHelper, $curSelection, TRUE, array(TRUE, ''));
            }
        }
    }

    public static function getProgramPartTimeDurationOptionsHTML($curSelection = NULL){
        $db = new EduDB();
        $qry = 'SELECT id, name FROM parttime_program_duration_options';
        $types = $db->query($qry, PDO::FETCH_ASSOC);
        if($types){
            $optionHelper = array();
            foreach($types as $t){
                $optionHelper[] = array('text' => $t['name'], 'value' => $t['id']);
            }

            if(empty($curSelection)){
                return optionsHTML($optionHelper);
            } else {
                return optionsHTML($optionHelper, $curSelection, TRUE, array(TRUE, ''));
            }
        }
    }

    public static function getProgramTypeOptionsHTML($curSelection = NULL){
        $db = new EduDB();
        $qry = 'SELECT id, name FROM program_type_options';
        $types = $db->query($qry, PDO::FETCH_ASSOC);
        if($types){
            $optionHelper = array();
            foreach($types as $t){
                $optionHelper[] = array('text' => $t['name'], 'value' => $t['id']);
            }

            if(empty($curSelection)){
                return optionsHTML($optionHelper);
            } else {
                return optionsHTML($optionHelper, $curSelection, FALSE, array(TRUE, ''));
            }
        }
    }

    public static function getProgramTagOptionsHTML($curSelection = NULL){
        $db = new EduDB();
        $qry = 'SELECT id,name FROM program_tag_options ORDER BY `name`';
        $tags = $db->query($qry, PDO::FETCH_ASSOC);
        if($tags){
            //$optionHelper = array();
            $optionHelper = '';
            $ctr = 0;
            foreach($tags as $t){
                if(empty($curSelection)){
                    //$optionHelper[] = array('label' => $t['name'], 'value' => $t['id'], 'selected' => FALSE);
                    $optionHelper .= <<<EOT
<div class="col-auto">
<div class='form-check'>
    <input class="form-check-input programTag" type="checkbox" name="programTags[]" id="programTag{$ctr}" value="{$t['id']}">
    <label class="form-check-label" for="programTag{$ctr}">{$t['name']}</label>
</div>
</div>
EOT;
                } else {
                    if(is_array($curSelection)){
                        //see if this value is selected from the array
                        //there is an array of selected values
                        if(in_array($t['id'], $curSelection)){
                            //if the array contains the id, it is already selected
                            //$optionHelper[] = array('label' => $t['name'], 'value' => $t['id'], 'selected' => TRUE);
                            $optionHelper .= <<<EOT
<div class="col-auto">
<div class='form-check'>
    <input class="form-check-input programTag" type="checkbox" name="programTags[]" id="programTag{$ctr}" value="{$t['id']}" checked>
    <label class="form-check-label" for="programTag{$ctr}">{$t['name']}</label>
</div>
</div>
EOT;
                        } else {
                            //$optionHelper[] = array('label' => $t['name'], 'value' => $t['id'], 'selected' => FALSE);
                            $optionHelper .= <<<EOT
<div class="col-auto">
<div class='form-check'>
    <input class="form-check-input programTag" type="checkbox" name="programTags[]" id="programTag{$ctr}" value="{$t['id']}">
    <label class="form-check-label" for="programTag{$ctr}">{$t['name']}</label>
</div>
</div>
EOT;
                        }
                    } else {
                        //just 1 value selected, not array of selected values
                        if($t['id'] == $curSelection){
                            //$optionHelper[] = array('label' => $t['name'], 'value' => $t['id'], 'selected' => TRUE);
                            $optionHelper .= <<<EOT
<div class="col-auto">
<div class='form-check'>
    <input class="form-check-input programTag" type="checkbox" name="programTags[]" id="programTag{$ctr}" value="{$t['id']}" checked>
    <label class="form-check-label" for="programTag{$ctr}">{$t['name']}</label>
</div>
</div>
EOT;
                        } else {
                            //$optionHelper[] = array('label' => $t['name'], 'value' => $t['id'], 'selected' => FALSE);
                            $optionHelper .= <<<EOT
<div class="col-auto">
<div class='form-check'>
    <input class="form-check-input programTag" type="checkbox" name="programTags[]" id="programTag{$ctr}" value="{$t['id']}">
    <label class="form-check-label" for="programTag{$ctr}">{$t['name']}</label>
</div>
</div>
EOT;
                        }
                    }
                }
                $ctr++;
            }

            return $optionHelper;
        }
    }

    public static function getProgramTagOptionsSelectHTML($curSelection = NULL){
        $db = new EduDB();
        $qry = 'SELECT id,name FROM program_tag_options';
        $tags = $db->query($qry, PDO::FETCH_ASSOC);
        if($tags){
            $optionHelper = array();
            foreach($tags as $t){
                $optionHelper[] = array('text' => $t['name'], 'value' => $t['id']);
            }

            if(empty($curSelection)){
                return optionsHTML($optionHelper);
            } else {
                return optionsHTML($optionHelper, $curSelection, TRUE, array(TRUE, ''));
            }
        }
    }

    public static function getRegionOptionsHTML($curSelection = NULL){
        $db = new EduDB();
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