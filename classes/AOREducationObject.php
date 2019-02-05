<?php

class AOREducationObject {

  public static $table;
  public static $primary_key;
  public $id;
  public $Attributes;
  public static $data_structure;
  public $valid = FALSE;

  public function __construct( $id ) {
    $this->id = $id;
    $db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings" );
    $sql = "SELECT * FROM ".static::$table." WHERE ".static::$primary_key."=:primary_key";
    $params = array( array( ":primary_key", $id, PDO::PARAM_INT ) );
    $row = $db->queryRowSafe( $sql, $params );
    foreach( $row as $key => $value ) $this->Attributes[$key] = $value;
    $this->valid = (count($row)) ? TRUE : FALSE;
  }
  
  public static function clean_input_array( $input_array, $structure ) {
    $output_array = array();
    foreach( $input_array as $key => $value ) {
      if (in_array( $key, array_keys( $structure ))) $output_array[$key] = $value;
    }
    return $output_array;
  }

  public static function create( $params ) {
    $db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings" );
    $params = self::clean_input_array( $params, static::$data_structure );
    $keys = array_keys( $params );
    $sql = "INSERT INTO ".static::$table." (".implode(",", $keys).") VALUES (:".implode(",:", $keys).")";
    $qparams = array();
    foreach ( $params as $key => $value ) {
      $datatype = (is_null( $value )) ? PDO::PARAM_NULL : static::$data_structure[$key]['datatype'];
      $qparams[] = array( ":$key", $value, $datatype );
    }
    $result = $db->execSafe( $sql, $qparams );
    if (!$result) die( "Something went wrong" );
    $sql = "SELECT LAST_INSERT_ID()";
    return $db->queryItem( $sql );
  }
  
  public function delete() {
    $db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings" );
    $sql = "DELETE FROM ".static::$table." WHERE ".static::$primary_key."=$this->id";
    return $db->exec( $sql );
  }
  
  public function update( $key, $value ) {
    $db = new pdo_db( "/common/settings/common.ini", "analytics_education_settings" );
    $params = array();
    $sql = "UPDATE ".static::$table." SET $key=:value WHERE ".static::$primary_key ." = $this->id";
    $datatype = (is_null($value)) ? PDO::PARAM_NULL : static::$data_structure[$key]['datatype'];
    $params = array( array( ":value", $value, $datatype ) );
    $this->Attributes[$key] = $value;
    return $db->execSafe( $sql, $params );
  }
  
  public function updateMultiple( $params ) {
    foreach( $params as $key => $value ) {
      $this->update( $key, $value );
    }
  }

}

?>