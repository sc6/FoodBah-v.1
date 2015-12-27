<!DOCTYPE HTML>

<?php

include("functions/connect.php");
include("functions/admins.php");

?>

<HTML>


<HEAD>
<meta name="robots" content="noindex">
<?php include("functions/imports.php"); ?>
<title>Administrator's Corner - FOODBAH</title>
</HEAD>

<BODY>
	<?php include("functions/login_bar.php"); ?>
	<script src="/functions/ads.js"></script>
	<?php include("functions/header_bar.php"); ?>
	
	<div class="content_wrapper">
	
	<?if($admins[$_SESSION['user_name']] === 'admin') {
		//Image removal
		if($_GET['v'] === 'removal' && is_int(intval($_GET['q']))) { 
			$query = "DELETE FROM `dayztbns_foodbah`.`Menu_Images` WHERE `Menu_Images`.`id` = ".$_GET['q'].";";
			mysqli_query($conn, $query);
			
			$query = "INSERT INTO `dayztbns_foodbah`.`admin_history` (`user_name`, `ip_address`, `description`, `timestamp`) VALUES ('".$_SESSION['user_name']."', '".$_SERVER['REMOTE_ADDR']."', 'deleted image ".$_GET['q']."', ".time().");";
			mysqli_query($conn, $query);
			
			echo '
			<p>The image has been successfully removed.</p>
			<br><br>
			<a href="/">Back to index</a>
			';
		}
		
	} else {?>
		<p>
		Sorry, you must be an administrator or moderator in order 
		to access this section. Apply to become a moderator today.
		</p>
	<? } ?>
	
	</div>
	
</BODY>
</HTML>