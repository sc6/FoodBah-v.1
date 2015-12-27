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

$item_name 		= 	$row["food"];	
$item_id 		= 	$_GET["q"];
$item_raw_tags 	= 	$row['tags']; //raw
$item_category 	= $row["category"];


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



<BODY>
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
		
	}
	else {
		$error = "not_logged_in";
	}
	
	
	//Form Processing
	if(isset($_POST['add_price'])) { //(form submitted)

		//INSERT TO RATINGS
		$stmt = mysqli_prepare($conn, "UPDATE `dayztbns_foodbah`.`Menus` SET `price` = ? WHERE `Menus`.`id` = ?");
		$price = filter_var($_POST['add_price'], FILTER_SANITIZE_NUMBER_INT);
		mysqli_stmt_bind_param($stmt, 'ii', $price, $item_id);
		mysqli_stmt_execute($stmt);
		mysqli_stmt_close($stmt);
		
		//UPDATE TAGS (no_price)
		$item_raw_tags = str_replace(', no_price', '', $item_raw_tags);
		$item_raw_tags = str_replace('no_price', '', $item_raw_tags); //if only one tag
		
		$query = "UPDATE `dayztbns_foodbah`.`Menus` SET `tags` = '$item_raw_tags' WHERE `Menus`.`id` = $item_id;";
		$result = mysqli_query($conn, $query);
		
		$error = "success";

	} 
	?>
	
	
	<div class="content_wrapper">
		<div class="add_rating_wrapper">
			<h4>
			Submit a Rating
			</h4>
			<br>
			<div class="submission_item">
				<table>
					<tr>
						<?if($image_qty > 0) { ?>
						<td><img src="<?=$image_path?>" alt="<?=$item_name?>" width="75px"></td>
						<?}?>
						<td style="padding-left:10px">
							<strong><?=$item_name?><strong>
							<br>
							<span style="font-size:12px">(<?=$vendor_name?> &nbsp;&gt;&nbsp; <?=$item_category?>)</span>
						</td>
				</table>
			</div>
			<br><br>
			
			<?if($error === 'already_submitted' || $error === 'success') {?>
				This item's price is now considered complete. If you'd like to make a correction, please
				contact us.
				<?if($error === 'already_submitted') {?>
				<br><br>
				Edit and remove features are currently unavailable. We hope to get them done as soon 
				as possible.
				<? } ?>
				
				<br><br>
				
				<a href="/item/?q=<?=$item_id?>">Back to <?=$item_name?></a>
				
			
			<?} else if($error === 'not_logged_in') {?>
				Submissions are for logged in users only. Becoming a user is quick, and will take less
				than a minute of your time. This is something we must do in order to combat fake or misleading submissions. 
				Your contributions would be greatly appreciated.
				<br><br>
				<a href="/login/register.php">You can register by clicking here.</a>&nbsp;&nbsp;<a href="/login/">(Or, login here)</a>
				<br><br><br>
				<a href="/item/?q=<?=$item_id?>">Back to <?=$item_name?></a>
			
			
			
			<?} else {?>
			<form method="POST" class="add_rating_form" id="add_rating_form">
				How much is this item?
				<br><br>
				<input type="text" name="add_price" placeholder="$3.00" required pattern="^\$?[0-9]{1,5}\.[0-9]{2}$" class="add_menu_item">
				<br><br>
				<input type="submit" class="add_price_submit" value="Submit">&nbsp;&nbsp;<a href="/item/?q=<?=$item_id?>">Back to <?=$item_name?></a>
			</form>
			<? } ?>
		</div>
	</div>
	
</BODY>

</HTML>