<?php
/**
 * Created by PhpStorm.
 * User: dherold
 * Date: 2/6/2019
 * Time: 3:45 PM
 */
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../../init.php';

//check if user is logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] != true) {
    //set up a message to display on the login page
    $_SESSION['logoutMessage'] = 'Please log in to view your profile page.';
    //redirect to login page so user can log in
    header('Location: login.php');
    //don't want the script to keep executing after a redirect
    die;
}
if (isset($_GET['testing'])) {
    $_SESSION['loggedIn'] = 41;
}

$subject = 'Analytics%20and%20O.R.%20Education%20Database%20-%20Institution%20Detail%20Update%20Request';
$contentHelp = "<p class='lead'>If you want to change the institution you administrate, please email the request to <a href='mailto:educationresources@informs.org?subject=$subject'>educationresources@informs.org</a> using the provided subject line.</p>";

$user = new User($_SESSION['loggedIn']);
$userName = $user->Attributes['Username'];
$firstName = $user->Attributes['FirstName'];
$lastName = $user->Attributes['LastName'];
$joinDate = $user->Attributes['CreateDate'];
$comments = $user->Attributes['Comments'];
$insts = $user->getInstitutionAssignments();
$instList = '<ul class="list-group list-group-horizontal">';
foreach($insts as $ins){
    $helper = new Institution($ins);
    $instList .= "<li class='list-group-item'><a class='' href='../institutions/display.php?id=$ins' role='button'>{$helper->Attributes['InstitutionName']}</a></li>";
}
$instList .= '</ul>';

$content = '';

//check for messages to display
if(isset($_SESSION['registerMessage'])){
    //add message to content
    $content = "<div class='alert alert-info'><p>{$_SESSION['registerMessage']}</p></div>";
    //clear out session variable after it is used
    $_SESSION['registerMessage'] = null;
}

//set the page content to be displayed by the wrapper class
$content .= <<<EOT
<div class="flex-column">
    <div class="card">
        <div class="card-header"> 
            <h2 class="display2">Welcome, {$firstName}!</h2>
        </div>
        <div class="card-body"> 
            <h2>My Profile</h2>
            <h3>Email Address/Username</h3>
            <p>{$userName}</p>
            <h3>First Name</h3>
            <p>{$firstName}</p>
            <h3>Last Name</h3>
            <p>{$lastName}</p>
            <h3>Administrator of Institution</h3>
            {$instList}
            {$contentHelp}
        </div>
        <div class="card-footer"> 
            <a class="btn btn-primary" href="editProfile.php" role="button">Edit My Info</a>
        </div>
    </div>
</div>
EOT;

//set page parameters up
$page_params['content'] = $content;
$page_params['page_title'] = "My Profile";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = WEB_ROOT . 'index.php';
//wrapper class to pass all the content and params to
$wrapper = new wrapperBama($page_params);
//display the content
$wrapper->html();