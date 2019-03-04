<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 1:15 PM
 */
//require the init file
require_once '../../init.php';

//get the textbookId to display info about
$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if($bookId){
    //get book info to display
    $book = new Textbook($bookId);
    $name = $book->Attributes['TextbookName'];
    $authors = $book->Attributes['Authors'];
    $pub = $book->Attributes['TextbookPublisher'];

    //display info about the book
    $content = <<<EOT
<div class="card">
    <div class="card-header" id="cardHeader">
        <h2 class="display2">{$name}</h2>
    </div>
    <div class="card-body">
        <h3 class="display3">Author(s)</h3>
        <p>{$authors}</p>
        <h3 class="display3">Publisher</h3>
        <p>{$pub}</p>
    </div>
    <!-- ToDo: decide if we want footer buttons or not -->
</div>
EOT;

} else {
    //display a list of books to the user for them to select from

    //get all books
    $books = Textbook::getBooks();
    $bookListHelper = array();
    foreach($books as $prog){
        $bookListHelper[] = array('text' => $prog['TextbookName'], 'value' => $prog['TextbookId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $bookListHTML = optionsHTML($bookListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>View Textbook Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Textbook">Select a Textbook</label>
		    <select class="form-control" name="Textbook" id="Textbook" onchange="self.location='display.php?id='+this.options[this.selectedIndex].value">
		        {$bookListHTML}
            </select>
        </div>
    </form>
</div>
EOT;
}

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "View Textbook Info";
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