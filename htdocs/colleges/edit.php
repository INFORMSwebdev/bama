<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/7/2019
 * Time: 12:56 PM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true) {
    //set up a message to display on the login page
    $_SESSION['logoutMessage'] = 'Please log in to edit college information.';
    //redirect to login page so user can log in
    header('Location: login.php');
    //don't want the script to keep executing after a redirect
    die;
}

//get the CourseId
$collegeId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

//get the user
$user = new User($_SESSION['loggedIn']);

if($collegeId){
    $college = new College($collegeId);

    $colType = $college->Attributes['TypeId'];
    $typeOptions = '';
    if(!is_null($colType)){
        $typeOptions = Dropdowns::getCollegeTypeOptions($colType);
    } else {
        $typeOptions = Dropdowns::getCollegeTypeOptions();
    }

    $otherHidden = 'd-none';
    if($colType == 6){
        $otherHidden = '';
    }

    $otherType = $college->getOtherType();
    if(is_null($otherType)){
        $otherType = '';
    }

    $content = <<<EOT
<div class="flex-column">
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <form action="../scripts/processCollegeEditForm.php" method="POST">
        <div class="form-row"> 
            <h3>College Details</h3>
        </div>
        <div class="form-row"> 
            <label for="collegeName">Title</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="collegeName" id="collegeName" value="{$college->Attributes['CollegeName']}"  placeholder="Name of college" required />
        </div>
        <div class="form-row"> 
            <label for="collegeType">Type</label>
            <!--<input type="text" class="form-control" name="collegeType" id="collegeType" value="" placeholder="Type of college" />-->
            <select id="collegeType" name="collegeType" class="form-control" required> 
                {$typeOptions}
            </select>
        </div>
        <div class="form-row {$otherHidden}" id="otherRow"> 
            <label for="otherType">Other Type</label>
            <input type="text" class="form-control" name="otherType" id="otherType" placeholder="Specify other type" value="{$otherType}" />
        </div>
        <br />
        <div class="form-row">
            <input type="hidden" id="collegeId" name="collegeId" value="{$college->id}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This College</button>
        </div>
    </form>
</div>
<br />
<div class="flex-column">
    <a href="/colleges/display.php?id={$college->id}" role="button" class="btn btn-primary">View College Details Page</a>
</div>
EOT;
}
else {
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'A valid college Id was not supplied.';
    header('Location: /index.php');
    die;
}

$typeJS = <<<EOT
$(function() {
    //college delete button functionality
    $(document).on( 'change', '#collegeType', function(e) {
        //check whether the option for 'Other' is selected
        var selection = $('#collegeType option:selected').val();
        //alert(selection);
        if(selection == 6){
            //show the text field for input
            $('#otherRow').removeClass('d-none');
        } else {
            $('#otherRow').addClass('d-none');
        }
    });
});
EOT;


//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit College Information";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['js'][] = array( 'text' => $typeJS );
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();