<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/14/2019
 * Time: 4:10 PM
 */

//load the init script
require_once '../../init.php';

$content = '';

//check if the session variable indicating a successful request for access
if(isset($_SESSION['registerSuccess']) && is_numeric($_SESSION['registerSuccess'])){
    //ensure session variable containing the pending user information is present
    if(isset($_SESSION['registerInput']) && count($_SESSION['registerInput']) == 5){
        $userName = htmlspecialchars($_SESSION['registerInput'][0]);
        $firstName = htmlspecialchars($_SESSION['registerInput'][1]);
        $lastName = htmlspecialchars($_SESSION['registerInput'][2]);
        $instId = $_SESSION['registerInput'][3];
        //get the institution name from this id to display
        if (is_numeric($instId) && $instId > 0 ) {
            $inst = new Institution($instId);
            $instName = $inst->Attributes['InstitutionName'];
        }
        else {
            $instName = "(Did not specify or chose 'other,' see Justification.)";
        }
        $comments = htmlspecialchars($_SESSION['registerInput'][4]);
        $content = <<<EOT
<div class="flex-column">
    <h1>INFORMS Analytics &amp; OR Education Database</h1>
    <p>Thank you, {$firstName}, for requesting access to the Analytic and Operations Research Education Database.</p>
    <p>Your request has been sent to the INFORMS administrators and is pending approval. You should receive an email containing the submitted information soon, if you haven't already received it.</p>
    <p>You will also find the submitted information below for a quick review.</p>
</div>
<div class="flex-column">
    <h2>Information Submitted:</h2>
    <p>Username: {$userName}</p>
    <p>First Name: {$firstName}</p>
    <p>Last Name: {$lastName}</p>
    <p>Institution: {$instName}</p>
    <p>Justification: {$comments}</p>
</div>
EOT;
    }
    else {
        //expected session variable not set
        $content = <<<EOT
<div class="flex-column">
    <div class="alert alert-danger" role="alert"> 
        <p>Something unexpected went wrong. Please contact <a href="mailto:webdev@mail.informs.org">webdev@mail.informs.org</a>.</p>
    </div>
</div>
EOT;
    }
}
else {
    //redirect to index
    header('Location: /index.php');
    die;
}

//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = 'Thank You';
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();