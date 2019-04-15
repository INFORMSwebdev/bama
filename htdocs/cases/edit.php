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
    $_SESSION['logoutMessage'] = 'You must be logged in to edit case study information.';
    header('Location: /users/login.php');
    die;
}

//get the CaseStudyId
$caseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

//get the user
$user = new User($_SESSION['loggedIn']);

//get courses user has permission to edit
if(isset($_SESSION['admin']) && $_SESSION['admin'] == true){
    $userCases = CaseStudy::getCaseStudies();
}
else {
    $userCases = $user->getCases();
}

//get the options maker, its gonna be needed
include_once('/common/classes/optionsHTML.php');

if($caseId){
    //check if user has permission to edit this course
    if (!in_array($caseId, $userCases) && !isset($_SESSION['admin'])) {
        //set up the message to be red
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'You do not have permission to edit the specified case study information.';

        //redirect to index
        header('Location: /index.php');
        die;
    }

    //get details of case to edit
    $c = new CaseStudy($caseId);
    $name = $c->Attributes['CaseTitle'];
    $type = $c->Attributes['CaseType'];
    $useDesc = $c->Attributes['CaseUseDescription'];
    $access = $c->Attributes['CaseAccess'];
    $analytics = $c->Attributes['AnalyticTag'];
    $business = $c->Attributes['BusinessTag'];

    $content = <<<EOT
<div class="flex-column">
    <p>Fields marked with <span class="text text-danger">*</span> are required.</p>
</div>
<div class="container-fluid">
    <form action="../scripts/processCaseEditForm.php" method="POST">
        <div class="form-row">
            <h3>Case Study Details</h3>
        </div>
        <div class="form-row"> 
            <label for="caseTitle">Title</label><span class="text text-danger">*</span>
            <input type="text" class="form-control" name="caseTitle" id="caseTitle" placeholder="Title of case study" value="{$name}" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="caseType">Type</label>
            <input type="text" class="form-control" name="caseType" id="caseType" placeholder="type of case study" value="{$type}" />
        </div>
        <div class="form-row">
            <label for="useDesc">Use Description</label>
		    <textarea class="form-control" name="useDesc" id="useDesc" aria-describedby="useDescHelp">{$useDesc}</textarea>
		    <p id="useDescHelp">Describe how the case study should be used.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="caseAccess">Access</label>
            <textarea class="form-control" name="caseAccess" id="caseAccess" aria-describedby="accessHelp">{$access}</textarea>
		    <p id="accessHelp">This does not have to be only a URL, please describe any information relating to how to access this case study.</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="analyticTag">Analytics Tags</label>
            <input type="text" class="form-control" name="analyticTag" id="analyticTag" placeholder="E.g. data mining; data visualization; optimization; etc." value="{$analytics}" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="businessTag">Business Tags</label>
            <input type="text" class="form-control" name="businessTag" id="businessTag" placeholder="E.g. entertainment; marketing; healthcare; etc." value="{$business}" />
        </div>
        <br />
        <div class="form-row">
            <input type="hidden" id="caseId" name="caseId" value="{$caseId}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This Case Study</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">These changes will not take effect until they have been approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
<div class="flex-column">
    <a href="/cases/display.php?id={$c->id}" role="button" class="btn btn-primary">View Case Study Details Page</a>
</div>
EOT;
}
else {
    //case id either not an integer or not present in query string

    //display a list of case studies to the user for them to select from THAT THEY HAVE PERMISSION TO EDIT
    $caeListHelper = array();
    foreach($userCases as $caseFoo){
        if(isset($_SESSION['admin'])){
            $caeListHelper[] = array('text' => $caseFoo['CaseTitle'], 'value' => $caseFoo['CaseId']);
        }
        else {
            $case = new CaseStudy($caseFoo);
            $caeListHelper[] = array('text' => $case->Attributes['CaseTitle'], 'value' => $case->Attributes['CaseId']);
        }
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    $caseListHTML = optionsHTML($caeListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>My Case Studies</h2>
    <p>Inside the list below are all the case studies you have permissions to edit.</p>
</div>
<div class="flex-column">
    <h2>Edit Case Study Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Course">Select a Case Study to edit</label>
		    <select class="form-control" name="Case" id="Case" onchange="self.location='edit.php?id='+this.options[this.selectedIndex].value">
		        {$caseListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Case Study";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();