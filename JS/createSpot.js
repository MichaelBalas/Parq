
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
    } else { // Not Supported
        alert("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
	if (document.getElementById("lat".value == null)) {
		document.getElementById("lat").value = position.coords.latitude;
	} 
	if (document.getElementById("lon".value == null)) {
		document.getElementById("lon").value = position.coords.longitude;
	}
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

function validateCreation() {
	var title = document.forms["Submission"]["title"];
	title.value = escapeHTML(title.value);
	var description = document.forms["Submission"]["desc"];
	desc.value = escapeHTML(desc.value);
	var price = document.forms["Submission"]["price"];
	price.value = escapeHTML(price.value);
	var latitude = document.forms["Submission"]["latitude"];
	latitude.value = escapeHTML(latitude.value);
	var longitude = document.forms["Submission"]["longitude"];
	longitude.value = escapeHTML(longitude.value);
	var image = document.forms["Submission"]["upload_image"];
	image.value = escapeHTML(image.value);
	var video = document.forms["Submission"]["upload_video"];
	video.value = escapeHTML(video.value);
	// Validate each of the form's fields
	if (!isTitleValid(title)) {
		title.focus();
		return false;
	} else if (!isDescriptionValid(description)) {
		description.focus();
		return false;
	} else if (!isPriceValid(price)) {
		price.focus();
		return false;
	} else if (!isLocationValid(latitude, longitude)) {
		return false;
	} else if (!isImageValid(image)) {
		return false;
	} else if (!isVideoValid(video)) {
		return false;
	} else {
		return true;
	}
}

function isTitleValid(title) {
	if (title.value.length == 0) {
		alert('Please enter title.');
		return false;
	} else if (title.value.length < 3) {
		alert('Title should have at least 3 characters.');
		return false;
	} else {
		return true;
	}
}


function isDescriptionValid(description) {
	if (description.value == '') {
		return true;
	}
	if (description.value.length > 500) {
		alert('Description cannot exceed 500 characters. Current number: ' + description.value.length);
		return false;
	} else {
		return true;
	}
}

function isPriceValid(price) {
	// At least one whole number followed by (optional) decimal point and either 1 or 2
	// decimal values. 
	if (/^\d+(.\d{1,2})?$/.test(price.value)) {
		return true;
	} else if (price.value.length == 0) {
		alert('Please enter weekly price.');
		return false;
	} else if (/^\d+(.\d{3,})?$/.test(price.value)) {
		alert('Up to 2 decimal values are permitted');
		return false;
	} else {
		alert('Invalid weekly price.');
		return false;
	}
}

function isLocationValid(latitude, longitude) {
	if (latitude.value == '') {
		alert('Please enter latitude.');
		latitude.focus();
		return false;
	}
	if (latitude.value > 90 || latitude.value < -90) {
		alert('Invalid latitude.');
		latitude.focus();
		return false;
	} if (longitude.value == '') {
		alert('Please enter longitude.');
		longitude.focus();
		return false;
	}
	if (longitude.value > 180 || longitude.value < -180) {
		alert('Invalid longitude.');
		longitude.focus();
		return false;
	}
	return true;
}

function isImageValid(image) {
	if (image.value == '') {
		return true;
	}
	var fileExtension = image.value.split('.')[1];
	if (fileExtension.match(/^(gif|jpeg|png|svg|x-icon)$/i)) { // Web safe image MIME types (case-insensitive)
		return true;
	} else {
		var fileName = image.value.replace(/^.*[\\\/]/, ''); // handles both "/" OR "\" in paths 
		alert(fileName + ' is not a supported image file type.');
		return false;
	}
}

function isVideoValid(video) {
	if (video.value == '') {
		return true;
	}
	var fileExtension = video.value.split('.')[1];
	if (fileExtension.match(/^(mp4|mov|avi|wmv|flv)$/i)) { // Web safe video MIME types (case-insensitive)
		return true;
	} else {
		var fileName = video.value.replace(/^.*[\\\/]/, ''); // handles both "/" OR "\" in paths
		alert(fileName + ' is not a supported video file type.');
		return false;
	}
}






































