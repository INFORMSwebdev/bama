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
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true) {
    //set up a message to display on the login page
    $_SESSION['logoutMessage'] = 'Please log in to edit textbook information.';
    //redirect to login page so user can log in
    header('Location: login.php');
    //don't want the script to keep executing after a redirect
    die;
}

//get the textbook ID (if set) of the textbook to edit
$bookId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$user = new User($_SESSION['loggedIn']);
$userBooks = $user->getBookAssignments();

if($bookId){
    //if the bookId passed via the query string is NOT in this list, the user does NOT have permission to edit this page
    if (!in_array($bookId, $userBooks)) {
        //set up the message to be red
        $_SESSION['editMessage']['success'] = false;
        $_SESSION['editMessage']['text'] = 'You do not have permission to edit the specified textbook\'s information.';

        //redirect to index
        header('Location: /index.php');
        die;
    } else {
        //get book info to put in form
        $book = new Textbook($bookId);
        $name = $book->Attributes['TextbookName'];
        $authors = $book->Attributes['Authors'];
        $pub = $book->Attributes['TextbookPublisher'];

        //display form w/ info about the book
        $content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processTextbookEditForm.php" method="POST">
        <div class="form-row">
            <h3>Textbook Details</h3>
        </div>
        <div class="form-row"> 
            <label for="textbookName">Name</label>
            <input type="text" class="form-control" name="textbookName" id="textbookName" placeholder="Title of textbook" value="{$name}" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="textbookAuthors">Author(s)</label>
            <textarea class="form-control" name="textbookAuthors" id="textbookAuthors" aria-describedby="authorHelp">{$authors}</textarea>
            <p id="authorHelp">Please list the authors and separate them by either commas (,) or semi-colons (;).</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="textbookPublisher">Publisher</label>
            <input type="text" class="form-control" name="textbookPublisher" id="textbookPublisher" placeholder="Name of textbook publisher" value="{$pub}" />
        </div>
        <br />
        <div class="form-row">
            <input type="hidden" id="bookId" name="bookId" value="{$bookId}" />
            <button class="btn btn-warning mr-2" type="submit" name="edit" value="edit">Submit changes</button>
            <button class="btn btn-danger" type="submit" name="delete" value="delete">Delete This Textbook</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">These changes will not take effect until they have been approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;

    }
} else {
    //display a list of books to the user for them to select from THAT THEY HAVE PERMISSION TO EDIT
    $bookListHelper = array();
    foreach($userBooks as $prog){
        $bookListHelper[] = array('text' => $prog['TextbookName'], 'value' => $prog['TextbookId']);
    }
    //pass the name/value pairs to the file to get the generated HTML for a select list
    include_once('/common/classes/optionsHTML.php');
    $bookListHTML = optionsHTML($bookListHelper);

    $content = <<<EOT
<div class="flex-column">
    <h2>Edit Textbook Details</h2>
    <form action="display.php" method="get">
        <div class="form-group">
            <label for="Textbook">Select a Textbook to edit</label>
		    <select class="form-control" name="Textbook" id="Textbook" onchange="self.location='edit.php?id='+this.options[this.selectedIndex].value">
		        $bookListHTML
            </select>
        </div>
    </form>
</div>
EOT;
}
//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Edit Textbook";
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


