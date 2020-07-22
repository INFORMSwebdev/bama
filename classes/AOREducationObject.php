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
          foreach( $row as $key => $value ) if ($key != 'SyllabusFile') $this->Attributes[$key] = $value;
          $this->valid = (count($row)) ? TRUE : FALSE;
      }
  }
  
  public static function clean_input_array( $input_array, $structure ) {
    $output_array = array();
    foreach( $input_array as $key => $value ) {
        //if ($key == static::$primary_key) continue;
      if (in_array( $key, array_keys( $structure ))) $output_array[$key] = $value;
    }
    return $output_array;
  }


    /**
     * Compares two objects of same type and returns an array of keys where the values differ.
     * @param $o1
     * @param $o2
     * @return array
     */
    public static function compareObjects($o1, $o2 ) {
      $output = [];
      $class = get_class( $o1 );
      $class2 = get_class( $o2 );
      if ($class != $class2) die( "compareObjects failed because objects are of type $class and $class2 and need to be the same.");
      $ignored_fields = [ $class::$primary_key, 'ApprovalStatusId', 'CreateDate', 'LastModifiedDate'];
      /*foreach( $o1->Attributes as $key => $value ) {
          if (in_array( $key, $ignored_fields )) continue;
          elseif (!in_array( $key, array_keys($class::$data_structure))) continue;
          elseif (!in_array( $key, $o2->Attributes)) continue;
          else {
              if ($value != $o2->Attributes[$key]) $output[] = $key;
          }
      }*/
      //echo "<pre>".print_r($o1,1)."</pre>";
       // echo "<pre>".print_r($o2,1)."</pre>";die();
      foreach( $o2->Attributes as $key => $value ) {
          if (in_array( $key, $ignored_fields )) continue;
          if ($o1->Attributes[$key] != $value ) $output[] = $key;
      }
      return $output;
  }

  public static function create( $params ) {
      /* $primary_key = static::$primary_key;
      *$excluded_keys = [ $primary_key, 'CreateDate', 'LastModifiedDate'];
        $temp_params = $params;
       $params = [];
      foreach( $temp_params as $key => $value) {
           if (!in_array($key, $excluded_keys)) {
               $params[$key] = $value;
           }
       }*/
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
    if (!$result) die( "Something went wrong in static create method " . print_r(EduDB::$connection->errorInfo(),1) . $sql . print_r($params,1));
    $sql = "SELECT LAST_INSERT_ID()";
    return $db->queryItem( $sql );
  }

  public static function createInstance( $params ) {
      $object = new static();
      $params = self::clean_input_array( $params, static::$data_structure );
      $Class = get_called_class();
      $primary_key = $Class::$primary_key;
      if (isset($params[$primary_key])) $object->id = $params[$primary_key];
      $object->Attributes = $params;
      $object->valid = TRUE;
      return $object;
  }

  public function createPendingUpdate( $updateTypeId, $UserId )
  {
      $class = get_class($this);
      $TableId = $class::$tableId;
      $PendingUpdateId = PendingUpdate::create( ['UpdateTypeId'=>$updateTypeId, 'TableId'=>$TableId, 'UpdateContent'=>serialize($this->Attributes),'UserId'=>$UserId] );
      $PendingUpdate = new PendingUpdate( $PendingUpdateId );
      $PendingUpdate->update( 'RecordId', $this->id );
      //$this->Attributes['ApprovalStatusId'] = APPROVAL_TYPE_NEW;
      /*switch( $updateTypeId ) {
          case UPDATE_TYPE_INSERT:
              //$this->id = $this->save();
              //$PendingUpdate->update( 'UpdateRecordId', $this->id );
              break;
          case UPDATE_TYPE_UPDATE:
              //$OriginalRecordId = $this->id;
              $PendingUpdate->update( 'RecordId', $this->id );
              //$this->id = $this->Attributes[static::$primary_key] = null;
              //$this->id = $this->save(); // save current object attributes into new row
              //$this->update('OriginalRecordId', $OriginalRecordId);
              //$PendingUpdate->update( 'UpdateRecordId', $this->id );
              break;
          case UPDATE_TYPE_DELETE:
              $PendingUpdate->update( 'RecordId', $this->id );
              break;
      }*/


      $attr_details = '';
      switch( $updateTypeId ) {
          case UPDATE_TYPE_INSERT:
          case UPDATE_TYPE_DELETE:
              foreach( $this->Attributes as $key => $value ) {
                  $attr_details .= $key . ": " . filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS)."<br/>";
              }
              //$this->id = $this->Attributes[$class::$primary_key] = $this->save();
              //$PendingUpdate->update( 'UpdateRecordId', $this->id );
              break;
          case UPDATE_TYPE_UPDATE:
              $original = new $class( $this->id );
              $keys = self::compareObjects($original, $this);
              foreach( $keys as $key ) {
                  $attr_details .= "Current value for $key: ".$original->Attributes[$key] . "<br/>";
                  $attr_details .= "Updated value for $key: ". $this->Attributes[$key] . "<br /><br />";
              }
              break;
      }
      $link = WEB_ROOT."admin/pendingUpdates.php";
      $details = '<p>'.$this->getAncestry().'</p>';
      $details .= '<p>'.$this->Attributes[$class::$name_sql].'</p>';
      $details .= '<div style="margin: 10px 0">';
      $details .= $attr_details;
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

      return $PendingUpdateId;
  }
  
  public function delete() {
    $db = new EduDB();
    $sql = "DELETE FROM ".static::$table." WHERE ".static::$primary_key."=$this->id";
    return $db->exec( $sql );
  }

    function fixMSChars( $s ) {
        return  str_replace(
            [chr(145), chr(146), chr(147), chr(148), chr(150), chr(151), chr(133)],
            ["'", "'", '"', '"', '-', '--', '...'],
            $s );
    }

    public static function formatPhoneNumber($s) {
        $rx = "/
            (1)?\D*     # optional country code
            (\d{3})?\D* # optional area code
            (\d{3})\D*  # first three
            (\d{4})     # last four
            (?:\D+|$)   # extension delimiter or EOL
            (\d*)       # optional extension
        /x";
        preg_match($rx, $s, $matches);
        if(!isset($matches[0])) return false;

        $country = $matches[1];
        $area = $matches[2];
        $three = $matches[3];
        $four = $matches[4];
        $ext = $matches[5];

        $out = "$three-$four";
        if(!empty($area)) $out = "$area-$out";
        if(!empty($country)) $out = "+$country-$out";
        if(!empty($ext)) $out .= "x$ext";

        // check that no digits were truncated
        // if (preg_replace('/\D/', '', $s) != preg_replace('/\D/', '', $out)) return false;
        return $out;
    }

  public function getAncestry( $asString = TRUE ) {
      if (!method_exists($this, 'getParent')) return null;
      $str = '';
      $coll = [];
      $parent = $this;
      while($parent = $parent->getParent()) {
          $class = get_class($parent);
          if ($asString) {
              $str = '<ul><li>' . $class . ": <b>" . $parent->Attributes[$class::$name_sql] . '</b>' . $str . "</li></ul>";
          }
          else {
              $coll[] = $parent;
          }
          if ($class == "Institution" || !method_exists($parent, 'getParent')) break;
      }

      return $asString ? $str : $coll;
  }

  public static function getSubmissionMessage( $type = 'add', $thing = null ) {
        // the $thing parameter allows something arbitrary to be filled in to indicate the thing that was updated, in case we need that
        if (!$thing) $thing = get_called_class();
        $html = "";
        switch (strtolower($type)) {
            case 'add':
            case 'insert':
                $html .= "New " . strtolower( $thing );
                break;
            case 'edit':
            case 'update':
            default:
                $html .= $thing . " update";
                break;
        }
        $html .= " successfully submitted and is awaiting approval for posting. ";
        $html .= "Please allow 5-7 business days for updates to be approved.";
        return $html;
  }

  public function getStatusLabel() {
        $label = NULL;
        if( isset( $this->Attributes['ApprovalStatusId'])) $label = $this->Attributes['ApprovalStatusId'];
        return $label;
  }
  public static function getStatusLabelFromId( $id ) {
      $db = new EduDB;
      $sql = "SELECT StatusName FROM approval_status WHERE ApprovalStatusId = $id ";
      return $db->queryItem( $sql );
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

  public function renderObject( $changed_keys = [] ) {
        $data_html = "";
        $Class = get_class($this);
        foreach( $this->Attributes as $key => $value ) {
            // logic to render value differently based on type goes here

            if (!$value) $value = '&nbsp;';
            $changed_class = (in_array($key, $changed_keys)) ? ' changed' : '';
            $data_html .= '<div class="row data_row">';
            $data_html .= '<div class="data_label">' . $Class::$data_structure[$key]['label'] . '</div>';
            $data_html .= '<div class="data_value' . $changed_class . '">' . $value . '</div>';
            $data_html .= '</div>';
        }
        return $data_html;
  }

  public function save() {
        if ($this->id) {
            return $this->updateMultiple( $this->Attributes );
        }
        else {
            return static::create( $this->Attributes );
        }
  }
  
  public function update( $key, $value ) {
        if( !array_key_exists( $key, static::$data_structure )) return false;
        $Class = get_class($this);
        $ignored_fields = [ $Class::$primary_key, 'CreateDate', 'LastModifiedDate'];
        if (in_array( $key, $ignored_fields )) return FALSE;
        $db = new EduDB();
        $params = array();
        $sql = "UPDATE ".static::$table." SET $key=:value WHERE ".static::$primary_key ." = $this->id";
        $datatype = (is_null($value)) ? PDO::PARAM_NULL : static::$data_structure[$key]['datatype'];
        $params = array( array( ":value", $value, $datatype ) );
        $this->Attributes[$key] = $value;
        return $db->execSafe( $sql, $params );
  }
  
  public function updateMultiple( $params ) {
    $counter = 0;
    foreach( $params as $key => $value ) {
      $counter += $this->update( $key, $value );
    }
    return $counter;
  }

}

?>