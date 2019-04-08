<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/14/2019
 * Time: 2:13 PM
 */
//require the init file
require_once '../../init.php';

//get the POST variables passed to this script from the form submission
$progId = filter_input(INPUT_POST, 'progId', FILTER_VALIDATE_INT);
//set up argument array cuz this is a pain
$arrayArgs = array(
    'contactChecklistOption' => array(
        'name' => 'contactChecklistOption',
        'flags' => FILTER_REQUIRE_ARRAY
    )
);
$checkedContacts = filter_input_array(INPUT_POST, $arrayArgs);

$program = new Program($progId);

//check if no options were selected
if(empty($checkedContacts['contactChecklistOption'])){
    //unassign all contacts from this program since none were checked
    $result = $program->unassignAllContacts();

    if($result){
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'All contacts have successfully been un-assigned from this program.';
    }
    else {
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'There was an error while un-assigning all contacts from this program. Alert the I.T. department.';
    }
}
else {
    $assignErrors = [];

    //go through each checked contact and assign them to the program
    foreach($checkedContacts['contactChecklistOption'] as $checked){
        $result = $program->assignContact($checked);

//        if(!$result){
//            $assignErrors[] = $checked;
//        }
    }

    //check for errors with any of the contacts being unassigned
    if(empty($assignErrors)){
        $_SESSION['editMessage']['success'] = true;
        $_SESSION['editMessage']['text'] = 'All selected contacts have successfully been assigned to this program.';
    }
    else {
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'Error assigning selected contacts to this program.';
        //$_SESSION['editMessage']['ids'] = $assignErrors;
    }
}
//redirect user to program display page
header("Location: /programs/display.php?id=$progId");
die;