<?php
session_start(); // Important for accessing session variable
?>
<!DOCTYPE html>
<html>
<head>
	<title>Results</title>
	<link rel="stylesheet" type="text/css" href="CSS/main.css">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- MapBox -->
	<script src='https://api.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.js'></script>
	<link href='https://api.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.css' rel='stylesheet' />

</head>
<body class="Results">
	<header>
		<h1 class="logo">par<span id=last-letter>q</span></h1>
		<input type="checkbox" id="nav-toggle" class="nav-toggle">
		<nav>
			<ul>
				<li><a href="search.php">find</a></li>
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
	<div id="map"></div>
	<div class="right-results">
		<?php
		$display_results = '';
		if (!is_array($_SESSION['search_output'])) {
			$display_results = '<h1 class="results-title>' . $_SESSION['search_output'] . '</h1>';
		} else {
			echo '<h1 class="results-title">Results.</h1><p class="results-description">Additional fees apply. Taxes may be added.</p>';
			foreach($_SESSION['search_output'] as $parking) {
				echo '<div class="responsive-results"><div class="gallery"><a href="reserve.php?parking='. $parking['parkingsID'] .'"><img src="' . $parking['imageurl'] . '" width="600" height="400"></a><div class="price"><h4>$' . $parking['weeklyprice'] . '</h4></div><div class="desc"><h4>' . $parking['title'] . '</h4><p>??? spots available | ??? reviews | ??? Stars</p></div></div></div>';
			}
		}
		?>
	</div>
	<!-- FOOTER -->
    <footer class="results">
      <p>&copy; 2019 Balas Enterprises &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
    </footer>
    <script src="JS/results.js"></script>
</body>
</html>
