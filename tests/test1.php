<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 2/13/2019
 * Time: 11:54 AM
 */
require_once( "../init.php");
$data = array( 'TextbookName' => "Test Textbook", "Authors" => "Smith, John", 'TextbookPublisher'=> 'Prentice Hall');
$x = Textbook::createInstance( $data );
$x = new Textbook( 7 );
$result = $x->createPendingUpdate( UPDATE_TYPE_UPDATE, 1);


echo $result;