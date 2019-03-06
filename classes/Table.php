<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/2/2019
 * Time: 2:28 PM
 */

class Table extends AOREducationObject
{
    public static $table = "table_lookup";
    public static $primary_key = "TableId";
    public static $tableId = 22;
    public static $data_structure = array(
        'TableId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT, 'label' => 'Table ID', 'editable' => FALSE ),
        'TableName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR, 'label' => 'Table Name', 'editable' => FALSE ),
        'TableDescription' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Table Description', 'editable' => FALSE ),
        'ClassName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR, 'label' => 'Associated PHP Classname', 'editable' => FALSE )
    );
}