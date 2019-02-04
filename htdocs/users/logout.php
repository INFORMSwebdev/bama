<?php
	//initialize the session
	session_start();
	
	//unset all session variables
	$_SESSION = array();
	
	//destroy the session
	session_destroy();
	
	//redirect to login page
	header("Location: ../users/login.php");
	
	//halt execution of this script after the redirect
	die;
?>