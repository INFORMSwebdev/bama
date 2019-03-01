<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/1/2019
 * Time: 7:58 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to add software to the system.';
    header('Location: ../users/login.php');
    die;
}

//get current user
$user = new User($_SESSION['loggedIn']);

//display the form for adding institution info to the user
$content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processSoftwareAddForm.php" method="POST">
        <div class="form-row">
            <h3>Software Details</h3>
        </div>
        <div class="form-row"> 
            <label for="name">Name</label>
            <input type="text" class="form-control" name="name" id="name" placeholder="Name of software" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="publisher">Publisher</label>
            <input type="text" class="form-control" name="publisher" id="publisher" placeholder="Publishing company of the software" />
        </div>
        <br />
        <div class="form-row">
            <button class="btn btn-warning" type="submit" name="add" value="add">Submit New Software</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This software will not be added to the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add New Software";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
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