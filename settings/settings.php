<?php
	
	$user = 'edu';
	$pass = 'kw*Xix1AS4O!';
	$dbName = 'analytic_education_dev';
	$host = 'rds';
	$charset = 'utf8mb4';
	$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";
	$options = [
		PDO::ATTR_ERRMODE 				=> PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE 	=> PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES 		=> false
	];
?>