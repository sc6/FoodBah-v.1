<!DOCTYPE HTML>

<?php
include("../functions/connect.php");
include("../functions/admins.php");

//--VENDORS--
$query = "SELECT id FROM `dayztbns_foodbah`.`Vendors` ORDER BY id DESC;";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);
	
$vendor_id 		= 	$_row["id"]+1;
?>
<HTML>


<HEAD>
<meta name="robots" content="noindex">
<?php include("../functions/imports.php"); ?>
<title>Add Vendor - FOODBAH</title>
</HEAD>

<BODY>
	<?php include("../functions/login_bar.php"); ?>
	<script src="/functions/ads.js"></script>
	<?php include("../functions/header_bar.php"); ?>
	
	<?//--FORM INPUT (ADD_VENDOR)--
	
	if(isset($_POST['add_name']) && isset($_POST['add_address']) && isset($_POST['add_zip']) && isset($_POST['add_phone']) && isset($_SESSION['user_name']) && isset($_FILES['fileToUpload'])) {
		
		if (preg_match('/^[a-zA-Z0-9 &-:]{3,149}$/', $_POST['add_name']) === 1 && 
			preg_match('/^[a-zA-Z0-9 -.]{3,149}$/', $_POST['add_address']) === 1 &&
			preg_match('/^[0-9 -]{5,5}$/', $_POST['add_zip']) === 1 &&
			preg_match('/^[0-9\(\)-]{8,15}$/', $_POST['add_phone']) === 1)
			{
				
			$name = $_POST['add_name'];
			$address = $_POST['add_address'];
			$zip = $_POST['add_zip'];
			$phone = $_POST['add_phone'];
			
			//Extract ZIP information
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://api.zippopotam.us/us/$zip");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			$json = json_decode($response, true);
			
			$city = $json['places'][0]['place name'];
			$state = $json['places'][0]['state'];
			
			//Make address
			$address_line_2 = "$city, $state $zip";
			
			//Insert into Vendors table
			$query = "INSERT INTO `dayztbns_foodbah`.`Vendors` (`vendor_name`, `menu_finished`, `address_1`, `address_2`, `phone`) VALUES ('$name', '0', '$address', '$address_line_2', '$phone');";
			mysqli_query($conn, $query);
			
			//Upload image to server
			$error_upload = "";
			if(isset($_FILES["fileToUpload"])) {
				$target_dir = "../assets/title_img/";
				$target_file = $target_dir . $vendor_id . "." . pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
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
				if ($_FILES["fileToUpload"]["size"] > 1024000) {
					$error_upload = "Error: This image is larger than 1MB.";
					$uploadOk = 0;
				}
				// Allow certain file formats
				if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
				&& $imageFileType != "gif" ) {
					$error_upload = "Error: This image file type is not supported. Try JPG, PNG, or GIF files.";
					$uploadOk = 0;
				}
				// Check if $uploadOk is set to 0 by an error
				if ($uploadOk == 0) {
					//echo "Sorry, your file was not uploaded.";
				}
				// if everything is ok, try to upload file
				else {
					if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
						$error_upload = "Image upload successful.";
					} else {
						//echo "Sorry, there was an error uploading your file.";
					}
				}
			}
			
			echo "$name has been successfully added.";
		}
		else {
			echo 'please do not manipulate client-side code or spoof input. your username/ip has been recorded.';
		}
	}
	?>
	
	
	
	
	<div class="content_wrapper">

		<br><br>
		<div class="search_bar_wrapper">
		
		<h4>
		Add Vendor
		</h4>
		<br>
	
		<?if($admins[$_SESSION['user_name']] === 'admin') { ?>
			<form method="POST" class="add_menu_form" enctype="multipart/form-data">
			
				<strong>1. What is this place called?</strong><br>
				<input type="text" name="add_name" required pattern="[a-zA-Z0-9 &-:]{3,149}" class="input_style_1">
				<br><br>
				
				<strong>2a. What's its street address?</strong><br>
				<input type="text" name="add_address" required pattern="[a-zA-Z0-9 -.]{3,149}" class="input_style_1">
				<br><br>
				
				<strong>2b. Zip code?</strong><br>
				<input type="text" name="add_zip" required pattern="[0-9 -]{5,5}" class="input_style_1" id="getZip">
				<br><br>
				
				<strong>3. Phone number?</strong><br>
				<input type="text" name="add_zip" required pattern="[0-9\(\)-]{8,15}" class="input_style_1" id="getPhone">
				<br><br>
				
				<strong>3. What does this place look like?</strong><br>
				<input type="file" name="fileToUpload" id="fileToUpload" class="input_style_1">
				<br><br><br>
				
				<input type="submit" name="submit" value="Add Vendor" class="input_style_1"> &nbsp;&nbsp; <a href="/">Back to home</a>
				
				<onclick="getAddress($('#getZip').val())">
			</form>
		<? } else {?>
			<p>
			Sorry, you must be an administrator or moderator in order 
			to access this section. Apply to become a moderator today.
			</p>
		<? } ?>
		
		</div>
		
		<br><br>
		
	</div>
	
	<br><br><br>
	
</BODY>


<script>
var state = "";
var city = "";
function getAddress(zip) {
	$.ajax({
		url: "http://api.zippopotam.us/us/"+zip,
		success: function(data) {
			city = data.places[0]['place name'];
			state = data.places[0]['state abbreviation'];
		}
	});
}
</script>

</script>


</HTML>