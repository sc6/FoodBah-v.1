<!DOCTYPE HTML>

<?php
include("../functions/connect.php");

//--VENDORS--
$query = "SELECT * FROM dayztbns_foodbah.Vendors WHERE id=".$_GET['v'].";";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

$vendor_name 	= 	$row["vendor_name"];	
$vendor_id 		= 	$_GET["v"];


//--MENUS--
$query = "SELECT * FROM dayztbns_foodbah.Menus WHERE id=".$_GET['q'].";";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

$item_name 	= 	$row["food"];	
$item_id 	= 	$_GET["q"];
$item_category = $row["category"];


//MENU_IMAGES
$query = "SELECT * FROM dayztbns_foodbah.Menu_Images WHERE menu_id=".$_GET['q']." ORDER by points DESC;";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);

$image_qty = mysqli_num_rows($result);
$image_path = "/assets/$vendor_id/".$row['name'].".png";

mysqli_free_result($result);


?>

<HTML>



<HEAD>
<meta name="robots" content="noindex">
<?php include("../functions/imports.php"); ?>
<title><?=$item_name?> Submission - FOODBAH</title>
</HEAD>



<BODY class="plain">
	<?php include("../functions/login_bar.php"); ?>
	<script src="/functions/ads.js"></script>
	<?php include("../functions/header_bar.php"); ?>
	
	<?php
	$user_id = 0;
	$rating_content = null;
	$rating_value = null;
	$error = null;
	
	if(isset($_SESSION['user_name'])) { //(User logged in check)
		//GET USER INFO
		$query = "SELECT * FROM dayztbns_login.users WHERE user_name='".$_SESSION['user_name']."';";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		mysqli_free_result($result);
		
		$user_id = $row['user_id'];
		
		
		//RATINGS
		$query = "SELECT * FROM dayztbns_foodbah.Ratings WHERE user_id='".$user_id."' AND item_id='".$item_id."';";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
		if(mysqli_num_rows($result) > 0) {
			$error = "already_submitted";
			$rating_content = htmlspecialchars(stripslashes($row['comment']));
			$rating_value = $row['rating'];
		}
		mysqli_free_result($result);
	}
	else {
		$error = "not_logged_in";
	}
	
	
	//Form Processing
	if(isset($_POST['value']) && isset($_POST['content']) && isset($_SESSION['user_name'])) { //(form submitted)

		//INSERT TO RATINGS
		$stmt = mysqli_prepare($conn, "INSERT INTO `dayztbns_foodbah`.`Ratings` (`item_id`, `user_id`, `rating`, `comment`, `timestamp`) 
								VALUES (?, ?, ?, ?, ?);");
		mysqli_stmt_bind_param($stmt, 'iiisi', $item_id, $user_id, $_POST['value'], $_POST['content'], time());
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		
		$error = "success";

	} 
	?>
	
	<div class="index_capsule">

		<div class="index_panel">
			<span class="subheader_1">Share an image for <?=$item_name?></span>
			<br><br>
			<br>
			
			<?if($error === 'already_submitted' || $error === 'success') {?>
				Thanks for rating this item. Your rating is appreciated.
				<?if($error === 'already_submitted') {?>
				<br><br>
				Edit and remove features are currently unavailable. We hope to get them done as soon 
				as possible.
				<br><br><br>
				
				<div class="item_rating_wrapper" <? echo ($rating_value == 1 ? "style='background-color:rgba(0, 255, 0, 0.1);'" : "style='background-color:rgba(255, 0, 0, 0.1);'")?>>
					<div class="item_rating_content">
						<?=$rating_content?>
					</div>
					<div class="item_rating_caption">
						Rating by <?=$_SESSION['user_name']?>
					</div>
				</div>
				<? } ?>
				
				<br><br>
				
				<a href="/item/?q=<?=$item_id?>">Never mind, take me back</a>
				
			
			<?} else if($error === 'not_logged_in') {?>
				Rating is for logged in users only. Becoming a user is quick, and will take less
				than a minute of your time. This is something we must do in order to combat fake or misleading submissions. 
				Your contributions would be greatly appreciated.
				<br><br>
				<a href="/login/register.php">You can register by clicking here.</a>&nbsp;&nbsp;<a href="/login/">(Or, login here)</a>
				<br><br><br>
				<a href="/item/?q=<?=$item_id?>">Never mind, take me back</a>
			
			
			
			<?} else {?>
			<form method="POST" class="add_rating_form" id="add_rating_form">
				1. How did you feel about this item?
				<br><br>
				
				<input type="radio" name="value" value="1" required>
				<span style="font-size:14px">I liked this menu item and felt that it was at least worth its value.</span>
				<br>
				<input type="radio" name="value" value="0">
				<span style="font-size:14px">I did not enjoy this item at all and would never recommend it to a friend.</span>
				
				<br><br><br>
				2. Why do you like/dislike this item? (Max. 500 characters)
				<br><br>
				
				<textarea class="add_rating_content" form="add_rating_form" name="content" maxlength="500" required autofocus></textarea>
				<br><br>
				<input type="submit" class="add_rating_submit" value="Submit">&nbsp;&nbsp;<a href="/item/?q=<?=$item_id?>">Back to <?=$item_name?></a>
			</form>
			<? } ?>
		</div>
	</div>
	
</BODY>

</HTML>