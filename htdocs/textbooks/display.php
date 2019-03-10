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

$content = '';

if($bookId){
    //get book info to display
    $book = new Textbook($bookId);
    $name = $book->Attributes['TextbookName'];
    $authors = $book->Attributes['Authors'];
    $pub = $book->Attributes['TextbookPublisher'];

    //display info about the book
    $content .= <<<EOT
<div class="card">
    <div class="card-header" id="cardHeader">
        <h2 class="display2">{$name}</h2>
    </div>
    <div class="card-body">
        <h3 class="display3">Author(s)</h3>
        <p>{$authors}</p>
        <h3 class="display3">Publisher</h3>
        <p>{$pub}</p>
        <div class="btn-group">
            <a role="button" class="btn btn-warning mr-3" href="/textbooks/edit.php?id={$book->id}">Edit this Textbook</a>
            <button id="id_{$book->id}" name="textbookDelete" type="submit" class="btn btn-danger btn-textbook-delete">Delete this Textbook</button>
        </div>
    </div>
</div>
EOT;

}
else {
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

    $content .= <<<EOT
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

$customJS = <<<EOT
$(function() {
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
$page_params['js'][] = array( 'text' => $customJS );
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