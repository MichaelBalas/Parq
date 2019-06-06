# Parq
## Purpose
An exercise for learning how to build the client and server components of a modern data-driven website using a variety of appropriate **Client-side** (HTML, CSS, JavaScript/jQuery) and **Server-side** (PHP, MySQL) Web technologies, as well as including modern approaches to Web programming (i.e. responsive/mobile Web design, dynamically generated content, deployment on cloud infrastructures, etc.).
## Introduction
In this repository, I have designed and fully implemented a website that allows users to search and reserve nearby parking spots. This **parking reservation service** is called **parq**. This site will:
* Allow users to register for accounts or login. The users can use the website as a parking owner or driver using the same account.
* Allow registered users to create a parking spot as an owner by entering information such as location, price, images, etc.
* Allow users to search for nearby parking spots using their geographical information (as well as other filters such as price, rating, etc.) and see the results on a map and paginated display. 
* Allow registered users to reserve parkings spots. The website displays information about the parking services and their reviews if the user selects a parking spot.
* Allow registered users to leave ratings and reviews.

This site is also functional for users with disabilities (e.g. using a screen-reader or text-based browser) and follows WCAG guidelines. It displays well on a desktop browser as well as a mobile browser, with responsive layouts for sizes ranging from 320 pixels wide to 1024 pixels wide, as well as anything larger. It should work reasonably well for users with low-bandwidth connections. Finally, this website includes protections against malicious user-entered data, including protection against cross-site scripting (XSS) attacks and SQL injection attacks. 
## Sitemap
#### Registration
![registration page](ex_img/registration.png)
A user registration page, containing a form in which users are asked to enter the information required to sign up for an account. All fields are validated on both the client- (HTML5 and JavaScript) and server-side (PHP). During registration, a random ≥80-bit salt is generated (to defeat hash/rainbow tables). The user's data is submitted to a PHP script which, after performing appropriate server-side validation, adds the data to the database. The username and *H(password, salt)* is stored (along with other information such as name and email) in the database, where *H* is a password hardening function that hashes the password many times (to slow down brute-force attacks).

#### Login
![login page](ex_img/login.png)
Here, the user supplies their username and purported password, which is then checked against the username, salt and hash in the database (i.e. check if *H(password, salt) = stored hash*). The PHP Sessions API is used for managing data accessibility. After logging in successfully, PHP creates a unique identifier for that particular session (a random string of 32 hexadecimal numbers), sends a cookie called **PHPSESSID** to the user to store the unique session identification string, and creates a file in a temporary directory on the server named after the unique identifer (prefixed by **sess_ ie sess_**). 

#### Parking Submission
![submission page](ex_img/submission.png)
A parking submission page, containing a form with which owners (i.e. only logged-in users) could submit a new parking service. The form has fields for the name of the spot, a description, weekly price, and its location as a pair of latitude-longitude coordinates. The form also allows owners to upload an image for the parking service. Users can set the location of the parking spot using the **HTML5 Geolocation API**. All fields are validated on both the client- (HTML5 and JavaScript) and server-side (PHP). The user's data is submitted to a PHP script which, after performing appropriate server-side validation, adds the data to the database. 

#### Search
![search page](ex_img/search.png)
A search page that allows drivers to search for parking spots using information about name, distance, price, and rating in a form. It includes a button that allows users to search based on their location using the Geolocation API to retrieve the user's location. The search form submits the search query to the web server and returns an appropriate dynamically generated results page.

