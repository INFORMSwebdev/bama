<?php


class un_data extends AOREducationObject
{
  public static function getCountryName( $ISO3LetterCode = '') {
      $sql = "SELECT Country_or_Area FROM un_country_region_data WHERE ISO_alpha3_Code=:code";
      $params = [[':code', $ISO3LetterCode, PDO::PARAM_STR]];
      $db = new EduDB;
      return $db->queryItemSafe( $sql, $params );
  }
  public static function getRegionName( $ISO3LetterCode = '') {
      if (!$ISO3LetterCode) return "unknown";
      $sql = "SELECT Region_Name, Intermediate_Region_Name FROM un_country_region_data WHERE ISO_alpha3_Code=:code";
      $params = [[':code', $ISO3LetterCode, PDO::PARAM_STR]];
      $db = new EduDB;
      $result = $db->querySafe( $sql, $params );//die( print_r($result,1));//die(json_encode($result));
      //$result = Array ( Array ( 'Region_Name' => 'Americas', 'Intermediate_Region_Name' => '') );
      $region = $result[0]['Region_Name'];
      if ($result[0]['Intermediate_Region_Name']) $region .= " > " . $result[0]['Intermediate_Region_Name'];
      return $region;
  }
}