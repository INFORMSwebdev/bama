<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/27/2019
 * Time: 10:30 AM
 */
//require the init file
require_once '../../init.php';

//check if user is logged in
if(!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true){
    //user is not logged in
    $_SESSION['logoutMessage'] = 'You must be logged in to add institutions to the system.';
    header('Location: ../users/login.php');
    die;
}

//get current user
$user = new User($_SESSION['loggedIn']);

//get list of states
$states = User::getStateList();
$stateListHelper = array();
foreach($states as $s){
    $stateListHelper[] = array('text' => $s['name'], 'value' => $s['abbr']);
}
//pass the name/value pairs to the file to get the generated HTML for a select list
include_once('/common/classes/optionsHTML.php');
$stateListHTML = optionsHTML($stateListHelper);

//display the form for adding institution info to the user
$content = <<<EOT
<div class="jumbotron bg-info text-white">
    <form action="../scripts/processInstitutionAddForm.php" method="POST">
        <div class="form-row">
            <h3>Institution Details</h3>
        </div>
        <div class="form-row"> 
            <label for="institutionName">Name</label>
            <input type="text" class="form-control" name="institutionName" id="institutionName" placeholder="Name of institution" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="institutionAddress">Address</label>
            <input type="text" class="form-control" name="institutionAddress" id="institutionAddress" placeholder="Address of institution" aria-describedby="addressHelp" required />
            <p id="addressHelp">Please use the format: Number, Street Name, Suite (if applicable)</p>
        </div>
        <div class="form-row">
            <label for="city">City</label>
		    <input type="text" class="form-control" name="city" id="city" placeholder="City where institution is located" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="state">State</label>
            <select class="form-control" name="state" id="state" required>
		        $stateListHTML
            </select>
        </div>
        <br />
        <div class="form-row"> 
            <label for="zip">Zip Code</label>
            <input type="text" class="form-control" name="zip" id="zip" placeholder="Zip code" required />
        </div>
        <br />
        <div class="form-row"> 
            <label for="region">Region</label>
            <input type="text" class="form-control" name="region" id="region" placeholder="Geographical region where institution is located" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="phone">Phone</label>
            <input type="text" class="form-control" name="phone" id="phone" placeholder="E.g. 555-555-5555 or (555) 555-5555" />
        </div>
        <br />
        <div class="form-row"> 
            <label for="email">Email</label>
            <input type="text" class="form-control" name="email" id="email" placeholder="Email address to contact institution" aria-describedby="emailHelp" />
            <p id="emailHelp">Only valid email addresses will be accepted (e.g. name@organization.com)</p>
        </div>
        <!--<br />-->
        <div class="form-row"> 
            <label for="access">Access</label>
            <input type="text" class="form-control" name="access" id="access" placeholder="Website for the institution" aria-describedby="accessHelp" />
            <p id="accessHelp">Only valid URLs will be accepted</p>
        </div>
        <!--<br />-->
        <div class="form-row">
            <button class="btn btn-warning" type="submit" name="add" value="add">Submit New Institution</button>
        </div>
        <!--<br />-->
        <div class="form-row">
            <p class="lead">This institution will not be added to the system until the changes are approved by an INFORMS administrator.</p>
        </div>
    </form>
</div>
EOT;

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['content'] = $content;
$page_params['page_title'] = "Add New Institution";
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