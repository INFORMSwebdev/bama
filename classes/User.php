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

    public function assignToInstitution($InstitutionId)
    {

    }

    public function generateToken() {

    }

    public function sendInviteEmail() {

    }

    public function sendPasswordResetEmail() {

    }

    public static function checkUsernameExists($Username){

    }
}