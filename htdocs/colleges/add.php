<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/6/2019
 * Time: 1:11 PM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to add a college to an institution.';
    header('Location: /users/login.php');
    die;
}

//get the user
$user = new User($_SESSION['loggedIn']);

//get the institution id from the page the user was just viewing
$instId = filter_input(INPUT_GET, 'instId', FILTER_VALIDATE_INT);

if($instId){

    $content = <<<EOT
<div class="flex-column">
    <p>This college will be associated with your assigned institution. Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <form action="/scripts/processCollegeAddForm.php" method="POST">
        <div class="form-row">
            <h3>College Details</h3>
        </div>
        <div class="form-row"> 
            <label for="collegeName">Name</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="collegeName" id="collegeName" placeholder="Name of college" required />
        </div>
        <div class="form-row"> 
            <label for="collegeType">Type</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="collegeType" id="collegeType" placeholder="Type of college" required />
        </div>
        <br />
        <div class="form-row">
            <input type="hidden" name="institutionId" id="institutionId" value="{$instId}" />
            <button class="btn btn-primary" type="submit" name="add" value="add">Add College</button>
        </div>
    </form>
</div>
EOT;
}
else {
    //either no instId in query string or it wasn't an integer
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'No valid InstitutionId was supplied.';
    header('Location: /index.php');
    die;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add New College";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//$page_params['js'][] = array( 'text' => $custom_js );
$page_params['show_title_bar'] = FALSE;
//do not display the usual header/footer
$page_params['admin'] = TRUE;
//$page_params['active_menu_item'] = 'home';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();