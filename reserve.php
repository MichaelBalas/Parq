<?php
session_start(); // Important for accessing session variable
$parkingsID = $_GET['parking'];
$result = array();
foreach($_SESSION['search_output'] as $parking) {
	if ($parkingsID == $parking['parkingsID']) {
		$result = $parking;
		break;
	}
}
if (empty($result)) {
	echo 'Oops! Something went wrong... Please try again later.';
}
?>
<!DOCTYPE html>
<head>
	<title>Parking Spot</title>
	<!-- Mapbox -->
	<script src='https://api.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.js'></script>
	<link rel="stylesheet" type="text/css" href="CSS/main.css">
	<link href='https://api.mapbox.com/mapbox-gl-js/v0.50.0/mapbox-gl.css' rel='stylesheet' />
</head>
<script>
	function showForm() {
		var ele = document.getElementById("showForm")
		ele.parentNode.removeChild(ele);
		document.getElementById("row-review").innerHTML = "<form id='reviewForm' name='Review' method='POST'>"
		+ "<div class='row'>" + "<label for='rating'>Rating<span class='last-star'>*</span></label>"
		+ "<select id='rating' name='rating' required>" + "<option value='' disabled selected hidden>Leave your rating...</option>"
		+ "<option value='1'>1 Star</option>" + "<option value='2'>2 Stars</option>" + "<option value='3'>3 Stars</option>"
		+ "<option value='4'>4 Stars</option>" + "<option value='5'>5 Stars</option>" + "</select></div>"
		+ "<div class='row';'>" + "<label for='review'>Review<span class='last-star'>*</span></label>"
		+ "<textarea name='review' id='review' placeholder='Please limit your review to 500 characters.' required></textarea>"
		+ "<br/><div id='errorplaceholder'></div></div>" + "<input type='button' class='submit' onclick='submitReviewForm()' value='Submit Review'></form>";
	}

	function insertReviewResponse() {
		if (this.status == 200) {
			response = JSON.parse(this.response);
			if (response.status == false) {
				document.getElementById("errorplaceholder").innerHTML = "<span class='error'><b>Error:</b> " + response.message + "</span>";
			} else {
				document.getElementById("temp").innerHTML = '<div class="rev-container" itemprop="review" itemscope itemtype="http://schema.org/Review"><img src=' + response.profilepic + 'alt="Avatar"><p class="name"><span itemprop="author">' + response.reviewer + '</span></p><div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating"><!-- Provided for unambiguity: lowest is assumed to be one --><meta itemprop="worstRating" content="1"><!-- Provided for unambiguity: highest is assumed to be five --><meta itemprop="bestRating" content="5"><p class="stars"><span itemprop="ratingValue">' + response.rating + ' Stars</span></p><p class="review"><span itemprop="description">' + response.review + '</span></p></div></div>';
			}
		} else {
			alert(this.status);
		}
	}

	function submitReviewForm() {
		request = new XMLHttpRequest();
		request.open("POST", "submit_review.php");
		request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		request.onload = insertReviewResponse;
		var selector = document.getElementById("rating");
		request.send("rating=" 
			+ encodeURIComponent(selector[selector.selectedIndex].value)
			+ "&review="
			+ encodeURIComponent(document.getElementById("review").value));
	}

</script>
<body class="Parking">
	<header>
		<h1 class="logo">par<span id="last-letter">q</span></h1>
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

	<div itemscope itemtype="http://schema.org/Place">
		<div class="left-half">
			<!-- itemprop labels properties of an item -->
			<img itemprop="photo" src="<?php echo $result['imageurl']?>" class="main_pic">
		</div>
		<div class="right-half">
			<h1 class="parking-title"><span itemprop="name"><?php echo $result['title']?></span></h1>
			<p id="by">By: <a href="#">John Smith</a></p>
			<p id="price">Weekly Price: <strong></strong>$<?php echo $result['weeklyprice']?></strong></p>
			<p id="spots">Spots Available: <strong><span itemprop="maximumAttendeeCapacity"><?php echo $result['title']?></span></strong></p> 
			<p id="parking-desc_head">Description</p>
			<p id="parking-desc"><span itemprop="description"><?php echo $result['description']?></span></p>
			<!--<img src="images/lux_map.png" alt="Parking Map" id="result_map">-->
			<div id="small-map"></div>
			<!-- In this case, the value of the item property is itself another item with its own set of properties. geo is an item of type GeoCoordinates, which has the properties longitude and latitude-->
			<div id="result_map_details" itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
				<p>Latitude: <?php echo $results['latitude']?></p>
				<p>Longitude: <?php echo $results['longitude']?></p>
				<p>Located: ???km away</p>
			</div>
		</div>
		<script>
			document.getElementById("result_map_details").insertAdjacentHTML('beforeend', '<meta itemprop="latitude" content="' + latitude + '" />')
			document.getElementById("result_map_details").insertAdjacentHTML('beforeend', '<meta itemprop="longitude" content="' + longitude + '" />')
		</script>
	<!-- Scorecard displaying aggregate user ratings in a histogram-like format -->
	<div id="scorecard" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
		<h1 class="user">User Rating</h1>
		<p class="average"><span itemprop="ratingValue">4.2</span> average based on <span itemprop="reviewCount">117</span> reviews.</p>
		<hr style="border: 3px solid #ccc">
		</div>
	</div>
	<div class="reviews">
		<h1 class="user">Top Customer Reviews</h1>
		<div class="rev-container" itemprop="review" itemscope itemtype="http://schema.org/Review">
			<img src="images/reviewer_1.jpg" alt="Avatar">
			<p class="name"><span itemprop="author">Elon Musk</span></p>
			<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating">
				<!-- Provided for unambiguity: lowest is assumed to be one -->
				<meta itemprop="worstRating" content="1">
				<!-- Provided for unambiguity: highest is assumed to be five -->
				<meta itemprop="bestRating" content="5">
				<p class="stars"><span itemprop="ratingValue">2 Stars</span></p>
				<p class="review"><span itemprop="description">Can't fit any of my falcon rockets. There aren't even any charging stations for my Tesla Roadster. The managers wouldn't let me drill under the garage.</span></p>
			</div>
		</div>
	</div>
	<div class="leave-review">
		<h1 class="user">Leave a Review!</h1>
		<div id="temp">
		<?php
		if (isset($_SESSION['loggedIn'])) {
			echo '<div id="row-review" class="row"><input id="showForm" type="button" value="Create" class="submit" onclick="showForm()"></div>';
		} else {
			echo '<div class="row"><p>You must be <a href="login.php">logged in</a> to leave a review.</p></div>';
		}
		?>
	</div>
	</div>
</div>
	<footer>
      <p>&copy; 2019 Balas Enterprises &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
    </footer>
    <script src="JS/parkingPage.js"></script>
</body>
</html>
