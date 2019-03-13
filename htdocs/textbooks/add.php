<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 1:15 PM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to add books to the system.';
    header('Location: /users/login.php');
    die;
}

$courseId = filter_input(INPUT_GET, 'courseId', FILTER_VALIDATE_INT);

//display the form for adding textbook info to the user
$content = <<<EOT
<div class="container-fluid">
    <form action="../scripts/processTextbookAddForm.php" method="POST">
        <div class="form-row">
            <h3>Textbook Details</h3>
        </div>
        <div class="form-row"> 
            <label for="textbookName">Name</label>
            <input type="text" class="form-control" name="textbookName" id="textbookName" placeholder="Title of textbook" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="textbookAuthors">Author(s)</label>
            <textarea class="form-control" name="textbookAuthors" id="textbookAuthors" aria-describedby="authorHelp"></textarea>
            <p id="authorHelp">Please list the authors and separate them by either commas (,) or semi-colons (;).</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="textbookPublisher">Publisher</label>
            <input type="text" class="form-control" name="textbookPublisher" id="textbookPublisher" placeholder="Name of textbook publisher" />
        </div>
        <br />
        <div class="form-row">
            <input type="hidden" id="courseId" name="courseId" value="{$courseId}" />
            <button class="btn btn-warning" type="submit" name="add" value="add">Submit New Textbook</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This textbook will not be updated in the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add New Textbook";
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