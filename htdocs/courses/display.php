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
    //set up the message to be red
    $_SESSION['editMessage']['success'] = false;
    $_SESSION['editMessage']['text'] = 'You must select a valid course to view it\'s details.';

    //redirect to index
    header('Location: /index.php');
    die;
}
else {
    if(isset($_SESSION['editMessage'])){
        //set up the alert color
        if($_SESSION['editMessage']['success'] == true){
            //successful insert into pending_updates table
            $content = '<div class="alert alert-success" id="message">';
        }
        else {
            //unsuccessful insert
            $content = '<div class="alert alert-danger" id="message">';
        }
        //add message to alert
        $content .= "<p>{$_SESSION['editMessage']['text']}</p></div>";

        //clear out the session variable after its' use
        $_SESSION['editMessage'] = null;
    }

    //get details of course
    $courseObj = new Course($courseId);
    //$program = $courseObj->getProgram(TRUE);
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
    //$instructorId = $courseObj->Attributes['InstructorId'];
    //get the instructors assigned to this course
    $instructors = $courseObj->getInstructors(TRUE);

    $courseHTML = <<<EOT
{$numberHTML}
{$deliveryHTML}
{$capstoneHTML}
{$textHTML}
{$analyticHTML}
{$businessHTML}
<div class="btn-group">
<a role="button" class="btn btn-warning mr-3" href="/courses/edit.php?id={$courseObj->id}">Edit this Course</a>
<button id="id_{$courseObj->id}" name="courseDelete" type="submit" class="btn btn-danger btn-course-delete">Delete this Course</button>
</div>
EOT;

    //set up HTML to display on the instructors tab
    $instructorHTML = '';
    if($instructors){
        //set up the instructor table
        $instructorHTML .= <<<EOT
<table id="instructorTable" class="table table-striped">
    <thead>
        <tr>
            <th>Prefix</th>
            <th>First Name</th> 
            <th>Last Name</th>
            <th>Email</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
EOT;
        foreach($instructors as $instFoo){
            //get instructor details
            $instructorFname = $instFoo->Attributes['InstructorFirstName'];
            $instructorLname = $instFoo->Attributes['InstructorLastName'];
            $instructorPref = $instFoo->Attributes['InstructorPrefix'];
            $prefOut = '';
            if(isset($instructorPref)){
                $prefOut = $instructorPref;
            }
            else {
                $prefOut = 'Not Set';
            }
            $instructorEmail = $instFoo->Attributes['InstructorEmail'];
            $emailHTML = '';
            if(isset($instructorEmail)){
                $emailHTML = "<a href='mailto:$instructorEmail'>$instructorEmail</a>";
            }
            else {
                $emailHTML = 'Not set.';
            }

            //set up table rows
            $instructorHTML .= <<<EOT
        <tr>
            <td>{$prefOut}</td>
            <td>{$instructorFname}</td>
            <td>{$instructorLname}</td>
            <td>{$emailHTML}</td>
            <td>
                <a role="button" class="btn btn-info btn-block" href="/instructors/display.php?id={$instFoo->id}">View this Instructor</a>
                <a role="button" class="btn btn-warning btn-block" href="/instructors/edit.php?id={$instFoo->id}">Edit this Instructor</a>
                <button id="id_{$instFoo->id}" name="instructorDelete" class="btn btn-danger btn-block btn-instructor-delete">Delete this Instructor</button>
            </td>
        </tr>
EOT;
        }
        $instructorHTML .= <<<EOT
    </tbody>
</table>

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
    <td>
        <a role="button" class="btn btn-info btn-block" href="/textbooks/display.php?id={$b->id}">View this Textbook</a>
        <a role="button" class="btn btn-warning btn-block" href="/textbooks/edit.php?id={$b->id}">Edit this Textbook</a>
        <button id="id_{$b->id}" name="textbookDelete" class="btn btn-danger btn-block btn-textbook-delete">Delete this Textbook</button>
    </td>
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
    <td>
        <a role="button" class="btn btn-info btn-block" href="/software/display.php?id={$soft->id}">View this Software</a>
        <a role="button" class="btn btn-warning btn-block" href="/software/edit.php?id={$soft->id}">Edit this Software</a>
        <button id="id_{$soft->id}" name="softwareDelete" class="btn btn-danger btn-block btn-software-delete">Delete this Software</button>
    </td>
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
        $datasetRows = '';
        foreach($datasets as $dataset){
            $data = new Dataset($dataset);
            $datasetRows .= <<<EOT
<tr>
    <td>{$data->Attributes['DatasetName']}</td>
    <td>{$data->Attributes['DatasetType']}</td>
    <td>{$data->Attributes['DatasetIntegrity']}</td>
    <td>{$data->Attributes['DatasetAccess']}</td>
    <td>
        <a role="button" class="btn btn-info btn-block" href="/datasets/display.php?id={$data->id}">View this Dataset</a>
        <a role="button" class="btn btn-warning btn-block" href="/datasets/edit.php?id={$data->id}">Edit this Dataset</a>
        <button id="id_{$data->id}" name="datasetDelete" class="btn btn-danger btn-block btn-dataset-delete">Delete this Dataset</button>
    </td>    
</tr>
EOT;
        }
        $datasetHTML = <<<EOT
<table class="table table-striped" id="datasetTable">
    <thead> 
        <tr> 
            <th>Name</th>
            <th>Type</th>
            <th>Integrity</th>
            <th>Access</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {$datasetRows}
    </tbody>
</table>
EOT;
    }
    else {
        //no datasets for this course
        $datasetHTML = '<p>There are currently no data sets assigned to this course.</p>';
    }

    //get any case studies
    $cases = $courseObj->getCases();
    $casesHTML = '';
    if($cases){
        $caseRows = '';
        foreach($cases as $case){
            $c = new CaseStudy($case);
            $caseRows .= <<<EOT
<tr>
    <td>{$c->Attributes['CaseTitle']}</td>
    <td>{$c->Attributes['CaseType']}</td>
    <td>{$c->Attributes['CaseAccess']}</td>
    <td>{$c->Attributes['CaseUseDescription']}</td>
    <td>
        <a role="button" class="btn btn-info btn-block" href="/cases/display.php?id={$c->id}">View this Case Study</a>
        <a role="button" class="btn btn-warning btn-block" href="/cases/edit.php?id={$c->id}">Edit this Case Study</a>
        <button id="id_{$c->id}" name="caseDelete" class="btn btn-danger btn-block btn-case-delete">Delete this Case Study</button>
    </td>  
</tr>
EOT;
        }
        $casesHTML .= <<<EOT
<table class="table table-striped" id="caseTable">
    <thead> 
        <tr> 
            <th>Title</th>
            <th>Type</th>
            <th>Access</th>
            <th>Use Description</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        {$caseRows}
    </tbody>
</table>
EOT;

    }
    else {
        //no case studies for this course
        $casesHTML = '<p>There are currently no case studies assigned to this course.</p>';
    }

    //display the course details
    $content .= <<<EOT
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
                <div class="card"> 
                    <div class="card-header">
                        <h3>Assigned Instructors</h3>
                    </div>
                    <div class="card-body">
                        {$instructorHTML}
                        <div class="d-hidden" id="instructorListContainer"><!-- Instructor HTML goes here for AJAX --></div>
                    </div>
                    <div class="card-footer">
                        <a role="button" class="btn btn-primary" id="id_{$courseId}" href="/courses/assignInstructors.php?courseId={$courseId}">Assign Instructors</a>
                    </div>
                </div>
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
    $('#instructorTable').DataTable();
    $('#datasetTable').DataTable();
    
    //course delete button functionality
    $(document).on( 'click', '.btn-course-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this course?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteCourse.php", { 'CourseId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    //alert( msg );
                    $('#message').html('<p>' + msg + '</p>')
                    $('#message').addClass('alert alert-danger');
                    $('#message').show();
                }
                else if (data.msg) {
                    //alert( data.msg );
                    $('#message').html('<p>' + data.msg + '</p>');
                    if(data.msg.includes('submitted')){
                        $('#message').addClass('alert alert-success');
                    }
                    else {
                        $('#message').addClass('alert alert-danger');
                    }
                    $('#message').show();
                }
            }, "json");
        }
    });
    
    //case study delete button functionality
    $(document).on( 'click', '.btn-case-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this case study?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteCaseStudy.php", { 'CaseId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    //alert( msg );
                    $('#message').html('<p>' + msg + '</p>')
                    $('#message').addClass('alert alert-danger');
                    $('#message').show();
                }
                else if (data.msg) {
                    //alert( data.msg );
                    $('#message').html('<p>' + data.msg + '</p>');
                    if(data.msg.includes('submitted')){
                        $('#message').addClass('alert alert-success');
                    }
                    else {
                        $('#message').addClass('alert alert-danger');
                    }
                    $('#message').show();
                }
            }, "json");
        }
    });
    
    //dataset delete button functionality
    $(document).on( 'click', '.btn-dataset-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this course?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteDataset.php", { 'DatasetId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    //alert( msg );
                    $('#message').html('<p>' + msg + '</p>')
                    $('#message').addClass('alert alert-danger');
                    $('#message').show();
                }
                else if (data.msg) {
                    //alert( data.msg );
                    $('#message').html('<p>' + data.msg + '</p>');
                    if(data.msg.includes('submitted')){
                        $('#message').addClass('alert alert-success');
                    }
                    else {
                        $('#message').addClass('alert alert-danger');
                    }
                    $('#message').show();
                }
            }, "json");
        }
    });
    
    //software delete button functionality
    $(document).on( 'click', '.btn-software-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this software?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteSoftware.php", { 'SoftwareId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    //alert( msg );
                    $('#message').html('<p>' + msg + '</p>')
                    $('#message').addClass('alert alert-danger');
                    $('#message').show();
                }
                else if (data.msg) {
                    //alert( data.msg );
                    $('#message').html('<p>' + data.msg + '</p>');
                    if(data.msg.includes('submitted')){
                        $('#message').addClass('alert alert-success');
                    }
                    else {
                        $('#message').addClass('alert alert-danger');
                    }
                    $('#message').show();
                }
            }, "json");
        }
    });
    
    //textbook delete button functionality
    $(document).on( 'click', '.btn-textbook-delete', function(e) {
        //make sure message box gets re-hidden if its shown
        $('#message').hide();
        var conf = confirm( "Are you sure you want to delete this textbook?" );
        if (conf) {
            var id = $(this).attr('id').substring(3);
            $.post( "/scripts/ajax_deleteTextbook.php", { 'TextbookId': id }, function(data) {
                //alert( data );
                if (data.errors.length > 0 ) {
                    var msg = 'One or more errors were encountered:\\r\\n\\r\\n';
                    for (var i = 0; i < data.errors.length; i++) {
                        msg +=  data.errors[i] + "\\r\\n";
                    }
                    //alert( msg );
                    $('#message').html('<p>' + msg + '</p>')
                    $('#message').addClass('alert alert-danger');
                    $('#message').show();
                }
                else if (data.msg) {
                    //alert( data.msg );
                    $('#message').html('<p>' + data.msg + '</p>');
                    if(data.msg.includes('submitted')){
                        $('#message').addClass('alert alert-success');
                    }
                    else {
                        $('#message').addClass('alert alert-danger');
                    }
                    $('#message').show();
                }
            }, "json");
        }
    });
});
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "View Course Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
$page_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
$page_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
$page_params['js'][] = array( 'text' => $customJS );
//$page_params['js'][] = array( 'text' => $custom_js );
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();