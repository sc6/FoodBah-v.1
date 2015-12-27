<!DOCTYPE HTML>

<?php
include("../functions/connect.php");
include("../functions/admins.php");

//--VENDORS--
$query = "SELECT * FROM dayztbns_foodbah.Vendors WHERE id=".$_GET['v'].";";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

$vendor_name 	= 	$row["vendor_name"];	
$vendor_id 		= 	$_GET["v"];


?>

<HTML>



<HEAD>
<meta name="robots" content="noindex">
<?php include("../functions/imports.php"); ?>
<title><?=$vendor_name?> Change Image - FOODBAH</title>

<script>
function pleaseWait() {
	$("#pleaseWait").text("Uploading, please wait...");
}
</script>

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
	
	if(isset($_SESSION['user_name'])) { //User logged in check
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
	$error_upload = "";
	if(isset($_FILES["fileToUpload"]) && isset($_SESSION['user_name'])) {
		$target_dir = "../assets/title_img/";
		$target_file = $target_dir . $_GET['v'].".jpg";
		$uploadOk = 1;
		$imageFileType = pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
		// Check if image file is a actual image or fake image
		if(isset($_POST["submit"])) {
			$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			if($check !== false) {
				$uploadOk = 1;
			} else {
				$uploadOk = 0;
			}
		}
		// Check if file already exists
		if (file_exists($target_file)) {
			$error_upload = "Error: The file name conflicts with one already stored in our database.";
			$uploadOk = 0;
		}
		 // Check file size
		if ($_FILES["fileToUpload"]["size"] > 4200000) {
			$error_upload = "Sorry, vendor images must be less than 4MB.";
			$uploadOk = 0;
		}
		// Allow certain file formats
		if($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "JPG") {
			$error_upload = "Error: This image file type is not supported. Only JPEG files are allowed.";
			$uploadOk = 0;
		}
		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			//echo "Sorry, your file was not uploaded.";
		}
		// if everything is ok, try to upload file
		else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				
				//strip EXIF data
				 try {   
					$img = new Imagick(glob($target_file));
					$img->stripImage();
					$img->writeImage($target_file);
					$img->clear();
					$img->destroy();
				} catch(Exception $e) {
					echo 'DEBUG: Failed to delete metadata from this image.';
				}
				
				//create a smaller (900px x .) file
				$img = new Imagick(realpath($target_file));
				$og_size = getimagesize($target_file);
				$og_width = $og_size[0];
				$og_height = $og_size[1];
				$og_ratio = $og_height/$og_width;
				$new_height = $og_ratio * 900;
				$img->resizeImage(900, $new_height, Imagick::FILTER_LANCZOS, 1);
				$img->writeImage($target_file);
				$img->clear();
				$img->destroy();
				
				$error_upload = "Image upload successful.";			
			} else {
				//echo "Sorry, there was an error uploading your file.";
			}
		}
	}
	?>
	
	
	<div class="index_capsule">
		
		
		<div class="index_panel">
			<span class="subheader_1">Change header image for <?=$vendor_name?></span>
			<br><br>
			<br>

			<?if($error === 'not_logged_in') {?>
				Image submission is for logged in users only. Becoming a user is quick, and will take less
				than a minute of your time. This is something we must do in order to combat fake or misleading submissions. 
				Your contributions would be greatly appreciated.
				<br><br>
				<a href="/login/register.php">You can register by clicking here.</a>&nbsp;&nbsp;<a href="/login/">(Or, login here)</a>
				<br><br><br>
				<a href="/vendor/?q=<?=$vendor_id?>">Back to <?=$vendor_name?></a>
			<? } elseif(!$admins[$_SESSION['user_name']] === 'admin' && !file_exists("../assets/title_img/$vendor_id.jpg")) { ?>
				This section can currently only be accessed by administrators.
			<?} else {?>		
				<form method="POST" enctype="multipart/form-data">
					<table><tr>
						<td><span class="subheader_3">Choose a photo</span></td>
						<td><input type="file" name="fileToUpload" id="fileToUpload"></td>
					</tr><tr>
						<td><span class="subheader_3">And you're done</td>
						<td><input class="login_flat" type="submit" value="Upload Image" onclick="pleaseWait()" name="submit"></td>  <span id="pleaseWait"></span>
					</tr></table>
					<br><br>
					<?
					if($error_upload === "Image upload successful.") { ?>
						&nbsp;&nbsp;<span style="color:green;"><?=$error_upload?></span>
						<br><br>
						&nbsp;&nbsp;<a href="/vendor/?q=<?=$vendor_id?>">Head back to <?=$vendor_name?>.</a>
					<? } elseif($error_upload != "") { ?>
						&nbsp;&nbsp;<span style="color:red;"><?=$error_upload?></span>
						<br><br>
						&nbsp;&nbsp;<a href="/vendor/?q=<?=$vendor_id?>">Head back to <?=$vendor_name?>.</a>
					<? } else { ?>
						&nbsp;&nbsp;<a href="/vendor/?q=<?=$vendor_id?>">Never mind, take me back.</a>
					<? } ?>
				</form>
			<? } ?>
		</div>
		
		<br><br>

		</div>
	</div>
	
</BODY>

</HTML>