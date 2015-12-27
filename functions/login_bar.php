<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once($_SERVER["DOCUMENT_ROOT"]."/login/libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once($_SERVER["DOCUMENT_ROOT"]."/login/config/db.php");

// load the login class
require_once($_SERVER["DOCUMENT_ROOT"]."/login/classes/Login.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();
?>


<div class="login_bar">
	<div class="content_wrapper">
	
	<?
	//checks if remember me token is active IF not logged in
	if(!isset($_SESSION['user_name']) && isset($_COOKIE['rememberme'])) {
		$db_temp = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$query = "SELECT * FROM auth_tokens WHERE token = '".$_COOKIE['rememberme']."'";
		$result = $db_temp->query($query);
		$row = $result->fetch_array();
		
		$_SESSION['user_name'] = $row['user_name'];
		$_SESSION['user_login_status'] = 1;
	}
	
	if(isset($_SESSION['user_name'])) {
	?>
		Hi, <?=$_SESSION['user_name']?>. &nbsp;&nbsp; <a href="/login">My Account</a> &nbsp;&nbsp; <a href="/login/?logout">Logout</a>
	<? 
	}else {
	?>
		&nbsp; <a class="login_link" href="/login">Login</a> 
		&nbsp;&nbsp;&nbsp; 
		<a class="login_link" href="/login/register.php">Register</a>
	<?
	}
	?>
	
	</div>
</div>