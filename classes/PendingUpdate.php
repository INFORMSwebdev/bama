<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/27/2019
 * Time: 10:11 AM
 */

class PendingUpdate extends AOREducationObject
{
    public static $table = "pending_updates";
    public static $primary_key = "UpdateId";
    public static $tableId = 16;
    public static $data_structure = array(
        'UpdateId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'UpdateTypeId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'TableId' => array( 'required' => TRUE, 'datatype'=> PDO::PARAM_INT ),
        'RecordId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'UpdateContent' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'UserId' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT ),
        'ApprovalStatusId' => array( 'required' => FALSE, 'datatype' => PDO::PARAM_INT ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR )
    );
}