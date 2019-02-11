<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:22 PM
 */

class Dataset extends AOREducationObject
{
    public static $table = "datasets";
    public static $primary_key = "DatasetId";
    public static $data_structure = array(
        'DatasetId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'DatasetName' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_STR ),
        'DatasetType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DatasetIntegrity' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DatasetFileType' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DatasetUseDescription' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'DatasetAccess' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'AnalyticTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'BusinessTag' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );
}