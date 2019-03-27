<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/26/2019
 * Time: 11:25 AM
 */
//require the init file
require_once '../../init.php';

//get the contact id
$contactId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if(empty($contactId)){
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'A valid ContactId was not supplied to display.';
    header('Location: index.php');
    die;
}

