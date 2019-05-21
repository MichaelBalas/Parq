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
function validateInput() {
	var name = document.forms["Register"]["name"];
	name.value = escapeHTML(name.value);
	var email = document.forms["Register"]["email"];
	email.value = escapeHTML(email.value);
	var pswd = document.forms["Register"]["pswd"];
	pswd.value = escapeHTML(pswd.value);
	var pswd_repeat = document.forms["Register"]["pswd-repeat"];
	pswd_repeat.value = escapeHTML(pswd_repeat.value);
	var profile_pic = document.forms["Register"]["profile_pic"];
	profile_pic.value = escapeHTML(profile_pic.value);
	var agree_to_terms = document.getElementById("agree-to-terms")
	agree_to_terms.value = escapeHTML(agree_to_terms.value);
	// Validate each of the form's fields
	if (!isNameValid(name)) {
		name.focus();
		return false;
	} else if (!isEmailValid(email)) {
		email.focus()
		return false;
	} else if (!isPasswordValid(pswd)) {
		pswd.focus();
		return false;
	} else if (!doPasswordsMatch(pswd, pswd_repeat)) {
		pswd_repeat.focus();
		return false;
	} else if (!isProfilePicValid(profile_pic)) {
		return false;
	} else if (agree_to_terms.checked == false) {
		alert('You must agree to the terms and conditions to register.');
		agree_to_terms.focus()
		return false;
	} else {
		return true;
	}
}

function isNameValid(name) {
	// Valid name: Starts with character b/w aA-zZ followed by others, until reaching a space,
	// and repeat for last name. Includes hyphenated names or names with apostraphes 
	// (e.g. O'Donnell and Levi-Strauss)
	if (/^[a-zA-Z'\-]+\s[a-zA-Z'\-]+$/.test(name.value)) {
		return true;
	} else if (name.value.length === 0) {
		alert("Please enter first and last name.");
		return false;
	} else if (/\d/.test(name.value)) { // Name contains a digit
		alert('Name should not contain digits.');
		return false;
	} else if (name.value.length < 3 || name.value.length > 50) {
		alert("Name should be between 3 and 50 characters");
		return false;
	} else if (!(/\s/.test(name.value))) { // If full name doesn't contain a space
		alert('Missing space between first and last names');
		return false;
	} else {
		alert('Invalid name.');
		return false;
	}
}

function isEmailValid(email) {
	// Complex email standards: follows RFC 5322, works for 99.99% of all email addresses
	if (/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(email.value)) {
		return true;
	} else {
		alert('Invalid email address');
		return false;
	}
}

function isPasswordValid(pswd) {
	if (pswd.value.length < 6) {
		alert("Password must be at least 6 characters long.");
		return false;
	} else if (pswd.value.length > 120) {
		alert("Password cannot be longer than 120 characters.");
		return false;
    // Passwords can have any combination of digits, letters and/or symbols.
	} else {
		return true;
	}
}

function doPasswordsMatch(pswd, pswd_repeat) {
	if (pswd.value === pswd_repeat.value) { // No type converstion is performed
		return true;
	} else {
		alert('Passwords do not match.');
		return false;
	}
}

function isProfilePicValid(profile_pic) {
	if (profile_pic.value == '') {
		return true;
	}
	var fileExtension = profile_pic.value.split('.')[1];
	if (fileExtension.match(/^(gif|jpeg|png|svg|x-icon)$/i)) { // Web safe image MIME types
		return true;
	} else {
		var fileName = profile_pic.value.replace(/^.*[\\\/]/, ''); // handles both "/" OR "\" in paths 
		alert(fileName + ' is not a supported image file type.');
		return false;
	}
}
/* ~~~ ############### ~~~ */
/* ~~~ ############### ~~~ */




















