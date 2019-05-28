<?php
session_start();
// If they are not logged in, redirect to login page.
if (!isset($_SESSION['loggedIn'])) {
	header("Location: http://{$_SERVER['HTTP_HOST']}/login.php");
	exit();
}
?>