#### Results (Dynamically Generated)
![results page pt1](ex_img/results-1.png) ![results page pt2](ex_img/results-2.png)
A results page showing the results of a search: 
1. On an embedded live map using JavaScript and the [MapBox API](https://www.mapbox.com/). The map shows markers with the search results on the map. When the user clicks the marker, a label appears with some information about the result, including a link to the individual item page. 
2. In a paginated and tabular format.

From the results map and/or table, users are able to link to a more detailed and dynamically generated screen for each parking spot.

#### Parking Reservation (Dynamically Generated)
![reservation page pt1](ex_img/reserve-1.png) ![reservation page pt2](ex_img/reserve-2.png)
A dynamically generated page for a parking spot, with information about its location on an embedded live map, as well as a list of all reviews and ratings that have been entered by users. Logged-in users are also able to submit a review and a rating for an individual parking, which is inserted into the database and displayed for future users. 

#### Logout
![login page](ex_img/login.png)
We need a way to logout so that those using the computer after us can't access private pages. Logging out clears the user data from the session and redirects the user to the login page. 

#### Responsive Header/Design
![responsive reservation page](ex_img/responsive_reservation.png)
An example of how the page displays on a mobile device. All of the navigation options are now enclosed within a hamburger menu. The orientation and size of the different DOM elements have shifted to accomodate a smaller and narrower screen. 

## Cloud Infrastructure
This project was deployed on a live website using Amazon Web Services (AWS). Specifically, I set up an Amazon Elastic Cloud Compute (EC2) virtual server running Linux. I used a *t2.micro* instance, which is more than powerful enough for this small-scale application and is included in the AWS Free Tier. I stored all user uploaded images/videos on an Amazon Simple Storage Service (S3) bucket. 
You will also need to install web server and database software on your virtual server. I used [Apache](https://httpd.apache.org/) (with mod_php installed) and [MySQL](https://www.mysql.com/). Make sure to enable SSL/TLS on your web server. I used [Let's Encrypt](https://letsencrypt.org/) to obtain my certificate and automatically set up SSL/TLS.
If you use Github or some other repository for storing your source code, it is **very important that you do not put a copy of your AWS password or access keys in the repository**: there are automated scripts that malicious parties use to scrape repositories for AWS credentials.

## Additional Information
### PHP Data Objects (PDO)
The new PHP Data Objects (PDO) API is used for generic database access. It is an Object-Oriented extension library that ships with PHP 5.1 or later. **PDO** provides a data-access abstraction layer: regardless of which database you're using, you use the same functions to issue queries and fetch data. You will require a **PDO driver for your database of choice**, for example, ```extension = php_pdo_mysql.dll``` for MySQL. PDO prepared statements are used to prevent SQL injections (still need to validate user input according to business rules before inserting into database). 
### SQL Database
Three tables are used: one for users, one for parking spots, and one for reviews. Below are examples of how each can be structured. 
##### Users Table 
<br />
| Field  | Data Type |
| ------------- | ------------- |
| id  | INT(11) UNSIGNED NOT NULL AUTO_INCREMENT |
| fullname  | TEXT NOT NULL  |
| email  | TEXT NOT NULL  |
| username  | TEXT NOT NULL  |
| passwordhash  | TEXT NOT NULL  |
| profileurl  | TEXT DEFAULT NULL  |
| pid  | INT(11) UNSIGNED NOT NULL  |
|**CONSTRAINT FOREIGN KEY** (pid) **REFERENCES** parkings(id) **ON DELETE CASCADE** |
| rid  | INT(11) UNSIGNED NOT NULL  |
|**CONSTRAINT FOREIGN KEY** (rid) **REFERENCES** reviews(id) **ON DELETE CASCADE** |
| **CONSTRAINT** pk_users **PRIMARY KEY** (id) |
##### Parkings Table 
<br />
| Field  | Data Type |
| ------------- | ------------- |
| id  | INT(11) UNSIGNED NOT NULL AUTO_INCREMENT |
| title  | TEXT NOT NULL  |
| description  | TEXT DEFAULT NULL  |
| weeklyprice  | DECIMAL(5,2) NOT NULL  |
| latitude  | DECIMAL(10,6) NOT NULL  |
| longitude  | DECIMAL(10,6) NOT NULL  |
| imageurl  | TEXT NOT NULL |
| videourl  | TEXT DEFAULT NULL |
| **CONSTRAINT** pk_parkings **PRIMARY KEY** (id) |
##### Reviews Table 
<br />
| Field  | Data Type |
| ------------- | ------------- |
| id  | INT(11) UNSIGNED NOT NULL AUTO_INCREMENT |
| rating  | INT(1) NOT NULL  |
| review  | TEXT NOT NULL  |
| **CONSTRAINT** pk_reviews **PRIMARY KEY** (id) |
### Metadata & Microdata
Metadata fields are added in the header for Facebook's [Open Graph protocol](http://ogp.me/) and for [Twitter Cards](https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/getting-started.html) so that pages display well when shared on social media sites. Metadata is also included for mobile users who may wish to save the page to their phone's home screen.
Geographic microdata is added using the [Place microdata schema](https://schema.org/Place) to parking reservation pages so that advanced web parsers know where the parking spot is geographically located. Microdata is also included for [ratings](https://schema.org/Rating) and [reviews](https://schema.org/Review) so they can be aggregated into search results. 
### AJAX Ratings/Reviews
The simple way for users to submit a rating and/or review is to click submit, navigate to a new page as requested via a GET or POST, insert the data into the database, generate a new HTML response of the full page and return this to the browser which then loads the new page.
AJAX (Asynchronous JavaScript and XML) is used so that the user's browser doesn't load a new page when the user submits a rating or review. Instead, the server inserts the data into the database and returns a success/failure code to the browser, which then runs JavaScript to add the new review in the appropriate location of the DOM without reloading the entire page. This results in a **better UI experience** (don't have to discard and reload entire page to upload data), **less bandwidth and server-load** (only loads data or reloads a partial page), and **multithreaded data retrieval** from web servers (pre-fetches data before its needed, supports progress indicators and gives the appearence of speed). However, AJAX has two key drawbacks: **(i)** screen readers don't really know how to update their state based on updated content, and **(ii)** most web crawlers don't execute JavaScript so any content loaded via AJAX won't be visible to them (although GoogleBot does execute JavaScript).
#### Author
Michael Balas

#### License
[GNU General Public License](LICENSE)
