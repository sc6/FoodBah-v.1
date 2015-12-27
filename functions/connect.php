<?php
$servername = "--";
$dbname = "--";
$username = "--";
$password = "--";

// Create connection
$conn = mysqli_connect($servername, $username, $password);

// Check connection
if (!$conn) {
	die("Connection failed: " . mysqli_connect_error());
}

function myQuery($query) {
	$result = null;
	if ($result = mysqli_query($conn, $query)) {
		mysqli_free_result($result);
	}
	
	return $result;
}
?>