<?php


class un_data
{
  public static function getCountryName( $ISO3LetterCode = '') {
      $sql = "SELECT Country_or_Area FROM un_country_region_data WHERE ISO_alpha3_Code=:code";
      $params = [[':code', $ISO3LetterCode, PDO::PARAM_STR]];
      $db = new EduDB;
      return $db->queryItemSafe( $sql, $params );
  }
}