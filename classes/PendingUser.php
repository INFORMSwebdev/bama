<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/27/2019
 * Time: 10:07 AM
 */

class PendingUser extends AOREducationObject
{
    public static $table = "pending_users";
    public static $primary_key = "PendingUserId";
    public static $tableId = 17;
    public static $data_structure = array(
        'PendingUserId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'Username' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'FirstName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'LastName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'InstitutionId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'Comments' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR )
    );
}