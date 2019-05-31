<?php
// Include config file
require_once "DB_config.php";
// Include S3 bucket name, access keys and library

$awsAccessKey = "XXplaceholderXX";
$awsSecretKey = "XXplaceholderXX";
$bucketName = "XXplaceholder";
require 'S3.php';
$s3 = new S3($awsAccessKey, $awsSecretKey);


if (!isset($_POST["rating"]) || ($_POST["rating"] === "")) {
	echo json_encode(array("status" => false,
						   "message" => "No rating provided"));
} else if (!isset($_POST["review"]) || ($_POST["review"] === "")) {
	echo json_encode(array("status" => false,
						   "message" => "No review provided"));
} else if (strlen($_POST['review']) > 500) {
	echo json_encode(array("status" => false,
						   "message" => "Review exceeding 500 characters!"));
} else {
	$sql = "INSERT INTO reviews (parkingsID, usersID, rating, review) VALUES (:parkingsID, :usersID, :rating, :review)";
	if ($stmt = $pdo -> prepare($sql)) {
			// Bind variables to the prepared statement as parameters
			$stmt -> bindValue(':parkingsID', $_GET['parking']);
			$stmt -> bindValue(':usersID', $_SESSION['usersID']);
			$stmt -> bindValue(':rating', $_POST["rating"]);
			$stmt -> bindValue(':review', $_POST["review"]);
			// Check if statement executes
			if (!$stmt -> execute()) {
                echo "Something went wrong. Please try again later.";
				echo $stmt -> error;
            }
		// Close statement and query
		unset($stmt);
        unset($sql);
	}
    // retrieve data from user profile to embed in review
    $sql = "SELECT fullname, profileurl FROM users WHERE usersid = :usersid"
    if ($stmt = $pdo -> prepare($sql)) {
        $stmt -> bindValue(':usersid', $_SESSION['usersID']);
        // Attempt to execute the prepared statement
        if ($stmt -> execute()) {
            if ($stmt -> rowCount() == 1) {
                if ($row = $stmt -> fetch()) {
                    $fullname = $row['fullname'];
                    $profileurl = $row['profileurl'];
                }
            } else {
                echo "You must be logged in to leave a review.";
                echo $stmt -> error;
            }
        } else {
            echo "Something went wrong. Please try again later.";
            echo $stmt -> error;
        }
        // Close statement and query
        unset($stmt);
        unset($sql);
    }
	// Close connection
	unset($pdo);

	echo json_encode(array("status" => true,
						   "rating" => htmlspecialchars($_POST["rating"]),
						   "review" => htmlspecialchars($_POST["review"]),
                           "reviewer" => $fullname,
                           "profilepic" => $profileurl
                           ));
}
?>
