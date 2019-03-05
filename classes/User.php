<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/11/2019
 * Time: 3:20 PM
 */

class User extends AOREducationObject
{
    public static $table = "users";
    public static $primary_key = "UserId";
    public static $tableId = 25;
    public static $data_structure = array(
        'UserId' => array('required' => TRUE, 'datatype' => PDO::PARAM_INT),
        'Username' => array('required' => TRUE, 'datatype' => PDO::PARAM_STR),
        'Password' => array('required' => FALSE, 'datatype' => PDO::PARAM_STR),
        'FirstName' => array('required' => FALSE, 'datatype' => PDO::PARAM_STR),
        'LastName' => array('required' => FALSE, 'datatype' => PDO::PARAM_STR),
        'Comments' => array('required' => FALSE, 'datatype' => PDO::PARAM_STR),
        'Token' => array('required' => FALSE, 'datatype' => PDO::PARAM_STR),
        'CreateDate' => array('required' => TRUE, 'datatype' => PDO::PARAM_STR),
        'Deleted' => array('required' => TRUE, 'datatype' => PDO::PARAM_INT)
    );

    /**
     * Add record to institution_admins table for current user and specified inst
     * @param $InstitutionId    int
     * @return int (1 on success, 0 on failure)
     */
    public function assignToInstitution($InstitutionId)
    {
        $db = new EduDB();
        $sql = "INSERT IGNORE INTO institution_admins (InstitutionId, UserId) VALUES (:InstitutionId, {$this->id})";
        $params = array(array(":InstitutionId", $InstitutionId, PDO::PARAM_INT));
        $result = $db->execSafe($sql, $params);
        return $result;
    }

    public function checkActiveStatus(){
        if($this->Attributes['Deleted'] == false){
            //the flag is set to 0, this is an active account (i.e. Deleted = false)
            return true;
        }
        else {
            //the flag is set to 1, this is an inactive account (i.e. Deleted = true)
            return false;
        }
    }

    public function checkPassword( $password ) {
        $password_hash = $this->Attributes['Password'];
        return password_verify( $password, $password_hash);
    }

    /**
     * @return mixed
     */
    public function generateToken()
    {
        $salt = "I wasn't originally going to get a brain transplant, but then I changed my mind.";
        $this->Attributes['Token'] = md5($salt . $this->id . time());
        return $this->update('Token', $this->Attributes['Token']);
    }

    public function getProgramAssignments( $asObjects = FALSE ) {
        $db = new EduDB();
        $sql = "SELECT ProgramId FROM programs WHERE InstitutionId IN 
          (SELECT InstitutionId FROM institution_admins WHERE UserId = $this->id)";
        $progs = $db->queryColumn( $sql );
        if($asObjects) {
            foreach( $progs as &$prog ) $prog = new Program($prog);
        }
        return $progs;
    }

    public function getInstitutionAssignments( $asObjects = FALSE ) {
        $db = new EduDB();
        $sql = "SELECT InstitutionId FROM institution_admins WHERE UserId = $this->id";
        $insts = $db->queryColumn( $sql );
        if ($asObjects)  {
            foreach( $insts as &$inst) $inst = new Institution($inst);
        }
        return $insts;
    }

    public static function getUserByEmail($Email, $asObject = TRUE)
    {
        $db = new EduDB();
        $sql = "SELECT UserId FROM users WHERE Username=:Email";
        $params = array(array(":Email", $Email, PDO::PARAM_STR));
        $UserId = $db->queryItemSafe($sql, $params);
        if (!$UserId) return FALSE;
        elseif ($asObject) return new User($UserId);
        else return $UserId;
    }

    /**
     * @param $Token
     * @param bool $asObject
     * @return User
     */
    public static function getUserByToken($Token, $asObject = TRUE)
    {
        $db = new EduDB();
        $sql = "SELECT UserId FROM users WHERE Token=:Token";
        $params = array(array(":Token", $Token, PDO::PARAM_STR));
        $UserId = $db->queryItemSafe($sql, $params);
        if ($asObject) return new User($UserId);
        else return $UserId;
    }

    public function resetPassword() {
        $this->generateToken();
        $this->sendPasswordResetEmail();
        return true;
    }

    public function sendInviteEmail()
    {
        $email = $this->Attributes['Username'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->generateToken();
            $path = WEB_ROOT . 'users/setPassword.php?token=' . $this->Attributes['Token'];
            $link = '<a href="'.$path.'">'.$path.'</a>';
            $msg = <<<EOT
<p>Welcome to the INFORMS Analytics &amp; OR Education Database management system. We have 
created an account for you.</p>
<p>Your username: {$this->Attributes['Username']}</p>
<p>Please click this link to set a password:</p>
<p>$link</p>
<p>Note: If you are using a plain-text email reader, you will need to manually copy and paste the full URL into a web browser.</p>
EOT;
            $e_params['to'] = $this->Attributes['Username'];
            $e_params['subject'] = "Analytics and Operations Research Education Database - Welcome";
            $e_params['body_html'] = $msg;
            $email = new email( $e_params );
            $success = $email->send();
            return $success;
        } else {
            static::log("Bad email address: $email");
        }
        return false;
    }

