<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/26/2019
 * Time: 2:41 PM
 */
//require the init script
require_once '../../init.php';

//get the ProgramId to find out which textbooks to display
$progId = filter_input(INPUT_GET, 'progId', FILTER_VALIDATE_INT);

$content = '';

if ($progId) {
    # ToDo: fix this up so that it displays a list of courses in this program w/ textbooks & display the textbook info
     # under each course that has a textbook

    # ToDo: FIX THIS MESS!!
    //get the textbooks in this program
    $prog = new Program($progId);
    $progName = $prog->Attributes['ProgramName'];
    $inst = new Institution($prog->Attributes['InstitutionId']);
    $instName = $inst->Attributes['InstitutionName'];

    $cards = '';

    //get courses in this program
    $courses = $prog->getCourses();

    //check to make sure courses were returned
    if($courses){
        //for each course, get list of textbooks
        foreach($courses as $course){
            $courseObj = new Course($course);
            $courseBooks = $courseObj->getBooks();

            //set up the card header w/ the course name
            $cards .= <<<EOT
<div class="card"><!-- Card Start -->
    <div class="card-header"><!-- Card Header Start -->
        {$courseObj->Attributes['CourseTitle']}
    </div><!-- Card Header Close -->
    <div class="card-body"><!-- Card Body Start -->
EOT;

            //check to make sure books were returned
            if($courseBooks){
                $tableRows = '';
                foreach ($courseBooks as $book) {
                    $tableRows .= <<<EOT
<tr>
    <td>{$book['TextbookName']}</td>
    <td>{$book['Authors']}</td>
    <td>{$book['TextbookPublisher']}</td>
    <td><a type="button" class="btn btn-info" href="/textbooks/edit.php?id={$book['TextbookId']}">Edit</a></td>
</tr>
EOT;
                }
                $cards .= <<<EOT
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
                $courseId = $courseObj->Attributes['CourseId'];
                $cards .= '<p>No textbooks are currently available for this course.</p>';
                $cards .= "<a class='btn btn-primary btn-block' href='../textbooks/add.php?courseId=$courseId'>Add Textbook to This Course</a>";
            }
            //close the card divs
            $cards .= <<<EOT
    </div><!-- Card Body Close -->
</div><!-- Card Close -->
EOT;
        }
    }
    else {
        $cards .= <<<EOT
<div class="alert alert-info">
    <p>There are currently no courses associated with this program.</p>
    <a class="btn btn-primary btn-block" href="../courses/add.php?progId={$prog->Attributes['ProgramId']}">Add Course to This Program</a>
</div>
EOT;

    }


    $content .= <<<EOT
{$cards}
<br />
<div class="row">
    <a class="btn btn-primary btn-block" href="add.php?progId={$progId}">Add a textbook to this program</a>
</div>
EOT;

} else {
    //programId was not in query string or was not an integer, display a select list for the user to select the program

    //get list of all institutions
    $progs = Program::getAllProgramsAndInstitutions();

    //turn that into an array of name/value pairs to pass to the optionsHTML.php file
    $progListHelper = array();
    foreach ($progs as $prog) {
        $progListHelper[] = array('text' => $prog['ProgramName'] . ' – ' . $prog['InstitutionName'], 'value' => $prog['ProgramId']);
    }

    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $progListHTML = optionsHTML($progListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>View Program Textbooks</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Program">Select a Program</label>
		    <select class="form-control" name="Program" id="Program" onchange="self.location='programTextbooks.php?id='+this.options[this.selectedIndex].value">
		        $progListHTML
            </select>
            <!--<p class="text text-muted" id="Help">The list may take a second or two to load, please be patient after clicking the field.</p>-->
        </div>
    </form>
</div>
EOT;
}
$customJS = <<<EOT
$(function() {
    $('#textbookTable').DataTable();
});
EOT;


//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "View Program Textbooks";
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