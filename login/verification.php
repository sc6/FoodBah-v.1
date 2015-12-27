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

// Check connection
if ($link->connect_error) {
	die("Connection failed: " . $link->connect_error);
}
?>

<?

//User has already been verified
if($row['user_email_verified'] == 1) {
	$dialog = "stop";
}
//User is requesting verification
else {


//Runs if pin was generated and time hasn't expired (10 minutes)
if(!is_null($row['param_1']) && time() < $row['time_param']) { //Checks if pin is correct
	$dialog = "You have a pending verification code. Please check your email.";
	//Runs if form was submitted
	if(isset($_POST['pin'])) {
		//Runs if submission matches generated entry
		if($_POST['pin'] === $row['param_1']) {
			$query = "UPDATE dayztbns_login.users SET user_email_verified='1' WHERE user_email='".$_SESSION['user_email']."'";
			$result_dummy = mysqli_query($link, $query);
			$dialog = "stop";
		}
		else {
			$dialog = 'Your input was incorrect. Please try again.';
		}
	}
}
//Runs if time had expired
else if (time() >= $row['time_param']) 
{
	$dialog = "Your verification code has expired. Another verification code has been sent to your email address.";
	goto generate_pin;
}
else { //Generate the verification code and email it.
	generate_pin:
	$user_code = mt_rand (1000, 9999);
	$dialog = 'A verification code has been sent to ' . $_SESSION['user_email'];

	$query = "UPDATE dayztbns_login.users SET param_1='".$user_code."', time_param='".(time() + (60*10))."' WHERE user_email='".$_SESSION['user_email']."'";
	$result = mysqli_query($link, $query);
	
	$to  = $_SESSION['user_email'];
	$subject = 'Verification Code';

	$message = '
	<html>
	<head>
		<title>cidzor.com - verify email</title>
	</head>
	<body>
		<p>Your verification code is <strong>'.$user_code.'</strong>.</p>
	</body>
	</html>
	';

	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'From: cidzor.com <bot@cidzor.com>' . "\r\n";
	
	mail($to, $subject, $message, $headers);

	
}
}//OK
?>

<!DOCTYPE>

<HTML>

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!--<meta name="description" content="Welcome to cidzor">-->

    <title>cidzor - verify email</title>

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
			<?php if($dialog === 'stop'): ?>
				Your email has been successfully verified.
				<br /><br />
				<a href="/">[Return to index]</a>
			<?php else: ?>
				<?php echo $dialog ?>
				<br /><br />
				
				<form method="post" action="verification.php" name="verificationform">
				
					<label for="email_verification_code">Please enter this code here:</label><br />
					<input id="pin" class="login" type="text" name="pin" required />
					<br /><br />
					
					<input type="submit"  name="login" class="login" value="Verify" />
					<br /><br />
					
				</form>
			<? endif; ?>
		
		</div>
		
	</div>


</body>


</HTML>