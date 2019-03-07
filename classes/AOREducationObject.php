<?php

class AOREducationObject {

  public static $table;
  public static $primary_key;
  public $id;
  public $Attributes;
  public static $data_structure;
  public $valid = FALSE;

  public function __construct( $id = null) {
      if ($id) {
          $this->id = $id;
          $db = new EduDB();
          $sql = "SELECT * FROM ".static::$table." WHERE ".static::$primary_key."=:primary_key";
          $params = array( array( ":primary_key", $id, PDO::PARAM_INT ) );
          $row = $db->queryRowSafe( $sql, $params );
          foreach( $row as $key => $value ) $this->Attributes[$key] = $value;
          $this->valid = (count($row)) ? TRUE : FALSE;
      }
  }
  
  public static function clean_input_array( $input_array, $structure ) {
    $output_array = array();
    foreach( $input_array as $key => $value ) {
      if (in_array( $key, array_keys( $structure ))) $output_array[$key] = $value;
    }
    return $output_array;
  }

  public static function create( $params ) {
    $db = new EduDB();
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

  public static function createInstance( $params ) {
      $object = new static();
      $params = self::clean_input_array( $params, static::$data_structure );
      $object->Attributes = $params;
      $object->valid = TRUE;
      return $object;
  }

  public function createPendingUpdate( $updateTypeId, $UserId )
  {
      $class = get_class($this);
      $TableId = $class::$tableId;
      $db = new EduDB();
      $sql = "INSERT INTO pending_updates (UpdateTypeId, TableId, RecordId, UpdateContent, UserId ) VALUES (:UpdateTypeId, :TableId, :RecordId, :UpdateContent, :UserId)";
      $params = [];
      $params[] = array( ":UpdateTypeId", $updateTypeId, PDO::PARAM_INT );
      $params[] = array( ":TableId", $TableId, PDO::PARAM_INT );
      if ($updateTypeId == UPDATE_TYPE_INSERT) {
          $params[] = array( ":RecordId", null, PDO::PARAM_NULL );
      }
      else {
          $params[] = array( ":RecordId", $this->Attributes[static::$primary_key], PDO::PARAM_INT );
      }
      $params[] = array( ":UpdateContent", serialize($this->Attributes), PDO::PARAM_STR );
      $params[] = array( ":UserId", $UserId, PDO::PARAM_INT );
      $result = $db->execSafe( $sql, $params );
      if ($result) {
          $link = WEB_ROOT."admin/pendingUpdates.php";
          $details = '<div style="margin: 10px 0">';
          foreach( $this->Attributes as $key => $value ) {
              $details .= $key . ": " . filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS)."<br/>";
          }
          $details .= '</div>';
          $e_params = [];
          $e_params['to'] = ADMIN_EMAIL;
          $e_params['subject'] = "Analytics and Operations Research Education Database - Pending Update Request";
          $e_params['body_html'] = <<<EOT
<p>The Analytics &amp; OR Education Database system has received a new content update request.</p>
$details
<p>You can review this request at <a href="$link">$link</a>.</p>
EOT;
          $email = new email($e_params);
          $email->send();
      }
      return $result;
  }
  
  public function delete() {
    $db = new EduDB();
    $sql = "DELETE FROM ".static::$table." WHERE ".static::$primary_key."=$this->id";
    return $db->exec( $sql );
  }

    /**
     * write to application log at root_dir . log_dir
     * @param $text
     * @param string $filename
     */
    public static function log($text, $filename = "bama.log" ) {
      $ini = parse_ini_file( "/common/settings/common.ini", TRUE );
      $aes = $ini['analytics_education_settings'];
      $path = $aes['root_dir'] . $aes['log_dir'];
      $fh = fopen( $path . $filename, 'a' );
      fwrite( $fh, date('Y-m-d H:i:s') ." ================ ".PHP_EOL );
      fwrite( $fh, $text . PHP_EOL );
      fclose( $fh );
  }

  public function save() {
        if ($this->id) {
            $this->updateMultiple( $this->Attributes );
        }
        else {
            static::create( $this->Attributes );
        }
  }
  
  public function update( $key, $value ) {
    $db = new EduDB();
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