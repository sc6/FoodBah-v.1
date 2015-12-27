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

<?php
$servername = "localhost";
$dbname = "dayztbns_cidzor";
$username = "dayztbns_cid";
$password = "dummyPa55word!";

$link = new mysqli($servername, $username, $password, $dbname);

$query = "SELECT * FROM dayztbns_login.users WHERE user_email='".$_SESSION['user_email']."'";
$result = mysqli_query($link, $query);

$row = mysqli_fetch_array($result, MYSQL_ASSOC);

$p_rlname = $row['user_rlname'];
$p_address = $row['user_address'];
$p_city = $row['user_city'];
$p_state = $row['user_state'];
$p_zip = $row['user_zip'];

// Check connection
if ($link->connect_error) {
	die("Connection failed: " . $link->connect_error);
}
?>

<?
if(isset($_POST['street_address'])) {
	//escape chars and set to db
	$rl_name = htmlspecialchars(mysqli_real_escape_string($link, $_POST['rl_name']));
	$street_address = htmlspecialchars(mysqli_real_escape_string($link, $_POST['street_address']));
	$city = htmlspecialchars(mysqli_real_escape_string($link, $_POST['city']));
	$state = htmlspecialchars(mysqli_real_escape_string($link, $_POST['state']));
	$zip = htmlspecialchars(mysqli_real_escape_string($link, $_POST['zip']));
	
	$query = "UPDATE dayztbns_login.users SET 
	user_rlname='".$rl_name."',
	user_address='".$street_address."', 
	user_city='".$city."',
	user_state='".$state."',
	user_zip='".$zip."' 
	WHERE user_email='".$_SESSION['user_email']."'";
	
	mysqli_query($link, $query);
	
	$dialog = "Your mailing address has been updated. <a href='index.php'>Return</a>";
}

?>

<!DOCTYPE>

<HTML>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--<meta name="description" content="Welcome to cidzor">-->

    <title>cidzor - configure account details</title>

    <link href="/assets/style/style.css" rel="stylesheet" type="text/css" />
	
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">

</head>


<body>
	<?php 
	//$sub_directory = "front_page";
	include '../snippets/header.php';
	?>
	
	<div class="body_wrapper">

		<div class="content_box">
			<div class="subtitle">Set Mailing Address</div>
			<br />
			<?php if(isset($dialog)): ?>
				<?php echo $dialog ?>
				<br /><br />
			<?php endif; ?>
			
			
			<form method="post" action="configure.php" name="setaddress">
			<table class="form">
				<tr>
				<th>Information</th>
				<th>Field</th>
				</tr>
				<tr>
				<td class="form">
					<label for="set_rl_name">Name:</label>
				</td>
				<td class="form">
					<input id="rl_name" class="login" type="text" name="rl_name" required maxlength="300" placeholder="<?php echo $p_rlname ?>"/>
				</td>
				</tr>
				<tr>
				<td class="form">
					<label for="set_street_address">Street Address:</label>
				</td>
				<td class="form">
					<input id="street_address" class="login" type="text" name="street_address" required maxlength="300" placeholder="<?php echo $p_address ?>"/>
				</td>
				</tr>
				<tr>
				<td class="form">
					<label for="set_city">City:</label>
				</td>
				<td class="form">
					<input id="city" class="login" type="text" name="city" required maxlength="150" placeholder="<?php echo $p_city ?>"/>
				</td>
				</tr>
				<tr>
				<td class="form">
					<label for="set_state">State:</label>
				</td>
				<td class="form">
					<input id="state" class="login" type="text" name="state" required maxlength="2" placeholder="<?php echo $p_state ?>"/>
				</td>
				</tr>
				<tr>
				<td class="form">
					<label for="set_zip">Zip:</label>
				</td>
				<td class="form">
					<input id="zip" class="login" type="text" name="zip" required maxlength="10" placeholder="<?php echo $p_zip ?>"/>
				</td>
				</tr>
			</table>
			<input type="submit"  name="login" class="login" value="Submit" />
			</form>
			
			
			<br /><br />
			<a href="/">[Return to index]</a>&nbsp;&nbsp;&nbsp;
			<a href="index.php">[My account]</a>
			<br /><br />
		</div>
		
	</div>


</body>


</HTML>