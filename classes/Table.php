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
        'TableId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'TableName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'TableDescription' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'ClassName' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR )
    );
}