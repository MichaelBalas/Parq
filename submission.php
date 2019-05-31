<?php 
//Ensure user is logged in
require 'loggedInCheck.inc.php'; 
// Include config file
require_once "DB_config.php";
// Include S3 bucket name, access keys and library
$awsAccessKey = "XXplaceholderXX";
$awsSecretKey = "XXplaceholderXX";
$bucketName = "XXplaceholderXX";
require 'S3.php';
$s3 = new S3($awsAccessKey, $awsSecretKey);

// Define variables and set to empty values
$title = $description = $weeklyprice = $spotsavailable = $latitude = $longitude = $imageurl = $videourl = "";
// Define error variables and set to empty values
$title_err = $description_err = $weeklyprice_err = $spotsavailable_err = $location_err = $imageurl_err = $videourl_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	// Validate parking spot title
	if (empty(trim($_POST['title']))) {
		$title_err = "Please enter a title for your parking spot.";
	} else if (strlen($_POST['title']) > 50) {
		$title_err = "Title cannot exceed 50 characters.";
	} else {
		$title = $_POST['title'];
	}

	// Validate description, if used
	if (strlen($_POST['description']) > 500) {
		$description_err = "Description cannot exceed 500 characters.";
	} else {
		$description = $_POST['description'];
	}

	// Validate weekly price
	if (preg_match('/^\d{1,4}(.\d{1,2})?$/', cleanPrice($_POST['weeklyprice']))) {
		$weeklyprice = round(floatval(cleanPrice($_POST['weeklyprice'])), 2);
	} else if (empty(trim(cleanPrice($_POST['weeklyprice'])))) {
		$weeklyprice_err = "Please specify the price of your parking spot.";
	} else if (preg_match('/^\d+(.\d{3,})?$/', cleanPrice($_POST['weeklyprice']))) {
		$weeklyprice_err = "Up to two decimal values are permitted.";
	} else if (preg_match('/^\d{4,}(.\d*)?$/', cleanPrice($_POST['weeklyprice']))) {
		$weeklyprice_err = "Weekly price cannot exceed $9999.99.";
	} else {
		$weeklyprice_err = "Invalid price.";
	}

	// Validate spots available
	if (empty(trim($_POST['spotsavailable']))) {
		$spotsavailable_err = "Please specify the number of available parking spots.";
	} else if (preg_match('/^[1-9][0-9]*$/', $_POST['spotsavailable'])) {
		$spotsavailable = $_POST['spotsavailable'];
	} else {
		$spotsavailable_err = "Invalid number of spots available.";
	}

	// Validate Latitude
	if (empty(trim($_POST['latitude']))) {
		$location_err = "Please enter latitude of parking spot.";
	} else if (floatval($_POST['latitude']) > 90 || floatval($_POST['latitude']) < -90) {
		$location_err = 'Latitude must be between -90 and 90.';
	} else if (preg_match('/^(\-)?\d+(.\d{0,3})$/', $_POST['latitude'])) {
		$location_err = 'Latitude must have at least four decimal points.';
	} else if (preg_match('/^(\-)?\d+(.\d{4,})$/', $_POST['latitude'])) {
		$latitude = floatval($_POST['latitude']);
	} else {
		$location_err = "Invalid latitude.";
	}

	// Validate Longitude
	if (empty(trim($_POST['longitude']))) {
		$location_err = "Please enter longitude of parking spot.";
	} else if (floatval($_POST['longitude']) > 180 || floatval($_POST['longitude']) < -180) {
		$location_err = 'Longitude must be between -180 and 180.';
	} else if (preg_match('/^(\-)?\d+(.\d{0,3})$/', $_POST['longitude'])) {
		$location_err = 'Longitude must have at least four decimal points.';
	} else if (preg_match('/^(\-)?\d+(.\d{4,})$/', $_POST['longitude'])) {
		$longitude = floatval($_POST['longitude']);
	} else {
		$location_err = "Invalid longitude.";
	}

	// Validate parking image url
	// If user selects file to upload
	if (!empty($_FILES['imageurl']['name'])) {
		$check = getimagesize($_FILES['imageurl']['tmp_name']);
		if ($check === false) {
			$imageurl_err = "File is not an image.";
		} else if ($_FILES['imageurl']['size'] > 5000000) {
			$imageurl_err = "Sorry, your file is too large. Max Limit = 5MB.";
		} else {
			// Get file extension of image file (e.g. jpg, png, etc.)
			$extension = pathinfo($_FILES['imageurl']['name'], PATHINFO_EXTENSION);
			// Hash file name with SHA-1 cryptographic hash function to give unique name (i.e. prevent overwrites)
			$filehash = sha1_file($_FILES['imageurl']['tmp_name']);
			$filename = $filehash . "." . $extension;
			$ok = $s3->putObjectFile($_FILES['imageurl']['tmp_name'], $bucketName, $filename, S3::ACL_PUBLIC_READ);
			if ($ok) {
				$imageurl = 'https://s3.amazonaws.com/' . $bucketName . '/' . $filename;
				echo '<p>File upload successful: <a href="' . $imageurl . '">' . $imageurl . '</a></p><img src="' . $imageurl . '" />';
			} else {
				echo 'Oops! There was an error uploading your file. Please try again later...';
			}
		}
	}

	// If user selects file to upload
	if (!empty($_FILES['videourl']['name'])) {
		echo '<script>alert("here we are");</script>';
		$check = getvideosize($_FILES['videourl']['tmp_name']);
		if ($check === false) {
			$videourl_err = "File is not a video.";
		} else if ($_FILES['videourl']['size'] > 5000000) {
			$videourl_err = "Sorry, your file is too large. Max Limit = 5MB.";
		} else {
			$videourl = "uploads/" . basename($_FILES["videourl"]["name"]);
			if (!(move_uploaded_file($_FILES['videourl']['tmp_name'], $imageurl))) {
				echo "Oops! There was an error uploading your file. Please try again later."; 
			}
		}
	}


	// Check input errors before inserting in database
	// Also make sure they agree to terms and conditions
	if (empty($title_err) && empty($description_err) && empty($weeklyprice_err) && empty($spotsavailable) && empty($location_err) && empty($imageurl_err) && empty($videourl_err)) {
		// Prepare an insert statement
		$sql = "INSERT INTO parkings (usersID, title, description, weeklyprice, latitude, longitude, imageurl, videourl) VALUES (:usersID, :title, :description, :weeklyprice, :latitude, :longitude, :imageurl, :videourl)";
		if ($stmt = $pdo -> prepare($sql)) {
			// Bind variables to the prepared statement as parameters
			$stmt -> bindValue('usersID', $_SESSION['usersID']);
			$stmt -> bindValue(':title', $title);
			//$stmt -> bindValue(':description', $description);
			$stmt -> bindValue(':description', empty($description) ? null : $description);
			$stmt -> bindValue(':weeklyprice', $weeklyprice);
			//$stmt -> bindValue(':spotsavailable', $spotsavailable);
			$stmt -> bindValue(':latitude', $latitude);
			$stmt -> bindValue(':longitude', $longitude);
			$stmt -> bindValue(':imageurl', empty($imageurl) ? null : $imageurl);
			$stmt -> bindValue(':videourl', empty($videourl) ? null : $videourl);

			//Attempt to execute the prepared statement
			if ($stmt -> execute()) {
				// Redirect to login page
				header("Location: http://{$_SERVER['HTTP_HOST']}/search.php");
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

} //end of post

// Remove any dollar signs from price if user adds them
function cleanPrice($value) {
	return preg_replace('/&#36;/', '', $value);
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Create</title>
	<link rel="stylesheet" type="text/css" href="CSS/main.css">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body onload="getLocation()">
	<header>
		<h1 class="logo">par<span id="last-letter">q</span></h1>
		<input type="checkbox" id="nav-toggle" class="nav-toggle">
		<nav>
			<ul>
				<li><a href="search.php">find</a></li>
				<li class="inactive">create</li>
				<li><a href="logout.php">logout</a></li>
				<li><a href="login.php">login</a></li>
				<li><a href="register.php">register</a></li>
			</ul>
		</nav>
		<label for="nav-toggle" class="nav-toggle-label">
			<span></span>
		</label>
	</header>
	<h1 class="title">Mark Your Spot.</h1>
	<p class="description">Please complete the details below to make your listing available.</p>
	<form class="register" name="Submission" onsubmit="return validateCreation()" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
		<div class="row">
			<label for="title">Title<span class="last-star">*</span></label>
			<?php echo '<input type="text" name="title" id="title" placeholder="Name your spot (max 50 chars)" required value="'. htmlspecialchars($_POST['title']) . '">'?>
			<br/><span class="error"><?php echo $title_err;?></span>
		</div>
		<div class="row">
			<label for="desc">Description</label>
			<textarea name="description" id="desc" placeholder="Include any relevant information (max 500 chars)." maxlength="500"><?php echo htmlspecialchars($_POST['description']);?></textarea>
			<br/><span class="error"><?php echo $description_err;?></span>
		</div>
		<div class="row">
			<label for="set-price">Weekly Price<span class="last-star">*</span></label>
			<?php echo '<input type="number" name="weeklyprice" id="set-price" placeholder="$" step="0.01" min="0" required value="'. htmlspecialchars($_POST['weeklyprice']) . '">'?>
			<br/><span class="error"><?php echo $weeklyprice_err;?></span>
		</div>
		<div class="row">
			<label for="set-spots">Spots Available<span class="last-star">*</span></label>
			<?php echo '<input type="number" name="spotsavailable" id="set-spots" placeholder="Number of remaining parking spots" step="1" min="1" required value="'. htmlspecialchars($_POST['spotsavailable']) . '">'?>
			<br/><span class="error"><?php echo $spotsavailable_err;?></span>
		</div>
		<div class="row">
			<fieldset>
				<legend>Location<span class="last-star">*</span></legend>
				<label class="loc-label" for="lat">Latitude</label>
				<?php echo '<input type="number" name="latitude" id="lat" step="any" placeholder="-90 to 90" required value="'. htmlspecialchars($_POST['latitude']) . '">'?>
				<label class="loc-label" for="lon">Longitude</label>
				<?php echo '<input type="number" name="longitude" id="lon" step="any" placeholder="-180 to 180" required value="'. htmlspecialchars($_POST['longitude']) . '">'?>
			</fieldset>
			<span class="error"><?php echo $location_err;?></span>
		</div>
		<div class="row">
			<label for="img">Upload Image</label>
			<input type="file" name="imageurl" accept="image/*" id="img">
			<br/><span class="error"><?php echo $imageurl_err;?></span>
		</div>
		<!--
		<div class="row">
			<label for="video">Upload Video</label>
			<input type="file" name="videourl" accept="video/*" id="video">
			<br/><span class="error"></span>
		</div>
	-->
		<button type="submit" class="submit">Submit</button>
	</form>

	<!-- FOOTER -->
	<footer>
		<p>&copy; 2019 Balas Enterprises &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
	</footer>
	<script src="JS/createSpot.js"></script>
</body>
</html>
