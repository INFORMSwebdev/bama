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

    public function checkPassword( $password ) {
        $password_hash = $this->Attributes['Password'];
        return password_verify( $password, $password_hash);
    }

    /**
     * @return mixed
     */
    public function generateToken()
    {
        $db = new EduDB();
        $salt = "I wasn't originally going to get a brain transplant, but then I changed my mind.";
        $token = md5($salt . $this->id . time());
        return $this->update('Token', $token);
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

    public function sendInviteEmail()
    {
        $db = new EduDB();
        $email = $this->Attributes['Username'];
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // TODO add code to send email
        } else {
            static::log("Bad email address: $email");
        }
    }

    public function sendPasswordResetEmail()
    {
        $db = new EduDB();
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
            $sql .= " WHERE UserId != :UserId";
            $params[] = array(":UserId", $excludeUserId, PDO::PARAM_INT);
        }
        $matchedUserId = $db->queryItemSafe($sql, $params);
        return $matchedUserId;
    }
}