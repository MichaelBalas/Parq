<?php

// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect them to the welcome page
if(isset($_SESSION["loggedIn"]) && $_SESSION['loggedIn'] == true) {
	header("Location: http://{$_SERVER['HTTP_HOST']}/submission.php");
	exit();
}

// Include config file
require_once "DB_config.php";

//Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Check if username is empty
	if (empty(trim($_POST['username']))) {
		$username_err = "Please enter your username.";
	} else {
		$username = trim($_POST['username']);
	}

	// Check if password is empty
	if (empty(trim($_POST["password"]))) {
		$password_err = "Please enter your password.";
	} else {
		$password = trim($_POST['password']);
	}

	// Validate credentials
	if (empty($username_err) && empty($password_err) && empty($login_err)) {
		// Prepare a SELECT statement
		$sql = "SELECT usersID, username, passwordhash FROM users WHERE username = :username";
		if ($stmt = $pdo -> prepare($sql)) {
			// Bind variables to the prepared statement as parameters
			$stmt -> bindParam(":username", trim($_POST['username']));
			// Attempt to execute the prepared statement
			if ($stmt -> execute()) {
				// Check if username exists, if yes then verify password
				if ($stmt -> rowCount() == 1) {
					if ($row = $stmt -> fetch()) {
						$usersID = $row['usersID'];
						$username = $row['username'];
						$passwordhash = $row['passwordhash'];
						if (password_verify($password, $passwordhash)) {
							// Password is correct, so start a new session
							session_start();
							// Store data in session variables
							$_SESSION['loggedIn'] = true;
							$_SESSION['usersID'] = $usersID;
							$_SESSION['username'] = $username;
							// Redirect user to create page
							header("Location: http://{$_SERVER['HTTP_HOST']}/submission.php");
						} else {
							// Password invalid: display error message (they may also be using a wrong, yet existing username)
							$login_err = "Incorrect username or password.";
						}
					}
				} else {
					// Username doesn't exist: display error message
					$username_err = "No account found with that username.";
				}
			} else {
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
		// Close statement
		unset($stmt);
	}
	// Close connection
	unset($pdo);
}
?>
<!DOCTYPE html>
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
				<li><a href="logout.php">logout</a></li>
				<li class="inactive">login</li>
				<li><a href="register.php">register</a></li>
			</ul>
		</nav>
		<label for="nav-toggle" class="nav-toggle-label">
			<span></span>
		</label>
	</header>

	<h1 class="title">Login.</h1>
	<p class="description">Please login to your account.</p>
	<span style="color:red; font-size:12pt;"><?php echo $login_err;?></span><br/>
	<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<div class="row">
			<label for="username">Username</label>
			<?php echo '<input type="text" name="username" id="username" required value="'. htmlspecialchars($_POST['username']) . '">'?>
			<br/><span class="error"><?php echo $username_err;?></span>
		</div>
		<div class="row">
			<label for="password">Password</label>
			<!-- Don't want to save password after error -->
			<input type="password" name="password" id="password" required>
			<br/><span class="error"><?php echo $password_err;?></span>
		</div>
		<input type="submit" name="login" value="Login" class="submit">
	</form>
	<div class="container-signin">
		<p>Don't have an account? <a href="register.php">Register</a></p>
	</div>
	<!-- FOOTER -->
	<footer>
		<p>&copy; 2019 Balas Enterprises &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
	</footer>
</body>
</html>
