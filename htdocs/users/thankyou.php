<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/14/2019
 * Time: 4:10 PM
 */

//load the init script
require_once '../../init.php';

# ToDo: add in stuff for this page to display info returned from the processRegisterForm
 # this will only be redirected to on a successful anon registration attempt

//check if the session variable indicating a successful request for access
if(isset($_SESSION['registerSuccess']) && is_numeric($_SESSION['registerSuccess'])){
    //ensure session variable containing the pending user information is present
    if(isset($_SESSION['registerInput']) && count($_SESSION['registerInput']) == 5){
        $userName = htmlspecialchars($_SESSION['registerInput'][0]);
        $firstName = htmlspecialchars($_SESSION['registerInput'][1]);
        $lastName = htmlspecialchars($_SESSION['registerInput'][2]);
        $instId = $_SESSION['registerInput'][3];
        //get the institution name from this id to display
        $inst = new Institution($instId);
        $instName = $inst->Attributes['InstitutionName'];
        $comments = htmlspecialchars($_SESSION['registerInput'][3]);
        $content = <<<EOT
<div class="row">
    <h1>Thank you, {$firstName}, for requesting access to the Analytic and Operations Research Education Database.</h1>
    <p>Your request has been sent to the INFORMS administrators and is pending approval. You should receive an email containing the submitted information soon, if you haven't already received it.</p>
    <p>You will also find the submitted information below for a quick review.</p>
</div>
<div class="row">
    <h2>Information Submitted:</h2>
    <p>Username: {$userName}</p>
    <p>First Name: {$firstName}</p>
    <p>Last Name: {$lastName}</p>
    <p>Institution: {$instName}</p>
    <p>Justification: {$comments}</p>
</div>
EOT;
    } else {
        //expected session variable not set
        # ToDo: Redirect to error page? Ask Dave how he usually handles errors like this & implement something.
    }
}
//set page parameters up
# ToDo: is the user truly logged in at this point? I don't think so. Ask Dave what this page parameter is really doing.
$page_params['loggedIn'] = TRUE;
$page_params['content'] = $content;
$page_params['page_title'] = 'Thank You';
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
$page_params['show_title_bar'] = FALSE;
# ToDo: figure out the best way to use this admin parameter, maybe change up the bamaWrapper class some? Maybe it's OK
 # for right now. OPTIONAL!
//do not display the usual header/footer
$page_params['admin'] = TRUE;
$page_params['active_menu_item'] = 'home';
//put custom/extra css files, if used
//$page_params['css'][] = array("url" => "");
//put custom/extra JS files, if used
//$page_params['js'][] = array("url" => "");
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();