<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/6/2019
 * Time: 4:20 PM
 */

class Contact extends AOREducationObject
{
    public static $table = "contacts";
    public static $primary_key = "ContactId";
    public static $data_structure = array(
        'ContactId' => array( 'required' => TRUE, 'datatype' => PDO::PARAM_INT ),
        'ContactName' => array( 'required' => TRUE, 'datatype'=> PDO::PARAM_STR ),
        'ContactTitle' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'ContactPhone' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'ContactEmail' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'CreateDate' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_STR ),
        'Deleted' => array( 'required' => FALSE, 'datatype'=> PDO::PARAM_INT )
    );

    public function setInstitutionContact( $InstitutionId ){
      // this can be accomplished by updating the institution object's ContactId attribute so not adding code for this until later
    }
}