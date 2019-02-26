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
$progId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

if ($progId) {
    //get the textbooks in this program
    $prog = new Program($progId);
    $progName = $prog->Attributes['ProgramName'];
    $inst = new Institution($prog->Attributes['InstitutionId']);
    $instName = $inst->Attributes['InstitutionName'];
    $progBooks = $prog->getTextbooks();
    $tableRows = '';
    if ($progBooks == true) {
        foreach ($progBooks as $book) {
            $tableRows .= <<<EOT
<tr>
    <td>{$book['TextbookName']}</td>
    <td>{$book['Authors']}</td>
    <td>{$book['TextbookPublisher']}</td>
    <td><button type="button" class="btn btn-info">Placeholder</button></td>
</tr>
EOT;
        }

        //if content is returned (i.e. at least one course of this program has associated textbooks)
        $content = <<<EOT
<div class="flex-column">
    <h2>Textbooks for: {$progName} ({$instName})</h2>
</div>
<table class="table">
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
    } else {
        //display that there are no textbooks associated with this program
        $content = <<<EOT
<div class="alert alert-warning">
    <p>There are no textbooks in any courses associated with the program {$progName} ({$instName})</p>
</div>
EOT;
    }
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

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "View Program Textbooks";
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