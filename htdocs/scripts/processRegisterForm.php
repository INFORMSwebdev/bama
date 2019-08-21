<?php
//include the init.php file
require_once '../../init.php';

//ensure we are processing only on a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //set up local variables to hold the trimmed inputs
    $user = trim($_POST['Username']);
    $firstName = trim($_POST['FirstName']);
    $lastName = trim($_POST['LastName']);
    $instId = trim($_POST['inst']);
    $comments = trim($_POST['Comments']);

    //check the validity of inputs
    $results = validateInputs($user, $firstName, $lastName, $instId);
    //check for errors before continue processing
    if($results['errors'] == TRUE){
        //set up the error message and pass the input back to the form page so the user doesn't have to re-input everything
        $_SESSION['registerErrors'] = $results;

        $_SESSION['editMessage']['success'] = FALSE;
        $errorString = '';
        if(!empty($results['usernameError'])){
            $errorString .= 'Username Error: ' . $results['usernameError'];
        }

        if(!empty($results['passwordError'])){
            if(empty($errorString)){
                $errorString .= 'Password Error: ' . $results['passwordError'];
            }
            else {
                $errorString .= '<br/>Password Error: ' . $results['passwordError'];
            }
        }

        if(!empty($results['confirmError'])){
            if(empty($errorString)){
                $errorString .= 'Password Confirmation Error: ' . $results['confirmError'];
            }
            else {
                $errorString .= '<br/>Password Confirmation Error: ' . $results['confirmError'];
            }
        }

        if(!empty($results['institutionError'])){
            if(empty($errorString)){
                $errorString .= 'Institution Error: ' . $results['institutionError'];
            }
            else {
                $errorString .= '<br/>Institution Error: ' . $results['institutionError'];
            }
        }

        $_SESSION['editMessage']['text'] = $errorString;
        $_SESSION['registerInput'] = array( $user, $firstName, $lastName, $instId, $comments );

        //redirect to the register page
        header('Location: /users/register.php' );
        die;
    }

    //anonymous user has requested to become and institution admin
    # required inputs = Email (Username), First Name, Last Name, Institution
    # optional inputs = Justification (Comments)

    //add record to pending_users table
    $pendingUserId = PendingUser::create(
        array(
            'Username' => $user,
            'FirstName' => $firstName,
            'LastName' => $lastName,
            'InstitutionId' => $instId,
            'Comments' => $comments
        )
    );

    if ($pendingUserId) {
        //notify INFORMS admin a user requested access
        $InstitutionName = '(not specified)'; // default value
        if ($instId) {
            $Institution = new Institution( $instId);
            if ($Institution->valid) $InstitutionName = $Institution->Attributes['InstitutionName'];
        }
        $link = WEB_ROOT."admin/pendingUsers.php";
        $e_params = [];
        $e_params['to'] = ADMIN_EMAIL;
        $e_params['subject'] = "Analytics and Operations Research Education Database - New User Request";
        $e_params['body_html'] = <<<EOT
<p>The Analytics &amp; OR Education Database system has received a new user request.</p>
First Name: $firstName<br/>
Last Name: $lastName<br/>
Username (Email Address): $user<br/>
Comments: $comments<br/>
Institution: $InstitutionName</p>
<p>You can review this request at <a href="$link">$link</a>.</p>
EOT;
        $email = new email($e_params);
        $email->send();
    }

    //make sure we aren't passing unwanted session variables around
    unset($_SESSION['registerErrors']);

    //indicate successful pending user creation
    $_SESSION['registerSuccess'] = $pendingUserId;
    //pass the user input to the thank you page, which will decide what to display
    $_SESSION['registerInput'] = array( $user, $firstName, $lastName, $instId, $comments );

    //redirect anon user to a Thank You For Registering Page
    header('Location: /users/thankyou.php' );
    die;
}

/**
 * Validate inputs that were entered
 *
 * @author Dan Herold
 * @param string $email The input from the email address (username) field
 * @param string $firstName The input from the password field
 * @param string $lastName The input from the confirm password field
 * @param int $inst The InstitutionId that this user will be an admin of, from the institution select field
 * @return mixed[] The first item indicates if there were errors; Array full of error strings OR an array with the email address, first name, last name, and the institutionID the user will be an admin of.
 */
function validateInputs($email, $firstName, $lastName, $inst){
    //set up the variables to hold any possible errors that would result from the user input
    $username_err = $firstName_err = $lastName_err = $institution_err = '';

    //validate the email address/username
    if(empty($email)){
        $username_err = 'No email address was supplied.';
    } else {
        //check if username is already taken
        if(User::usernameExists($email)) {
            //username already exists
            $username_err = 'Email address already exists in the system, please use a different one.';
        }
    }

    //validate the first name
    if(empty($firstName)){
        $firstName_err = 'No first name was supplied.';
    }

    //validate the last name
    if(empty($lastName)){
        $lastName_err = 'No last name was supplied.';
    }

    //validate institution field has something selected
    //if(empty($inst)){
    if(!isset($inst)){
        $institution_err = "No institution selected.";
    } else if( !is_numeric($inst)){
        $institution_err = "Valid institution must be selected. InstitutionId passed was non-numeric.";
    }

    //check to see if we have any errors in the inputs
    if(empty($username_err) && empty($firstName_err) && empty($lastName_err) && empty($institution_err)){
        //inputs passed were valid
        return array('errors' => false, 'Username' => $email, 'FirstName' => $firstName, 'LastName' => $lastName,
            'institution' => $inst);
    }
    else {
        //inputs passed were not valid
        return array('errors' => true, 'usernameError' => $username_err, 'passwordError' => $firstName_err,
            'confirmError' => $lastName_err, 'institutionError' => $institution_err);
    }
}