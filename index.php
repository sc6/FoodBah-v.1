<!DOCTYPE HTML>


<HTML>


<HEAD>
<?php 
	include("functions/imports.php");
	include("functions/connect.php");
?>
<title>FOODBAH - Ordering Only the Finest</title>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</HEAD>

<BODY class="plain">
	<?php include("functions/login_bar.php"); ?>
	
	<br><br>
	
	<div class="content_wrapper">
		<div class="logo" style="margin:0 auto">FOODBAH</div>
	</div>
	
	<br><br>
	<br><br>
	<br><br>
	
	<div class="index_capsule">
	
		<!--Search bar-->
		<div class="index_panel" style="font-size:14px">
			<span class="subheader_1">Search for a restaurant or something you'd like to eat...</span>
			<form method="POST" action="/search.php">
				<input type="text" class="searchbar_flat_1" name="search_bar_main" pattern="^[a-zA-Z ]{3,120}$" autofocus></input>
			</form>
		</div>
		<!--END Search bar-->
		
		<div class="index_divider"></div>
		
		<!--REGISTRATION FORM-->
		<? if(!isset($_SESSION['user_name'])) { ?>
			<br><br>
			<div class="index_panel">
				<span class="subheader_1">Join our team and help this become the world's largest food database.</span>
				<br><br>
				
				<?php
				// checking for minimum PHP version
				if (version_compare(PHP_VERSION, '5.3.7', '<')) {
					exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
				} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
					// if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
					// (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
					require_once("login/libraries/password_compatibility_library.php");
				}
				
				// include the configs / constants for the database connection
				require_once("login/config/db.php");

				// load the registration class
				require_once("login/classes/Registration.php");

				// create the registration object. when this object is created, it will do all registration stuff automatically
				// so this single line handles the entire registration process.
				$registration = new Registration();

				//shows potential errors with form submission
				if (isset($registration)) {
					if ($registration->errors) {
						foreach ($registration->errors as $error) {
							echo "<span style='color:white'>$error</span><br /><br />";
						}
					}
					if ($registration->messages) {
						foreach ($registration->messages as $message) {
							echo "<span style='color:white'>$message</span><br /><br />";
						}
					}
				}
				?>
				
				<form method="post" action="/login/register.php" name="registerform" style="display:inline-block">
				
					<table>
					<tr>
						<td><span class="subheader_3">Choose a username</span></td>
						<td><input id="login_input_username" class="login" type="text" pattern="[a-zA-Z0-9]{2,16}" name="user_name" required autocomplete="off"/></td>
					</tr>
					<tr>
						<td><span class="subheader_3">Set a password</span></td>
						<td><input id="login_input_password_new" class="login" type="password" name="user_password_new" pattern=".{6,32}" required autocomplete="off" /></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<div class="register_captcha">
								<div class="g-recaptcha" data-sitekey="6LchmgYTAAAAABmg0kWgNkvLQ6cqcodL0gAC-8XK"></div>
							</div>
						</td>
					</tr>
					<tr>
						<td><span class="subheader_3">And, you're done</span></td>
						<td>
							<input type="submit" class="login_flat" name="register" value="Register" />
						</td>
					</tr>
					</table>
					
				</form>
			</div>
			
		<? } ?>
		<!--REGISTRATION FORM END-->
		
		<div class="index_divider"></div>
		<br>
		
		<!--TRENDING FOODS-->
		<div class="index_panel">
			<?
			$vendors = array();
			$query = "SELECT * FROM dayztbns_foodbah.Vendors;";
			$result = mysqli_query($conn, $query);
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$name = $row['vendor_name'];
				$vendors[$name]['id'] = $row['id'];
				$vendors[$name]['name'] = $name;
				$vendors[$name]['address'] = $row['address_1'] . ", " . $row['address_2'];
			}
			?>
			<strong>Some places to check out:</strong>
			<br>
			<?foreach($vendors as $vendor) { ?>
				<a href="/vendor/?q=<?=$vendor['id']?>">
					<div class="index_restaurant_card">
					<span class="name"><?=$vendor['name']?></span><br>
					<span class="address"><?=$vendor['address']?></span>
					</div>
				</a>
			<? } ?>
		</div>
		<!--END TRENDING FOODS-->
		
		
		
		<!--ADD VENDOR LINK-->
		<? if(isset($_SESSION['user_name'])) { ?>
		<div class="index_divider"></div>
		<br>
		
		<div class="index_panel">
			<a href="/vendor/add_vendor_2.php">Add your favorite restaurant or a business you own.</a>
		</div>
		<? } ?>
		<!--END ADD VENDOR LINK-->
	
	
	
	</div>


	<br><br>
	<br><br>	
	<br><br>
	<br><br>
	<br><br>
	<br><br>


	</div>
		

</BODY>


</HTML>