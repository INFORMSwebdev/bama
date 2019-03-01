<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 9:55 AM
 */
//require the init file
require_once '../../init.php';

//get the courseId
$courseId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

//check to make sure we have an Id to work with
if(empty($courseId)){
    //no valid Id supplied in query string
    //display a list of courses the user can choose from

    $courses = Course::getAllCourses();
    $courseListHelper = array();
    foreach($courses as $course){
        $courseListHelper[] = array('text' => $course['CourseTitle'], 'value' => $course['CourseId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $courseListHTML = optionsHTML($courseListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>View Course Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="course">Select a Course</label>
		    <select class="form-control" name="course" id="course" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        {$courseListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}
else {
    //get details of course
    $courseObj = new Course($courseId);
    $name = $courseObj->Attributes['CourseTitle'];
    $num = $courseObj->Attributes['CourseNumber'];
    $numberHTML = '<h3>Number</h3>';
    if(isset($num)){
        $numberHTML .= "<p>$num</p>";
    }
    else {
        $numberHTML .= '<p>Course number is currently not available.</p>';
    }
    $delivery = $courseObj->Attributes['DeliveryMethod'];
    $deliveryHTML = '<h3>Delivery Method</h3>';
    if(isset($delivery)){
        $deliveryHTML .= "<p>$delivery</p>";
    }
    else {
        $deliveryHTML .= '<p>Delivery method is currently not available.</p>';
    }
    $capstone = $courseObj->Attributes['HasCapstoneProject'];
    $capstoneHTML = '<h3>Has Capstone Project</h3>';
    if(isset($capstone)){
        $capstoneHTML .= "<p>$capstone</p>";
    }
    else {
        $capstoneHTML .= '<p>Capstone information is currently not available.</p>';
    }
    $text = $courseObj->Attributes['CourseText'];
    $textHTML = '<h3>Course Text</h3>';
    if(isset($text)){
        $textHTML .= "<p>$text</p>";
    }
    else {
        $textHTML .= '<p>Course text is currently not available.</p>';
    }
    $analytic = $courseObj->Attributes['AnalyticTag'];
    $analyticHTML = '<h3>Analytics Tags</h3>';
    if(isset($analytic)){
        $analyticHTML .= "<p>$analytic</p>";
    }
    else {
        $analyticHTML .= '<p>Analytics tags are not currently available.</p>';
    }
    $business = $courseObj->Attributes['BusinessTag'];
    $businessHTML = '<h3>Business Tags</h3>';
    if(isset($business)){
        $businessHTML .= "<p>$business</p>";
    }
    else {
        $businessHTML .= '<p>Business tags are not currently available.</p>';
    }
    $instructorId = $courseObj->Attributes['InstructorId'];

    $courseHTML = <<<EOT
{$numberHTML}
{$deliveryHTML}
{$capstoneHTML}
{$textHTML}
{$analyticHTML}
{$businessHTML}
EOT;


    //set up HTML to display on the instructors tab
    $instructorHTML = '';
    if(isset($instructorId) && is_numeric($instructorId)){
        //get instructor details
        $instructor = new Instructor($instructorId);
        $instructorFname = $instructor->Attributes['InstructorFirstName'];
        $instructorLname = $instructor->Attributes['InstructorLastName'];
        $instructorPref = $instructor->Attributes['InstructorPrefix'];
        $instructorEmail = $instructor->Attributes['InstructorEmail'];
        $emailHTML = '';
        if(isset($instructorEmail)){
            $emailHTML = "<p><a href='mailto:$instructorEmail'>$instructorEmail</a></p>";
        }
        else {
            $emailHTML = "<p>Email information for this instructor is not currently available.</p>";
        }
        $instructorHTML = <<<EOT
<h3>Name</h3>
<p>{$instructorPref} {$instructorFname} {$instructorLname}</p>
{$emailHTML}
EOT;
    }
    else {
        //no instructor for this course
        $instructorHTML = '<p>There is currently no instructor assigned to this course.</p>';
    }

    //get any textbooks used in the course
    $books = $courseObj->getBooks();
    $bookHTML = '';
    if($books){
        $tableRows = '';
        foreach($books as $bookId){
            $b = new Textbook($bookId);
            $tableRows .= <<<EOT
<tr>
    <td>{$b->Attributes['TextbookName']}</td>
    <td>{$b->Attributes['Authors']}</td>
    <td>{$b->Attributes['TextbookPublisher']}</td>
    <td><a type="button" class="btn btn-info" href="/textbooks/edit.php?id={$b->Attributes['TextbookId']}">Edit</a></td>
</tr>
EOT;
        }
        $bookHTML = <<<EOT
<table class="table" id="textbookTable">
    <thead>
        <tr>
            <th>Name</th>
            <th>Author(s)</th>
            <th>Publisher</th>
            <th></th><!-- buttons -->
        </tr>
    </thead>
    <tbody>
        {$tableRows}
    </tbody>
</table>
EOT;

    }
    else {
        //no books for this course
        $bookHTML = '<p>There are currently no textbooks assigned to this course</p>';
    }


    //get any software used in the course
    $softwares = $courseObj->getSoftware();
    $softwareHTML = '';
    if($softwares){
        $softwareRows = '';
        foreach($softwares as $software){
            $soft = new Software($software);
            $softwareRows .= <<<EOT
<tr>
    <td>{$soft->Attributes['SoftwareName']}</td>
    <td>{$soft->Attributes['SoftwarePublisher']}</td>
    <td><a type="button" class="btn btn-info" href="/software/edit.php?id={$soft->Attributes['SoftwareId']}">Edit</a></td>
</tr>
EOT;
        }
        $softwareHTML = <<<EOT
<table class="table" id="softwareTable">
    <thead> 
        <tr> 
            <th>Name</th>
            <th>Publisher</th>
            <th></th><!-- buttons -->
        </tr>
    </thead>
    <tbody> 
        {$softwareRows}
    </tbody>
</table>
EOT;
    }
    else {
        //no software for this course
        $softwareHTML = '<p>There is currently no software assigned to this course.</p>';
    }

    //get any datasets
    $datasets = $courseObj->getDatasets();
    $datasetHTML = '';
    if($datasets){
        $datasetHTML = '<div class="card-columns"><!-- card columns start -->';
        foreach($datasets as $dataset){
            $data = new Dataset($dataset);
            $datasetHTML .= <<<EOT
<div class="card"><!-- card start -->
    <div class="card-header"><!-- card header start -->
        <h2 class="display2">{$data->Attributes['DatasetName']}</h2>
    </div><!-- card header end -->
    <div class="card-body"> <!-- card body start -->
        <h3>Type</h3>
        {$data->Attributes['DatasetType']}
        <h3>Integrity</h3>
        {$data->Attributes['DatasetIntegrity']}
        <h3>Dataset Filetype</h3>
        {$data->Attributes['DatasetFileType']}
        <h3>Use Description</h3>
        {$data->Attributes['DatasetUseDescription']}
        <h3>Access</h3>
        {$data->Attributes['DatasetAccess']}
        <h3>Analytics Tags</h3>
        {$data->Attributes['AnalyticTag']}
        <h3>Business Tags</h3>
        {$data->Attributes['BusinessTag']}
    </div><!-- card body end -->
    <div class="card-footer"><!-- card footer start -->
        <a type="button" class="btn btn-info btn-block" href="/datasets/edit.php?id={$data->Attributes['DatasetId']}">Edit</a>    
    </div><!-- card footer end -->
</div><!-- card end -->
EOT;
        }
        $datasetHTML .= '</div><!-- card columns end -->';
    }
    else {
        //no datasets for this course
        $datasetHTML = '<p>There are currently no data sets assigned to this course.</p>';
    }

    //get any case studies
    $cases = $courseObj->getCases();
    $caseHTML = '';
    if($cases){
        $caseHTML = '<div class="card-columns"><!-- card columns start -->';
        foreach($cases as $case){
            $c = new CaseStudy($case);
            $caseHTML .= <<<EOT
<div class="card"><!-- card start -->
    <div class="card-header"><!-- card header start -->
        <h2 class="display2">{$c->Attributes['CaseTitle']}</h2>
    </div><!-- card header end -->
    <div class="card-body"> <!-- card body start -->
        <h3>Type</h3>
        {$c->Attributes['CaseType']}
        <h3>Use Description</h3>
        {$c->Attributes['CaseUseDescription']}
        <h3>Access</h3>
        {$c->Attributes['CaseAccess']}
        <h3>Analytics Tags</h3>
        {$c->Attributes['AnalyticTag']}
        <h3>Business Tags</h3>
        {$c->Attributes['BusinessTag']}
    </div><!-- card body end -->
    <div class="card-footer"><!-- card footer start -->
        <a type="button" class="btn btn-info btn-block" href="/cases/edit.php?id={$case->Attributes['CaseId']}">Edit</a>  
    </div><!-- card footer end -->
</div><!-- card end -->
EOT;
        }
        $caseHTML .= '</div><!-- card columns end -->';
    }
    else {
        //no case studies for this course
        $casesHTML = '<p>There are currently no case studies assigned to this course.</p>';
    }

    //display the course details
    $content = <<<EOT
<div class="card">
    <div class="card-header" id="cardHeader">
        <h2 class="display2">{$name}</h2>
        <ul class="nav nav-tabs card-header-tabs" id="cardNav" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="courseDetails" href="#tabCourse" data-toggle="tab" aria-selected="true" aria-controls="tabCourse">Course Details</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="instructorDetails" href="#tabInstructors" data-toggle="tab" aria-selected="false" aria-controls="tabInstructors">Instructors</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="textbookDetails" href="#tabTextbooks" data-toggle="tab" aria-selected="false" aria-controls="tabTextbooks">Textbooks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="softwareDetails" href="#tabSoftware" data-toggle="tab" aria-selected="false" aria-controls="tabSoftware">Software</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="datasetDetails" href="#tabDatasets" data-toggle="tab" aria-selected="false" aria-controls="tabDatasets">Data Sets</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="caseDetails" href="#tabCase" data-toggle="tab" aria-selected="false" aria-controls="tabCase">Case Studies</a>
            </li>
        </ul>
    </div>
    <div class="tab-content" id="ProgramTabContent">
        <div class="tab-pane fade show active" id="tabCourse" role="tabpanel" aria-labelledby="courseDetails">
            <div class="card-body">
                {$courseHTML}
            </div>
        </div>
        <div class="tab-pane fade" id="tabInstructors" role="tabpanel" aria-labelledby="instructorDetails">
            <div class="card-body">
                {$instructorHTML}
            </div>
        </div>
        <div class="tab-pane fade" id="tabTextbooks" role="tabpanel" aria-labelledby="textbookDetails">
            <div class="card-body">
                {$bookHTML}
            </div>
        </div>
        <div class="tab-pane fade" id="tabSoftware" role="tabpanel" aria-labelledby="softwareDetails">
            <div class="card-body">
                {$softwareHTML}
            </div>
        </div>
        <div class="tab-pane fade" id="tabDatasets" role="tabpanel" aria-labelledby="datasetDetails">
            <div class="card-body">
                {$datasetHTML}
            </div>
        </div>
        <div class="tab-pane fade" id="tabCase" role="tabpanel" aria-labelledby="caseDetails">
            <div class="card-body">
                {$casesHTML}
            </div>
        </div>
    </div>
    <div class="card-footer" id="cardFooter">
        <div class="btn-group" role="group" aria-label="Add new course items to this course.">
            <a role="button" class="btn btn-outline-primary" href="/software/add.php?courseId={$courseObj->Attributes['CourseId']}">Add New Software</a>
            <a role="button" class="btn btn-outline-primary" href="/textbooks/add.php?courseId={$courseObj->Attributes['CourseId']}">Add New Textbook</a>
            <a role="button" class="btn btn-outline-primary" href="/instructors/add.php?courseId={$courseObj->Attributes['CourseId']}">Add New Instructor</a>
            <a role="button" class="btn btn-outline-primary" href="/cases/add.php?courseId={$courseObj->Attributes['CourseId']}">Add New Case Study</a>
            <a role="button" class="btn btn-outline-primary" href="/datasets/add.php?courseId={$courseObj->Attributes['CourseId']}">Add New Data Set</a>
        </div>
    </div>
</div>
EOT;
}

//set up the custom JS for making tables into datatables
$customJS = <<<EOT
$(function() {
    $('#textbookTable').DataTable();
    $('#softwareTable').DataTable();
    $('#datasetTable').DataTable();
    $('#caseTable').DataTable();
});
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "View Course Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
$page_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
$page_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
$page_params['js'][] = array( 'text' => $customJS );
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