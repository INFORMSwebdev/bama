<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/28/2019
 * Time: 2:29 PM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to add datasets to the system.';
    header('Location: ../users/login.php');
    die;
}

//get the current user
if(is_numeric($_SESSION['loggedIn'])) {
    $user = new User($_SESSION['loggedIn']);
}
else {
    //I don't think this should ever occur, but just in case its here!
    //or the system account
    $user = new User(1);
}

$content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processDatasetAddForm.php" method="POST">
        <div class="form-row">
            <h3>Dataset Details</h3>
        </div>
        <div class="form-row"> 
            <label for="datasetName">Name</label>
            <input type="text" class="form-control" name="datasetName" id="datasetName" placeholder="Name of dataset" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="datasetType">Type</label>
            <input type="text" class="form-control" name="datasetType" id="datasetType" placeholder="Type of dataset" required />
        </div>
        <br />
        <div class="form-row">
            <label for="datasetIntegrity">Integrity</label>
		    <textarea class="form-control" name="datasetIntegrity" id="datasetIntegrity" aria-describedby="integrityHelp" placeholder="Describe the integrity of the dataset" required></textarea>
		    <p id="integrityHelp">Note telling how the integrity is expected to be entered.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="datasetFileType">File Type</label>
            <input type="text" class="form-control" name="datasetFileType" id="datasetFileType" aria-describedby="fileTypeHelp" placeholder="Type of file the dataset is" required />
            <p id="fileTypeHelp">.DOCX, .DOC, .PDF, .XLSX, etc.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="useDescription">Use Description</label>
            <textarea class="form-control" name="useDescription" id="useDescription" aria-describedby="descriptionHelp" placeholder="Describe how to use the dataset" required></textarea>
            <p id="descriptionHelp">Advice on how to best input the use description.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="datasetAccess">Access</label>
            <input type="text" class="form-control" name="datasetAccess" id="datasetAccess" aria-describedby="accessHelp" placeholder="www.informs.org/datasets/exampleFile.docx" required />
            <p id="accessHelp">Only valid URLs will be accepted.</p>
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
            <button class="btn btn-warning" type="submit" name="add" value="add">Submit New Dataset</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This dataset will not be added to the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add New Dataset";
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