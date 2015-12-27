<?php
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("libraries/password_compatibility_library.php");
}

// include the configs / constants for the database connection
require_once("config/db.php");

// load the login class
require_once("classes/Login.php");

// create a login object. when this object is created, it will do all login/logout stuff automatically
// so this single line handles the entire login process. in consequence, you can simply ...
$login = new Login();
?>



<!DOCTYPE>

<HTML>

<HEAD>
	<?php include("../functions/imports.php"); ?>
	<title>Foodbah - <?=$vendor_name?> Login</title>
</HEAD>


<BODY>
	<?php include("../functions/login_bar.php"); ?>
	<script src="/functions/ads.js"></script>
	<?php include("../functions/header_bar.php"); ?>


	<?php
	if ($login->isUserLoggedIn() == true) {
		include("views/logged_in.php");
	}
	else {
		echo '<br /><br />';
		include("views/not_logged_in.php");
	}
	?>

</BODY>

</HTML>