<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 3/10/2019
 * Time: 12:17 AM
 */
//require the init file
require_once '../../init.php';

//get the college id
$collegeId = filter_input_array(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$content = '';

$college = new College($collegeId);

$content .= <<<EOT
<div class="card">
    <div class="card-header">
        <h2 class="display2">{$name}</h2>
    </div>
    <div class="card-body"> 
        <h3>Publisher</h3>
        <p>{$pub}</p>
        <div class="btn-group">
            <a role="button" class="btn btn-warning mr-3" href="/software/edit.php?id={$soft->id}">Edit this Software</a>
            <button id="id_{$soft->id}" name="courseDelete" type="submit" class="btn btn-danger btn-software-delete">Delete this Software</button>
        </div>
    </div>
</div>
EOT;

$customJS = <<<EOT
$(function() {
    
});
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['js'][] = array( 'text' => $customJS );
$page_params['page_title'] = "View Software Details";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//$page_params['css'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css' );
//$page_params['js'][] = array( 'url' => 'https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js' );
//$page_params['js'][] = array( 'text' => $customJS );
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