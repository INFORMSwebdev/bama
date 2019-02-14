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
        'UserId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'Username' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'Password' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Comments' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Token' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => TRUE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => TRUE, 'datatype'=> PDO::PARAM_INT )
    );

    public function assignToInstitution($InstitutionId) {
        $db = new EduDB();
    }

    public function generateToken() {
        $db = new EduDB();
    }

    public function sendInviteEmail() {
        $db = new EduDB();
    }

    public function sendPasswordResetEmail() {
        $db = new EduDB();
    }

    public function unassignFromInstitution( $InstitutionId ){
        $db = new EduDB();
    }

    public static function usernameExists ( $username, $excludeUserId = null ) {
        $db = new EduDB();
        $sql = "SELECT UserId FROM users WHERE Username=:Username";
        $params =  array( array(':Username', $username, PDO::PARAM_STR ));
        if ($excludeUserId) {
            $sql .= " WHERE UserId != :UserId";
            $params[] = array( ":UserId", $excludeUserId, PDO::PARAM_INT );
        }
        $matchedUserId = $db->queryItemSafe( $sql, $params );
        return $matchedUserId;
    }

    public static function usernameExists($Username){

    }
}