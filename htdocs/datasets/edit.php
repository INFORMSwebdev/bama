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
    $_SESSION['logoutMessage'] = 'Please log in to edit dataset information.';
    header('Location: /users/login.php');
    die;
}

//get the datasetId
$datasetId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

//get the current user
$user = new User($_SESSION['loggedIn']);

//get datasets user has permission to edit
if(isset($_SESSION['admin']) && $_SESSION['admin'] == true){
    $userSets = Dataset::getAllDatasets();
}
else {
    $userSets = $user->getDatasets();
}

//get the options maker, its gonna be needed
include_once('/common/classes/optionsHTML.php');

if($datasetId){
    //valid Id was passed via query string

    //make sure user has permission to edit this dataset
    if (!in_array($datasetId, $userSets) && !isset($_SESSION['admin'])) {
        //set up the message to be red
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'You do not have permission to edit the specified dataset\'s information.';

        //redirect to index
        header('Location: /index.php');
        die;
    }

    $dataset = new Dataset($datasetId);

    $content = <<<EOT
<div class="flex-column">
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <form action="../scripts/processDatasetEditForm.php" method="POST">
        <div class="form-row">
            <h3>Dataset Details</h3>
        </div>
        <div class="form-row"> 
            <label for="datasetName">Name</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="datasetName" id="datasetName" placeholder="Name of dataset" value="{$dataset->Attributes['DatasetName']}" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="datasetType">Type</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="datasetType" id="datasetType" placeholder="Type of dataset" value="{$dataset->Attributes['DatasetType']}" required />
        </div>
        <br />
        <div class="form-row">
            <label for="datasetIntegrity">Integrity</label><span class="text text-danger">*</span>
		    <textarea class="form-control" name="datasetIntegrity" id="datasetIntegrity" aria-describedby="integrityHelp" placeholder="Describe the integrity of the dataset" value="{$dataset->Attributes['DatasetIntegrity']}" required></textarea>
		    <p id="integrityHelp">Note telling how the integrity is expected to be entered.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="datasetFileType">File Type</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="datasetFileType" id="datasetFileType" aria-describedby="fileTypeHelp" placeholder="Type of file the dataset is" value="{$dataset->Attributes['DatasetFileType']}" required />
            <p id="fileTypeHelp">.DOCX, .DOC, .PDF, .XLSX, etc.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="useDescription">Use Description</label><span class="text text-danger">*</span>
            <textarea class="form-control" name="useDescription" id="useDescription" aria-describedby="descriptionHelp" placeholder="Describe how to use the dataset" value="{$dataset->Attributes['DatasetUseDescription']}" required></textarea>
            <p id="descriptionHelp">Advice on how to best input the use description.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="datasetAccess">Access</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="datasetAccess" id="datasetAccess" aria-describedby="accessHelp" placeholder="www.informs.org/datasets/exampleFile.docx" value="{$dataset->Attributes['DatasetAccess']}" required />
            <p id="accessHelp">Only valid URLs will be accepted.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="analyticTag">Analytics Tags</label>
            <input type="text" class="form-control" name="analyticTag" id="analyticTag" placeholder="E.g. data mining; data visualization; optimization; etc." value="{$dataset->Attributes['AnalyticTag']}" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="businessTag">Business Tags</label>
            <input type="text" class="form-control" name="businessTag" id="businessTag" placeholder="E.g. entertainment; marketing; healthcare; etc." value="{$dataset->Attributes['BusinessTag']}" />
        </div>
        <br />
        <div class="form-row">
            <input type="hidden" id="datasetId" name="datasetId" value="{$datasetId}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This Dataset</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This dataset will not be added to the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;
}
else {
    //Id was either not an integer or not set
    //display a list of datasets to the user for them to select from THAT THEY HAVE PERMISSION TO EDIT
    $datasetListHelper = array();
    foreach($userSets as $dataId){
        if(isset($_SESSION['admin'])){
            $datasetListHelper[] = array('text' => $dataId['DatasetName'], 'value' => $dataId['DatasetId']);
        }
        else {
            $dSet = new Dataset($dataId);
            $datasetListHelper[] = array('text' => $dSet->Attributes['DatasetName'], 'value' => $dSet->Attributes['DatasetId']);
        }
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    $datasetListHTML = optionsHTML($datasetListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>Edit Dataset Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Course">Select a Dataset to edit</label>
		    <select class="form-control" name="Dataset" id="Dataset" onchange="self.location='edit.php?id='+this.options[this.selectedIndex].value">
		        {$datasetListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Dataset";
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