    public function sendPasswordResetEmail()
    {
        $path = WEB_ROOT . 'users/setPassword.php?token=' . $this->Attributes['Token'];
        $link = '<a href="'.$path.'">'.$path.'</a>';
        $msg = <<<EOT
<p>Please click this link to set a new password:</p>
<p>$link</p>
<p>Note: If you are using a plain-text email reader, you will need to manually copy and paste the full URL into a web browser.</p>
EOT;
        $e_params['to'] = $this->Attributes['Username'];
        $e_params['subject'] = "Analytics and Operations Research Education Database - Password Reset";
        $e_params['body_html'] = $msg;
        $email = new email( $e_params );
        $email->send();
    }

    public function unassignFromInstitution($InstitutionId)
    {
        $db = new EduDB();
        $sql = "DELETE FROM institution_admins WHERE InstitutionId = :InstitutionId AND UserId = $this->id";
        $params = array(array(":InstitutionId", $InstitutionId, PDO::PARAM_INT));
        return $db->execSafe($sql, $params);
    }

    /**
     * check if given username already exists, optionally specify a UserId to exclude from the search
     * @param $username string (email)
     * $param $excludeUserId    int optional
     * @return int (UserId) for match or null for no match
     */
    public static function usernameExists($username, $excludeUserId = null)
    {
        $db = new EduDB();
        $sql = "SELECT UserId FROM users WHERE Username=:Username";
        $params = array(array(':Username', $username, PDO::PARAM_STR));
        if ($excludeUserId) {
            $sql .= " AND UserId != :UserId";
            $params[] = array(":UserId", $excludeUserId, PDO::PARAM_INT);
        }
        $matchedUserId = $db->queryItemSafe($sql, $params);
        return $matchedUserId;
    }

    public function getCourses(){
        $db = new EduDb();
        $sql = "SELECT CourseId FROM program_courses pc INNER JOIN programs p ON pc.ProgramId = p.ProgramId WHERE p.InstitutionId IN (SELECT InstitutionId FROM institution_admins WHERE UserId = $this->id)";
        return $db->queryColumn( $sql );
    }

    public function getBookAssignments(){
        $db = new EduDb();
        $sql = "SELECT ct.TextbookId FROM course_textbooks ct INNER JOIN program_courses pc ON ct.CourseId = pc.CourseId INNER JOIN programs p ON pc.ProgramId = p.ProgramId WHERE p.InstitutionId IN (SELECT InstitutionId FROM institution_admins WHERE UserId = $this->id)";
        return $db->queryColumn( $sql );
    }

    public function getDatasets(){
        $db = new EduDb();
        $sql = "SELECT DatasetId FROM course_datasets cd INNER JOIN program_courses pc ON cd.CourseId = pc.CourseId INNER JOIN programs p on pc.ProgramId = p.ProgramId WHERE p.InstitutionId IN (SELECT InstitutionId FROM institution_admins WHERE UserId = $this->id);";
        return $db->queryColumn( $sql );
    }

    public function getCases(){
        $db = new EduDB();
        $sql = "SELECT CaseId FROM course_cases cc INNER JOIN program_courses pc ON cc.CourseId = pc.CourseId INNER JOIN programs p on pc.ProgramId = p.ProgramId WHERE p.InstitutionId IN (SELECT InstitutionId FROM institution_admins WHERE UserId = $this->id);";
        return $db->queryColumn( $sql );
    }

    public function getInstructors(){
        $db = new EduDB();
        $sql = "SELECT DISTINCT InstructorId from courses c INNER JOIN program_courses pc on c.CourseId = pc.CourseId INNER JOIN programs p on pc.ProgramId = p.ProgramId WHERE p.InstitutionId IN (SELECT InstitutionId FROM institution_admins WHERE UserId = $this->id) AND c.InstructorId IS NOT NULL";
        return $db->queryColumn( $sql );
    }

    public function getSoftware(){
        $db = new EduDB();
        $sql = "SELECT DISTINCT SoftwareId from course_softwares cs INNER JOIN program_courses pc on cs.CourseId = pc.CourseId INNER JOIN programs p on pc.ProgramId = p.ProgramID WHERE p.InstitutionId IN (SELECT InstitutionId FROM institution_admins WHERE UserId = $this->id)";
        return $db->queryColumn( $sql );
    }

    /**
     * This is just a method that will get all the states and abbreviations from the states table. I needed a place to get these so I threw this function in here
     * @return array of state abbreviations and state names
     */
    public static function getStateList(){
        $db = new EduDB();
        $sql = "SELECT * FROM states";
        $states = $db->query( $sql );
        return $states;
    }
}