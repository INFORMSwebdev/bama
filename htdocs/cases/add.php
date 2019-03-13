<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 9:56 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to add case studies to the system.';
    header('Location: /users/login.php');
    die;
}

$courseId = filter_input(INPUT_GET, 'courseId', FILTER_VALIDATE_INT);

//get the user
$user = new User($_SESSION['loggedIn']);

$content = <<<EOT
<div class="container-fluid">
    <form action="../scripts/processCaseAddForm.php" method="POST">
        <div class="form-row">
            <h3>Case Study Details</h3>
        </div>
        <div class="form-row"> 
            <label for="caseTitle">Title</label>
            <input type="text" class="form-control" name="caseTitle" id="caseTitle" placeholder="Title of case study" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="caseType">Type</label>
            <input type="text" class="form-control" name="caseType" id="caseType" placeholder="type of case study" />
        </div>
        <div class="form-row">
            <label for="useDesc">Use Description</label>
		    <textarea class="form-control" name="useDesc" id="useDesc" aria-describedby="useDescHelp"></textarea>
		    <p id="useDescHelp">Describe how the case study should be used.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="caseAccess">Access</label>
            <textarea class="form-control" name="caseAccess" id="caseAccess" aria-describedby="accessHelp"></textarea>
		    <p id="accessHelp">This does not have to be only a URL, please describe any information relating to how to access this case study.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="analyticTag">Analytics Tags</label>
            <input type="text" class="form-control" name="analyticTag" id="analyticTag" placeholder="E.g. data mining; data visualization; optimization; etc." />
        </div>
        <br />
        <div class="form-row"> 
            <label for="businessTag">Business Tags</label>
            <input type="text" class="form-control" name="businessTag" id="businessTag" placeholder="E.g. entertainment; marketing; healthcare; etc." />
        </div>
        <br />
        <div class="form-row">
            <input type="hidden" id="courseId" name="courseId" value="{$courseId}" />
            <button class="btn btn-warning" type="submit" name="add" value="add">Submit New Case Study</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This case study will not be added to the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;


//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add New Case Study";
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