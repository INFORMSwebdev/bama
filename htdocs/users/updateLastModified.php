<?php
/**
 * Created by PhpStorm.
 * User: dwirth
 * Date: 3/7/2019
 * Time: 9:19 AM
 */

require_once '../../init.php';

$Token = filter_input( INPUT_GET, 'Token' );
if (!$Token) die( "Missing required parameter: Token" );
$error_msg = '';
$Institution = null;
try {
    $Institution = Institution::getInstitutionByToken( $Token );
}
catch (Exception $e) {
    $error_msg = $e->getMessage();
}

if ($error_msg) {
    $content = <<<EOT
<p>An error was encountered: $error_msg</p>
<p>If you used a link contained in email that we sent to you, please make sure you 
are using the entire link -- you might have inadvertently cut off the "Token" value that 
is required for this process to work.</p>
EOT;

}
else {
    $Institution->update( 'LastModifiedDate', date("Y-m-d H:i:s", time()));
    // now let's set up a new token
    $salt = "Time is a great teacher, but unfortunately it kills all its pupils";
    $NewToken = md5( $salt . time() . $Institution->id );
    $Institution->update( 'Token', $NewToken );
    $content = <<<EOT
<p>Thank you, we have marked your institution's data as up-to-date. Please return at any 
time to edit data as necessary.</p>
EOT;
}


$page_params = [];
$page_params['content'] = $content;
$page_params['page_title'] = "INFORMS Analytics &amp; OR Education Database - ADMIN - Add Institution";
$page_params['admin'] = TRUE;
$wrapper = new wrapperBama($page_params);
$wrapper->html();