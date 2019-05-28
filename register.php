<?php
// Include config file
require_once "DB_config.php";
// Include S3 bucket name, access keys and library
$awsAccessKey = "XXplaceholderXX";
$awsSecretKey = "XXplaceholderXX";
$bucketName = "XXplaceholderXX";
require 'S3.php';
$s3 = new S3($awsAccessKey, $awsSecretKey);

//Define variables and set to empty values
$fullname = $email = $username = $password = $confirm_password = $profileurl = "";
$fullname_err = $email_err = $username_err = $password_err = $confirm_password_err = $profileurl_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Don't want to strip slashes or get rid of htmlspecialchars since they may form part of passwords or emails. SQL Injection prevented by prepared statements.

	// Validate full name
	if (empty(trim($_POST["fullname"]))) {
		// Check if fullname is empty
		$fullname_err = "Please enter your full name.";
	} else if (!preg_match('/(?=^.{0,255}$)^[a-zA-Z\'-]+\s[a-zA-Z\'-]+$/', $_POST["fullname"])) {
		// Check for a max 255 character string that contains only alpha characters and one space, as well as hyphens and apostraphes
		if (!preg_match("/\s/", $_POST["fullname"])) {
			// doesn't contain space
			$fullname_err = "Name missing space.";
		} else {
			// anything other error
			$fullname_err = "Invalid full name.";
		}
	} else {
		$fullname = trim($_POST['fullname']);
	}

	// Validate email
	if (empty(trim($_POST["email"]))) {
		// Check if email is empty
		$email_err = "Please enter your email.";
	} else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		// Check if email is invalid
		$email_err = "Invalid email address.";
	} else {
		// Check if email already exists in database
		$sql = "SELECT usersID FROM users WHERE email = :email";
		if ($stmt = $pdo -> prepare($sql)) {
			$stmt -> bindValue(':email', trim($_POST['email']));
			// Attempt to execute the prepared statement
			if ($stmt -> execute()) {
				if ($stmt -> rowCount() === 1) {
					$email_err = "This email is already being used.";
				} else {
					$email = trim($_POST['email']);
				}
			} else {
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
		// Close statement
		unset($stmt);
	}

	// Validate username
	if (empty(trim($_POST['username']))) {
		// Check if username is empty
		$username_err = "Please enter a username.";
		// No other checks required. No restrictions on username.
	} else {
		$sql = "SELECT usersID FROM users WHERE username = :username";
		if ($stmt = $pdo -> prepare($sql)) {
			$stmt -> bindValue(':username', trim($_POST['username']));
			// Attempt to execute the prepared statement
			if ($stmt -> execute()) {
				if ($stmt -> rowCount() === 1) {
					$username_err = "This username is already taken.";
				} else {
					$username = trim($_POST['username']);
				}
			} else {
				echo "Oops! Something went wrong. Please try again later.";
			}
		}
		// Close statement
		unset($stmt);
	}

	// Validate password
	if (empty(trim($_POST['password']))) {
		$password_err = "Please enter a password.";
	} else if (strlen($_POST['password']) < 6) {
		$password_err = "Password must have at least 6 characters.";
	} else {
		$password = trim($_POST['password']);
	}

	// Validate confirm password
	if (empty(trim($_POST['confirm_password']))) {
		$confirm_password_err = "Please confirm password.";
	} else {
		$confirm_password = trim($_POST["confirm_password"]);
		if (empty($password_err) && ($password != $confirm_password)) {
			$confirm_password_err = "Passwords did not match.";
		}
	}

	// Validate parking image url
	// If user selects file to upload
	if (!empty($_FILES['profileurl']['name'])) {
		$check = getimagesize($_FILES['profileurl']['tmp_name']);
		if ($check === false) {
			$imageurl_err = "File is not an image.";
		} else if ($_FILES['profileurl']['size'] > 5000000) {
			$imageurl_err = "Sorry, your file is too large. Max Limit = 5MB.";
		} else {
			// Get file extension of image file (e.g. jpg, png, etc.)
			$extension = pathinfo($_FILES['profileurl']['name'], PATHINFO_EXTENSION);
			// Hash file name with SHA-1 cryptographic hash function to give unique name (i.e. prevent overwrites)
			$filehash = sha1_file($_FILES['profileurl']['tmp_name']);
			$filename = $filehash . "." . $extension;
			$ok = $s3->putObjectFile($_FILES['profileurl']['tmp_name'], $bucketName, $filename, S3::ACL_PUBLIC_READ);
			if ($ok) {
				$profileurl = 'https://s3.amazonaws.com/' . $bucketName . '/' . $filename;
				echo '<p>File upload successful: <a href="' . $profileurl . '">' . $profileurl . '</a></p><img src="' . $profileurl . '" />';
			} else {
				echo 'Oops! There was an error uploading your file. Please try again later...';
			}
		}
	}

	// Check input errors before inserting in database
	// Also make sure they agree to terms and conditions
	if (empty($fullname_err) && empty($email_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($profileurl_err)) {
		// Prepare an insert statement
		$sql = "INSERT INTO users (fullname, email, username, passwordhash, profileurl) VALUES (:fullname, :email, :username, :passwordhash, :profileurl)";
		if ($stmt = $pdo -> prepare($sql)) {
			// Bind variables to the prepared statement as parameters
			$stmt -> bindValue(':fullname', $fullname);
			$stmt -> bindValue(':email', $email);
			$stmt -> bindValue(':username', $username);
			// Configure password hash and salt
			$stmt -> bindValue(':passwordhash', password_hash($password, PASSWORD_DEFAULT));
			$stmt -> bindValue(':profileurl', empty($profileurl) ? null : $profileurl);
			//Attempt to execute the prepared statement
			if ($stmt -> execute()) {
				// Redirect to login page
				header("Location: http://{$_SERVER['HTTP_HOST']}/login.php");
			} else {
				echo "Something went wrong. Please try again later.";
				echo $stmt -> error;
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
	<title>Register</title>
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
				<li><a href="login.php">login</a></li>
				<li class="inactive">register</li>
			</ul>
		</nav>
		<label for="nav-toggle" class="nav-toggle-label">
			<span></span>
		</label>
	</header>

	<h1 class="title">Create Account.</h1>
	<p class="description">Please fill in this form to create an account.</p>
	<form name="Register" onsubmit="return validateInput()" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<div class="row">
			<label for="name">Full Name<span class="last-star">*</span></label>
			<?php echo '<input type="text" name="fullname" id="name" placeholder="Enter first and last name" required value="'. htmlspecialchars($_POST['fullname']) . '">'?>
			<br/><span class="error"><?php echo $fullname_err;?></span>
		</div>
		<div class="row">
			<label for="email">Email<span class="last-star">*</span></label>
			<?php echo '<input type="email" name="email" id="email" placeholder="Enter email" required value="'. htmlspecialchars($_POST['email']) . '">'?>
			<br/><span class="error"><?php echo $email_err;?></span>
		</div>
		<div class="row">
			<label for="username">Username<span class="last-star">*</span></label>
			<?php echo '<input type="text" name="username" id="username" placeholder="Create username" required value="'. htmlspecialchars($_POST['username']) . '">'?>
			<br/><span class="error"><?php echo $username_err;?></span>
		</div>
		<div class="row">
			<label for="pswd">Password<span class="last-star">*</span></label>
			<input type="password" name="password" placeholder="Enter Password" id="pswd" required>
			<span class="error"><?php echo $password_err;?></span>
			<!-- Hidden class is only readable by screen readers (since placeholder text is not screen readable) -->
			<label for="pswd-repeat" class="visually-hidden">Repeat Password</label>
			<input type="password" name="confirm_password" placeholder="Repeat Password" id="pswd-repeat" required>
			<span class="error"><?php echo $confirm_password_err;?></span>
		</div>
		<div class="row">
			<label for="img">Upload Profile Picture</label>
			<input type="file" name="profileurl" id="img" accept="image/*"/>
			<br/><span class="error"><?php echo $profileurl_err;?></span>
		</div>
		<div class="terms">
			<label><input type="checkbox" name="accept" id="agree-to-terms" required>By creating an account you agree to our <a href="#">Terms & Conditions<span class="last-star">*</span></a></label>
		</div>
		<button type="submit" class="submit">Register</button>
	</form>
	<div class="container-signin">
		<p>Already have an account? <a href="login.php">Login</a></p>
	</div>
	<!-- FOOTER -->
	<footer>
		<p>&copy; 2019 Balas Enterprises &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
	</footer>
	<script src="JS/validateRegistration.js"></script>
</body>
</html>

