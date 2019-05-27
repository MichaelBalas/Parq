<?php
require 'loggedInCheck.inc.php'; 

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
		unset($_SESSION['loggedIn']);
		header("Location: http://{$_SERVER['HTTP_HOST']}/login.php");
		exit();
	}
?>

<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" type="text/css" href="CSS/main.css">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
	<header>
		<h1 class="logo">par<span id="last-letter">q</span></h1>
		<input type="checkbox" id="nav-toggle" class="nav-toggle">
		<nav>
			<ul>
				<li><a href="search.php">find</a></li>
				<li><a href="submission.php">create</a></li>
				<li class="inactive">logout</li>
				<li><a href="login.php">login</a></li>
				<li><a href="register.php">register</a></li>
			</ul>
		</nav>
		<label for="nav-toggle" class="nav-toggle-label">
			<span></span>
		</label>
	</header>

	<h1 class="title">Logout.</h1>
	<p class="description">Are you sure you want to logout? You will no longer be able to submit parking spots or leave reviews.</p>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<div class="row">
			<input type="submit" name="logout" value="Logout" class="submit">
		</div>
	</form>
	<!-- FOOTER -->
	<footer>
		<p>&copy; 2019 Balas Enterprises &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
	</footer>
</body>
</html>
