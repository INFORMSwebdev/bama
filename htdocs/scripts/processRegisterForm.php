<?php
//include the init.php file
require_once '../../init.php';

//ensure we are processing only on a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //set up local variables to hold the trimmed inputs
    $user = trim($_POST['Username']);
    $firstName = trim($_POST['FirstName']);
    $lastName = trim($_POST['LastName']);
    $instId = trim($_POST['Institution']);
    $comments = trim($_POST['Comments']);

    //check the validity of inputs
    $results = validateInputs($user, $firstName, $lastName, $instId);
    //check for errors before continue processing
    if($results['errors'] == TRUE){
        //set up the error message and pass the input back to the form page so the user doesn't have to re-input everything
        $_SESSION['registerErrors'] = $results;
        $_SESSION['registerInput'] = array( $user, $firstName, $lastName, $instId, $comments );

        //redirect to the register page
        header('Location: ' . USER_DIR . 'register.php' );
        die;
    }

    //check session variables to see what kind of register form was submitted
    if ((isset($_SESSION['admin']) && $_SESSION['admin'] == TRUE) || isset($_GET['testing'])) {
        //INFORMS admin has attempted to set up a new user account
        //go ahead and make it and associate the new account as the admin of the specified institution
        # required inputs = Email (Username), First Name, Last Name, Institution
        # optional inputs = Comments

        $newUserId = User::create( array( 'Username' => $user, 'FirstName' => $firstName, 'LastName' => $lastName,
            'Comments' => $instId ) );
        $newUser = new User($newUserId);
        $newUser->assignToInstitution($instId);

        //report successful user creation and institution assignment via session variable
        //make sure the registerErrors session variable gets reset
        unset($_SESSION['registerErrors']);
        //since the account was successfully created, we don't want the user to have to clear out the fields themselves
        unset($_SESSION['registerInput']);

        //set a new session variable up
        $_SESSION['registerSuccess'] = $newUserId;
        # ToDo: figure out what I want to pass back to the register page (if anything) to display to the INFORMS admin
         # current ideas: newUserId (definitely), FirstName? LastName? Comments? InstitutionId?
         # We don't need to pass back the input since it was already accepted and processed through, but would we
         # want to pass anything back to display?

        //redirect back to register page so admin can add more users if desired
        header('Location: ' . USER_DIR . 'register.php' );
        die;
    } else {
        //anonymous user has requested to become and institution admin
        # required inputs = Email (Username), First Name, Last Name, Institution
        # optional inputs = Justification (Comments)

        //add record to pending_users table
        $pendingUserId = PendingUser::create( array( 'Username' => trim($_POST['Username']),
            'FirstName' => trim($_POST['FirstName']),
            'LastName' => trim($_POST['LastName']),
            'InstitutionId' => trim($_POST['Institution']),
            'Comments' => trim($_POST['Comments']) ) );

        //notify INFORMS admin a user requested access
        # ToDo: figure out how to send a notification and what is needed for it
         # from, to, subject, body?

        //make sure we aren't passing unwanted session variables around
        unset($_SESSION['registerErrors']);

        //indicate successful pending user creation
        $_SESSION['registerSuccess'] = $pendingUserId;
        # ToDo: do we want to display the users' entered info on this thank you page? Some info? Confer w/ Dave
        //pass the user input to the thank you page, which will decide what to display
        $_SESSION['registerInput'] = array( $user, $firstName, $lastName, $instId, $comments );

        //redirect anon user to a Thank You For Registering Page
        header('Location: ' . USER_DIR . 'thankyou.php' );
        die;
    }
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
    if(empty($inst)){
        $institution_err = "No institution selected.";
    } else if( !is_numeric($inst)){
        $institution_err = "Valid institution must be selected. InstitutionId passed was non-numeric.";
    }
    # ToDo: put in more validation checks to see if the institution selected actually exists in the system

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