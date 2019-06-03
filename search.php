<?php
session_start(); // Important for accessing session variable

// Include config file
require_once "DB_config.php";
$filtered_results = array();
// Processing form data when form is submitted
if (isset($_GET['submit'])) {
	$test = $_GET['location'];
	if (!empty(trim($_GET['location']))) {
		$search_lat = trim(explode(',', $_GET['location'])[0]); // get first comma separated value (i.e. lat)
		$search_lon = trim(explode(',', $_GET['location'])[1]); // get second comma separated value (i.e. lon)
	}
	$sth = $pdo->prepare("SELECT * FROM parkings");
	$sth -> execute();
	// Fetch all of the rows in the result set
	$results = $sth -> fetchAll();
	foreach($results as $parking) {
		if (!empty(trim($_GET['name'])) && !stripos($_GET['name'], $parking['title'])) {
			// If title is not empty and not similar to specified name, continue searching...
			continue;
		} 
		if (!empty(trim($_GET['minprice'])) && ($_GET['minprice'] > $parking['weeklyprice'])) {
			// If minprice is not empty and greater than parking spot, continue searching...
			continue;
		}
		if (!empty(trim($_GET['maxprice'])) && ($_GET['maxprice'] < $parking['weeklypricece'])) {
			// If maxprice is not empty and less than than parking spot, continue searching...
			continue;
		}
		if (!empty(trim($_GET['location'])) && ($_GET['distance'] != '>15km')) {
			// If location and max distance is specified (>15km means any location works)
			$distance = distance($search_lat, $search_lon, $parking['latitude'], $parking['longitude']);
			// and distance from parking spot to location is greater than desired, continue searching...
			if ($distance > preg_replace('/[^0-9]/', '', $_GET['distance'])) {
				continue;
			}
		}
		$filtered_results[] = $parking;
	}
	if (empty($filtered_results)) {
		// Display no results found messsage
		$_SESSION['search_output'] = "No parking spots found that meet the criteria.";
	} else {
		// Move the filtered results array to the SESSION variable
		$_SESSION['search_output'] = $filtered_results;
	}
	// Move the user to the new results page and echo out the data there
	header("Location: http://{$_SERVER['HTTP_HOST']}/results.php");
}
// Only returns distance between lat/lon values in kilometers
function distance($lat1, $lon1, $lat2, $lon2) {
	if (($lat1 == $lat2) && ($lon1 == $lon2)) {
		return 0;
	} else {
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$km = $dist * 60 * 1.853159;
		return $km;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Find</title>
	<link rel="stylesheet" type="text/css" href="CSS/main.css">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<!--
	<script type="text/javascript" src="JS/geolocation.js"></script>-->
</head>
<body>
	<header>
		<h1 class="logo">par<span id="last-letter">q</span></h1>
		<input type="checkbox" id="nav-toggle" class="nav-toggle">
		<nav>
			<ul>
				<li class="inactive">find</li>
				<li><a href="submission.php">create</a></li>
				<li><a href="logout.php">logout</a></li>
				<li><a href="login.php">login</a></li>
				<li><a href="register.php">register</a></li>
			</ul>
		</nav>
		<label for="nav-toggle" class="nav-toggle-label">
			<span></span>
		</label>
	</header>
	<h1 class="title">Find The Perfect Spot.</h1>
	<p class="description">Please select any relevant filters.</p>
	<form class="search" name="Search" onsubmit="return validateSearch()" method="GET" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<div class="row">
			<label for="byName">Name</label>
			<input type="text" name="name" id="name" placeholder="Name of spot...">
		</div>
		<div class="row">
			<label for="location">Location</label>
			<button type="button" id="location-button" onclick="getLocation()"><i class="material-icons">person_pin_circle</i></button>
			<input type="text" name="location" id="location" placeholder="Latitude, Longitude">
		</div>
		<div class="row">
			<label for="distance">Parking Spots Within...</label>
			<select id="distance" name="distance">
				<option value="1km">1 kilometer</option>
				<option value="5km">5 kilometers</option>
				<option value="10km">10 kilometers</option>
				<option value="15km">15 kilometers</option>
				<option value=">15km">>15 kilometers</option>
			</select>
		</div>
		<div class="row">
			<fieldset>
				<legend>Weekly Price Range</legend>
				<label class="price-label" for="min">Min</label>
				<input id="min" name="minprice" type="number" placeholder="$" step="0.01" min="0">
				<label class="price-label" for="max">Max</label>
				<input id="max" name="maxprice" type="number" placeholder="$" step="0.01" min="0">
			</fieldset>
		</div>
		<div class="row">
			<label for="rate">Minimum Rating</label>
			<select id="rate" name="rate">
				<option value="1star">1 Star</option>
				<option value="2stars">2 Stars</option>
				<option value="3stars">3 Stars</option>
				<option value="4stars">4 Stars</option>
				<option value="5stars">5 Stars</option>
			</select>
			<!--
			<div class="rate">
				<p class="min-rate">Minimum Rating</p>
				<input type="radio" id="star5" name="rate" value="5">
				<label for="star5" title="text">5 stars</label>
				<input type="radio" id="star4" name="rate" value="4">
				<label for="star4" title="text">4 stars</label>
				<input type="radio" id="star3" name="rate" value="3">
				<label for="star3" title="text">3 stars</label>
				<input type="radio" id="star2" name="rate" value="2">
				<label for="star2" title="text">2 stars</label>
				<input type="radio" id="star1" name="rate" value="1">
				<label for="star1" title="text">1 star</label>
			</div>
		-->
		</div>
		<button type="submit" name="submit" class="submit">Search</button>
		
	</form>

  	<!-- FOOTER -->
    <footer>
      <p>&copy; 2019 Balas Enterprises &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
    </footer>
    <script src="JS/search.js"></script>
</body>
</html>
