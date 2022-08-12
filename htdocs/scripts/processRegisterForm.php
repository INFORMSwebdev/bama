<?php
//include the init.php file
require_once '../../init.php';

//set up local variables to hold the trimmed inputs
$Username = filter_input( INPUT_POST, 'Username' );
$FirstName = filter_input(INPUT_POST, 'FirstName' );
$LastName = filter_input(INPUT_POST, 'LastName' );
$Comments = filter_input( INPUT_POST, 'Comments' );
$inst = filter_input( INPUT_POST, 'inst' );
$g_recaptcha_response = filter_input( INPUT_POST, 'g-recaptcha-response' );

//check the validity of inputs
$results = (object)[];
$results->errors = [];

if (empty($Username)) $results->errors[] = 'The Email (Username) field is required.';
elseif (!filter_var( $Username, FILTER_VALIDATE_EMAIL )) $results->errors[]= 'The Email Address provided is not valid.';
elseif (User::usernameExists($Username)) $results->errors[] = 'Email address already exists in the system, please use a different one.';

if (empty($FirstName)) $results->errors[] = 'The First Name field is required.';

if (empty($LastName)) $results->errors[] = 'The Last Name field is required.';

if (!is_numeric($inst) && $inst !== "Other") $results->errors = "Valid institution must be selected. InstitutionId passed was non-numeric or not &quot;Other&quot;.";

if (empty($g_recaptcha_response)) $results->errors[] = 'The captcha is required.';
elseif (!(new recaptcha2)->verify($g_recaptcha_response)) $results->errors[] = 'The captcha response was not valid.';

//check for errors before continue processing
if (count($results->errors)) {
    $results->data = ['Username'=>$Username,'FirstName'=>$FirstName,'LastName'=>$LastName,'inst'=>$inst,'Comments'=>$Comments];
    $_SESSION['registerResponse'] = serialize($results);
    header('Location: /users/register.php');
    die;
}
else {
    //anonymous user has requested to become an institution admin
    # required inputs = Email (Username), First Name, Last Name, Institution
    # optional inputs = Justification (Comments)

    //if Other was selected for institution, need to pass 0 instead of 'Other' to PendingUser instance
    if ($inst == 'Other') $inst = 0;  // TO-DO: modify PendingUser to handle non-numeric instId without error so this workaround can be removed

    //add record to pending_users table
    $pendingUserId = PendingUser::create(
        array(
            'Username' => $Username,
            'FirstName' => $FirstName,
            'LastName' => $LastName,
            'InstitutionId' => $inst,
            'Comments' => $Comments
        )
    );

    if ($pendingUserId) {
        //notify INFORMS admin a user requested access
        $InstitutionName = 'Other'; // default value
        if ($inst) {
            $Institution = new Institution( $inst);
            if ($Institution->valid) $InstitutionName = $Institution->Attributes['InstitutionName'];
        }
        $link = WEB_ROOT."admin/pendingUsers.php";
        $e_params = [];
        $e_params['to'] = ADMIN_EMAIL;
        $e_params['subject'] = "Analytics and Operations Research Education Database - New User Request";
        $e_params['body_html'] = <<<EOT
<p>The Analytics &amp; OR Education Database system has received a new user request.</p>
First Name: $FirstName<br/>
Last Name: $LastName<br/>
Username (Email Address): $Username<br/>
Comments: $Comments<br/>
Institution: $InstitutionName</p>
<p>You can review this request at <a href="$link">$link</a>.</p>
EOT;
        $email = new email($e_params);
        $email->send();
    }

    //indicate successful pending user creation
    $_SESSION['registerSuccess'] = $pendingUserId;
    //pass the user input to the thank you page, which will decide what to display
    $_SESSION['registerInput'] = array( $Username, $FirstName, $LastName, $inst, $Comments );

    //redirect anon user to a Thank You For Registering Page
    header('Location: /users/thankyou.php' );
    die;
}
