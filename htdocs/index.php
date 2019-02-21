<?php
//load the init.php file, which has a session_start() and also sets up path constants; not to mention it's autoload
//lets not forget that there is also the error settings in init.php!
require_once '../init.php';

# ToDo: remove this testing stuff before actual deployment!
if (isset($_GET['testing'])) {
    $_SESSION['loggedIn'] = 41;
}

# ToDo: add in check for messages to display that come from other pages/scripts

//check if user is logged in, if not then redirect them to the login page; GET string is only used for testing purposes
# ToDo: remove the GET string from this test before actual use
if ((!isset($_SESSION["loggedIn"]) || $_SESSION["loggedIn"] != true)) {
    //user is not logged in
    # ToDo: implement the anon display page here
     # Currently it is a placeholder.
    $content = <<<EOT
<div class="jumbotron">
    <h1>Welcome to the Analytics and Operations Research Eduction Program Site!</h1>
    <p class="lead">You will find information on many different Analytics and Operations Research (O.R.) programs offered by universities around the U.S.</p>
    <hr class="my-4" />
    <a class="btn btn-primary" href="#" name="analytics" role="button">Display All Analytics Programs</a>
    <a class="btn btn-primary" href="#" name="or" role="button">Display All O.R. Programs</a>
    <a class="btn btn-primary" href="#" name="all" role="button">Display All Programs</a>
</div>
<div class="row" id="programList">
    <!-- program info goes here when returned via ajax. Might have to add d-block to the class to circumvent flex display. -->
</div>
EOT;
    # ToDo: figure out how to update page content via ajax and implement the button functionality that way
} else {
    //user is already logged in, get their userID from the session
    $user = new User($_SESSION['loggedIn']);
    $userName = $user->Attributes['Username'];

    # ToDo: implement the logged in display when I figure out what should be displayed.
     # Currently it is a placeholder.
    $content = <<<EOT
<div class="jumbotron">
	<h1 class="display-4">Welcome $userName!</h1>
	<p class="lead">Message can go here about system</p>
	<hr class="my-4" />
	<a class="btn btn-primary" href="#" name="mine" role="button">Display my programs</a>
	<a class="btn btn-primary" href="#" name="analytics" role="button">Display All Analytics programs</a>
	<a class="btn btn-primary" href="#" name="or" role="button">Display All O.R. programs</a>
	<a class="btn btn-primary" href="#" name="all" role="button">Display all programs</a>
</div>
<div class="row" id="programList">
    <!-- program info goes here when returned via ajax. Might have to add d-block to the class to circumvent flex display. -->
</div>
EOT;
    # ToDo: figure out how to update page content via ajax and implement the button functionality that way
}



//set up utility links?
# ToDo: Ask Dave what these are
//$util_links = '<a href="/profile.php">Home</a>';

//create the parameters to pass to the wrapper
$page_params = array();
$page_params['loggedIn'] = TRUE;
$page_params['content'] = $content;
$page_params['page_title'] = "Programs Dashboard";
$page_params['site_title'] = "Analytics & Operations Research Education Program Listing";
$page_params['site_url'] = 'https://bama-dan.informs.org/index.php';
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
?>
