<?php
// Attempt to connect to MySQL database

/* Database credentials. */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'balasm');
define('DB_PASSWORD', 'password');
define('DB_NAME', 'comp4ww3');

try {
	$pdo = new PDO('mysql:host=' . DB_SERVER . ';dbname=' . DB_NAME, DB_USERNAME, DB_PASSWORD);
	// Set the PDO error mode to exception
	$pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
	echo "An Error Occurred!"; //user friendly message
	echo $ex -> getMessage();
	die();
}
?>