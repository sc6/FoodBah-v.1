<?php
$servername = "--";
$dbname = "--";
$username = "--";
$password = "--";

$link = new mysqli($servername, $username, $password, $dbname);

$query = "SELECT * FROM dayztbns_login.users WHERE user_email='".$_SESSION['user_email']."'";
$result = mysqli_query($link, $query);

$row = mysqli_fetch_array($result, MYSQL_ASSOC);

// Check connection
if ($link->connect_error) {
	die("Connection failed: " . $link->connect_error);
}
?>


<div class="content_wrapper">
	<h4>My Account Summary</h4>
	<br>
	
	Here, your account information will be displayed. This page will include things
	like voting and comment history, personal information, and received messages.
	<br><br>
	
	This section is currently in development. If you have suggestions on what we should
	include, please feel free to send them over.
	<br><br>
	
	Username: <?=$_SESSION['user_name']?>
	<br />
	Email: <?=$row['user_email']?>
	<br>
	<br><br>
	
	<a href="/">Return to index</a>
	
	</div>
</div>


