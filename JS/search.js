
/* ~~~ ############### ~~~ */
/* ~~~ Geolocation API ~~~ */
/* ~~~ ############### ~~~ */
// Set options for geolocation
var options = {
	enableHighAccuracy: true, // Get high accuracy lat/long values
	timeout: 10000, //timeout at 5s
	maximumAge: 0
}

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError, options);
    } else { // If not Supported
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
	var latlon = position.coords.latitude + ", " + position.coords.longitude; 
	document.getElementById("location").value = latlon;
}

function showError(error) {
	switch(error.code) {
		case error.POSITION_UNAVAILABLE:
			alert("Location information is unavailable.")
			break;
		case error.TIMEOUT:
			alert("Location request timed out.")
			break;
	    case error.UNKNOWN_ERROR:
	    	alert("An unknown error occurred.")
	    	break;
	    }
}
/* ~~~ ############### ~~~ */
/* ~~~ ############### ~~~ */





/* ~~~ ############### ~~~ */
/* ~~~ Form Validation ~~~ #/
/* ~~~ ############### ~~~ */
// Use browser's built-in functionality to sanitize or "escape" any user-provided input before displaying
// Prevents XSS (HTML Injection) attacks
function escapeHTML(str) {
	var div = document.createElement('div');
	div.appendChild(document.createTextNode(str));
	return div.innerHTML;
}
function validateSearch() {
	var location = document.forms["Search"]["location"];
	location.value = escapeHTML(location.value);
	var minPrice = document.forms["Search"]["minprice"];
	minPrice.value = escapeHTML(minPrice.value);
	var maxPrice = document.forms["Search"]["maxprice"];
	maxPrice.value = escapeHTML(maxPrice.value);
	// Validate each of the form's fields
	if (!isLocationValid(location)) {
		location.focus();
		return false;
	} else if (!arePricesValid(minPrice, maxPrice)) {
		return false;
	} else {
		return true;
	}
}

function isLocationValid(location) {
	return true;
}

function arePricesValid(minPrice, maxPrice) {
	// At least one whole number followed by (optional) decimal point and either 1 or 2
	// decimal values for both min and max prices. min price must be less than maxprice
	// If both values are inputted
	min = Number(minPrice.value); // Convert from string (after escaping HTML) to number
	max = Number(maxPrice.value); // Convert from string (after escaping HTML) to number
	if ((/^\d+(.\d{1,2})?$/.test(min)) && (/^\d+(.\d{1,2})?$/.test(max)) && (min < max)) {
		return true;
	} else if ((/^\d+(.\d{1,2})?$/.test(min)) && (maxPrice.value.length == 0)) { // If only min value is inputted
		return true;
	} else if ((/^\d+(.\d{1,2})?$/.test(max)) && (minPrice.value.length == 0)) { // If only max value is inputted 
		return true;
	} else if (/^\d+.\d{3,}$/.test(min)) {
		alert('Up to 2 decimal values are permitted');
		minPrice.focus();
		return false;
	} else if (/^\d+.\d{3,}$/.test(max)) {
		alert('Up to 2 decimal values are permitted');
		maxPrice.focus();
		return false;
	} else if (min < 0) {
		alert('Minimum Price must be non-negative.');
		minPrice.focus();
		return false;
	} else if (max <= 0) {
		alert('Maximum Price must be greater than zero.');
		maxPrice.focus();
		return false;
	} else if (min >= max && maxPrice.value.length != 0) {
		alert('Minimum price must be less than maximum price.')
		return false;
	}  else {
		alert('Invalid weekly price range');
		return false;
	}
}













