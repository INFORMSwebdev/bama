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
    public static $tableId = 14;
    public static $data_structure = array(
        'InstitutionId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Institution ID', 'editable' => FALSE  ),
        'InstitutionName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR, 'label' => 'Institution Name', 'editable' => TRUE  ),
        'InstitutionAddress' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Street Address', 'editable' => TRUE  ),
        'InstitutionCity' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'City', 'editable' => TRUE  ),
        'InstitutionState' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'State', 'editable' => TRUE  ),
        'InstitutionZip' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Postal Code', 'editable' => TRUE  ),
        //'InstitutionRegion' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Region', 'editable' => TRUE  ),
        'InstitutionPhone' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Phone', 'editable' => TRUE  ),
        'InstitutionEmail' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Email', 'editable' => TRUE  ),
        //'InstitutionAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Website', 'editable' => TRUE  ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Created', 'editable' => FALSE ),
        'LastModifiedDate' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Last Modified', 'editable' => FALSE ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT, 'label' => 'Deleted', 'editable' => FALSE  ),
        'Expired' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Expired', 'editable' => FALSE  ),
        'Token' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_STR, 'label' => 'Token', 'editable' => FALSE ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Status', 'editable' => FALSE ),
        'OriginalRecordId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Original Record ID', 'editable' => FALSE ),
        'RegionId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT, 'label' => 'Region', 'editable' => TRUE ),
    );
    public static $full_text_columns = 'InstitutionName, InstitutionCity';
    public static $name_sql = 'InstitutionName';
    public static $parent_class = NULL;
    public static $hidden_fields = ['OriginalRecordId', 'Token'];

    public function assignAdmin( $UserId ) {
        $db = new EduDB;
        $sql = "SELECT id FROM institution_admins WHERE InstitutionId = {$this->id} AND UserId = $UserId";
        $id = $db->queryItem( $sql );
        if ($id) return 1; // user is already assigned to this institution but we will pretend operation successful so there is no error feedback to admin
        $sql = "INSERT INTO institution_admins (InstitutionId, UserId) VALUES ($this->id, :UserId)";
        $params = array( array( ":UserId", $UserId, PDO::PARAM_INT));
        return $db->execSafe( $sql, $params );
    }

    public function getColleges( $asObjects = TRUE ) {
        $db = new EduDB();
        $sql = "SELECT CollegeId FROM colleges WHERE InstitutionId=$this->id AND ApprovalStatusId = ".APPROVAL_TYPE_APPROVE; ;
        $CollegeIds = $db->queryColumn( $sql );
        if ($asObjects) {
            $Colleges = [];
            foreach( $CollegeIds as $CollegeId ) $Colleges[] = new College( $CollegeId );
            return $Colleges;
        }
        else return $CollegeIds;
    }

    public function getContacts( $asObjects = TRUE, $ApprovalStatus = APPROVAL_TYPE_APPROVE ) {
        $Programs = $this->getPrograms( $asObjects, $ApprovalStatus );
        $Contacts = [];
        foreach( $Programs as $Program ) $Contacts[] = $Program->getContacts( $asObjects, $ApprovalStatus );
        $Contacts = array_unique( $Contacts, SORT_REGULAR );
        return $Contacts;
    }

    public function getCourses( $asObjects = TRUE ) {
        $Programs = $this->getPrograms();
        $Courses = [];
        foreach( $Programs as $Program ) {
            $ProgramCourses = $Program->getCourses( TRUE, TRUE);
            foreach( $ProgramCourses as $ProgramCourse) {
                if($asObjects) {
                    $Courses[] = $ProgramCourse;
                }
                $Courses[] = $ProgramCourse->Attributes;
            }
        }
        return $Courses;
    }

    public static function getInstitutionByToken ( $Token ) {
        $Institution = null;
        if (!$Token) throw new Exception("Missing required parameter: $Token" );
        else {
            $db = new EduDB;
            $sql = "SELECT InstitutionId FROM institutions WHERE Token = :Token ";
            $params = [[":Token", $Token, PDO::PARAM_STR]];
            $InstitutionId = $db->queryItemSafe( $sql, $params );
            if ($InstitutionId) {
                $Institution = new Institution( $InstitutionId );
                return $Institution;
            }
            else throw new Exception( "Token not valid" );
        }
        return $Institution;
    }

    public static function getInstitutions( $active = TRUE, $asObjects = FALSE, $ApprovalStatusID = 2) {
        $institutions = [];
        $db = new EduDB();
        $sql = "SELECT * FROM institutions";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        $insts = $db->query( $sql );
        if ($asObjects) {
            foreach( $insts as $inst) {
                $institutions[] = new Institution($inst);
            }
        }
        else {
            $institutions = $insts;
        }

        return $institutions;
    }

    public static function getInstitutionIds($active = TRUE){
        $db = new EduDB();
        $sql = "SELECT InstitutionId FROM institutions";
        if ($active !== null) $sql .= " WHERE Deleted = " . (($active == TRUE) ? "0" : "1");
        return $db->query( $sql );

    }

    public  function getInstructors( $asObjects = TRUE ) {
        $Courses = $this->getCourses();
        $Instructors = [];
        foreach( $Courses as $Course ) {
            $CourseInstructors = $Course->getInstructors();
            foreach( $CourseInstructors as $CourseInstructor ) {
                if ($asObjects) {
                    $Instructors[] = $CourseInstructor;
                }
                else {
                    $Instructors[] = $CourseInstructor->Attributes;
                }
            }
        }
        return $Instructors;
    }

    public function getName() {
        return $this->Attributes['InstitutionName'];
    }

    public static function getNameById( $id ) {
        $db = new EduDB;
        $sql = "SELECT InstitutionName FROM institutions WHERE InstitutionId = $id ";
        return $db->queryItem( $sql );
    }

    public function getPrograms( $asObjects = TRUE, $ApprovalStatusId = APPROVAL_TYPE_APPROVE ) {
        $db = new EduDB;
        $Programs = [];
        $sql = "SELECT ProgramId FROM programs WHERE InstitutionId=$this->id";
        if ($ApprovalStatusId) $sql .= " AND ApprovalStatusId = $ApprovalStatusId";
        $ProgramIds = $db->queryColumn( $sql );
        if ($asObjects) {
            $Programs = [];
            foreach( $ProgramIds as $ProgramId ) $Programs[] = new Program( $ProgramId );
            return $Programs;
        }
        else {
            return $ProgramIds;
        }
    }

    public function getRegionLabel() {
        $region_id = $this->Attributes['RegionId'];
        if (!$region_id) return '';
        $db = new EduDB;
        $sql = "SELECT `name` FROM region_dropdown WHERE id=$region_id";
        return $db->queryItem( $sql );
    }

    public function getUserAssignments( $asObjects = FALSE ) {
        $db = new EduDB();
        $sql = "SELECT UserID FROM institution_admins WHERE InstitutionId = $this->id";
        $users = $db->queryColumn( $sql );
        if ($asObjects)  {
            foreach( $users as &$user) $user = new User($user);
        }
        return $users;
    }

    public function hasColleges() {
        $db = new EduDB;
        $sql = "SELECT CollegeId FROM colleges WHERE InstitutionId = $this->id AND Deleted = 0";
        $Ids = $db->query( $sql );
        return (count($Ids)) ? TRUE : FALSE;
    }

    public function hasContacts( $ApprovalStatusId = APPROVAL_TYPE_APPROVE ) {
        $Contacts = $this->getContacts( FALSE, $ApprovalStatusId );
        return (count($Contacts)) ? TRUE : FALSE;
    }

    public function hasPrograms() {
        $db = new EduDB;
        $sql = "SELECT ProgramId FROM programs WHERE InstitutionId = $this->id AND Deleted = 0";
        $Ids = $db->query( $sql );
        return (count($Ids)) ? TRUE : FALSE;
    }

    public function renderObject( $changed_keys = [] ) {
        $data_html = "";
        foreach( $this->Attributes as $key => $value ) {
            if (in_array( $key, self::$hidden_fields)) continue;
            // logic to render value differently based on type goes here
            switch ( $key ) {
                case 'ApprovalStatusId':
                    $value = AOREducationObject::getStatusLabelFromId( $value );
                    break;
                case 'Deleted':
                case 'Expired':
                    $value = ($value) ? "Yes" : "No";
                    break;
                case 'RegionId':
                    $value = $this->getRegionLabel();
                    break;
            }

            if (!$value) $value = '&nbsp;';
            $changed_class = (in_array($key, $changed_keys)) ? ' changed' : '';
            $data_html .= '<div class="row data_row">';
            $data_html .= '<div class="data_label">' . self::$data_structure[$key]['label'] . '</div>';
            $data_html .= '<div class="data_value' . $changed_class . '">' . $value . '</div>';
            $data_html .= '</div>';
        }
        return $data_html;
    }

    public function sendExpirationNotice() {
      $recipients = [];

      // add to recipient list the Institution Email value if set and valid
      $inst_email = $this->Attributes['InstitutionEmail'];
      if (filter_var( $inst_email, FILTER_VALIDATE_EMAIL )) $recipients[] = $inst_email;

      // add to recipient list the contact email associated with any programs under this inst
      $Programs = $this->getPrograms();
      foreach( $Programs as $Program ) {
          if ($Program->Attributes['ContactId']) {
              $Contact = new Contact( $Program->Attributes['ContactId'] );
              if ($Contact->valid && filter_var( $Contact->Attributes['ContactEmail'], FILTER_VALIDATE_EMAIL )) $recipients[] = $Contact->Attributes['ContactEmail'];
          }
      }

      // add to recipient list any editors associated with this institution
      $Users = $this->getUserAssignments( TRUE );
      foreach ($Users as $User) {
          if (filter_var( $User->Attributes['Username'], FILTER_VALIDATE_EMAIL)) {
              $recipients[] = $User->Attributes['Username'];
          }
      }

      if (count($recipients)) {
          $Token = $this->Attributes['Token'];
          if (!$Token) {
              $salt = "Time is a great teacher, but unfortunately it kills all its pupils";
              $Token = md5( $salt . time() . $this->id );
              $this->update( 'Token', $Token );
          }
          $sameLink = WEB_ROOT . "/users/updateLastModified.php?Token=$Token";
          $editLink = WEB_ROOT . "/users/login.php";
          $e_params = [];
          $e_params['to'] = implode( ",", $recipients );
          $e_params['subject'] = "INFORMS Analytics & OR Education Database - Data Expiration Notice";
          $e_params['body_html'] = <<<EOT
<p>To help ensure the quality of the data in the INFORMS Analytics &amp; 
OR Education Database, we hope our institutional contacts will keep the data 
up to date. According to the information we have, data was last updated for 
{$this->Attributes['InstitutionName']} almost three years ago. You have three 
options:</p>
<ul>
<li>Click this link to verify that the data in our database is up to date: $sameLink</li>
<li>Click this link to log in and verify your institutional data and make updates 
where applicable: $editLink</li>
<li>If you do nothing within 30 days, we will de-list your institution in our database.</li>
</ul>
<p>Please refer any questions to <a href="mailto:onestopshop@mail.informs.org">onestopshop@mail.informs.org</a>.</p>
EOT;
          $email = new email( $e_params );
          $email->send();
          return TRUE;
      }
      else {
          return FALSE;
      }
    }

    public function swapID( $OldId ) {
        $db = new EduDB;
        $sql = "UPDATE institution_admins SET InstitutionId = $this->id WHERE InstitutionId = $OldId ";
        $db->exec( $sql );
        $sql = "UPDATE programs SET InstitutionId = $this->id WHERE InstitutionId = $OldId";
        $db->exec( $sql );
        return TRUE;
    }
}


