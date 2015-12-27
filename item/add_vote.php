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

<?php
include("../functions/connect.php");

if(isset($_SESSION['user_name'])) {
	$query = "INSERT INTO `dayztbns_foodbah`.`Image_Votes` (`user_name`, `img_id`, `value`, `timestamp`) VALUES ('".$_SESSION['user_name']."', '".$_POST['q']."', '".$_POST['v']."', ".time().");";
	mysqli_query($conn, $query);
}

exit();
?>