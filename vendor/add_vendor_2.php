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

<BODY class="plain">
	<?php include("../functions/login_bar.php"); ?>
	<script src="/functions/ads.js"></script>
	<?php include("../functions/header_bar.php"); ?>
	
	<?//--FORM PROCESSING (ADD_VENDOR)--
	
	if(isset($_POST['add_name']) && isset($_POST['add_address']) && isset($_POST['add_zip']) && isset($_POST['add_phone']) && isset($_SESSION['user_name'])) {
		
		if (preg_match('/^.{3,149}$/', stripslashes($_POST['add_name'])) === 1 && 
			preg_match('/^.{3,149}$/', stripslashes($_POST['add_address'])) === 1 &&
			preg_match('/^[0-9]{5,5}$/', stripslashes($_POST['add_zip'])) === 1 &&
			preg_match('/^[\d\-\(\)\s]{10,20}$/', stripslashes($_POST['add_phone'])) === 1)
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
			$state = $json['places'][0]['state abbreviation'];
			
			//Make address
			$address_line_2 = "$city, $state $zip";
			
			//Format phone number
			$phone = preg_replace("/[^0-9]/", "", $phone);
			if(strlen($phone) == 10) $phone = "1" + $phone;
			
			//****************************
			//FINAL SECURITY PROCEDURES
			//****************************
				//set charset to utf8
				//if (mysqli_set_charset($conn, "utf8")) {
				//	echo 'Something *may* have gone wrong... (100)';
				//}
			//
				//strip anything dangerous
				$name = mysqli_real_escape_string($conn, strip_tags($_POST['add_name'], ENT_QUOTES));
                $address = mysqli_real_escape_string($conn, strip_tags($_POST['add_address'], ENT_QUOTES));
				$phone = mysqli_real_escape_string($conn, strip_tags($phone, ENT_QUOTES));
				
			//******************************
			//END FINAL SECURITY PROCEDURES
			//******************************
			
			//Insert into Vendors table
			$query = "INSERT INTO `dayztbns_foodbah`.`Vendors` (`vendor_name`, `menu_finished`, `address_1`, `address_2`, `phone`) VALUES ('$name', '0', '$address', '$address_line_2', '$phone');";
			mysqli_query($conn, $query);
			
			if(isset($_FILES['fileToUpload'])) {
			
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
			}
			
			//Success- do this
			$query = "SELECT * FROM `dayztbns_foodbah`.`Vendors` ORDER BY id DESC LIMIT 0,1;";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			mysqli_free_result($result);

			$u_id = $row['id'];
		
			echo stripslashes($name)." has been added successfully. <a onclick=\"window.top.location.href = 'http://foodbah.com/vendor/?q=$u_id'\" style='cursor:pointer'>Take a look.</a><br><br>";
		}
		else {
			echo 'Something went wrong. (Error 00)';
		}
	}
	else {
		//echo 'Something went wrong. (Error: 01)';
	}
	?>
	
	
	
	
	<div class="index_capsule">	
		<div class="index_panel">
			<span class="subheader_1">Add a Vendor</span>
			<br><br>
		
			<?if(isset($_SESSION['user_name'])) { ?>
				<script>
					function zip_validation() {
						var zip = $("#add_zip").val();
						
						if(/^[0-9]{5,5}$/.test(zip)) {
							var city = "test";
							var state = "test";
							
							$.ajax({
								url: "http://api.zippopotam.us/us/"+zip,
								success: function(data) {
									city = data.places[0]['place name'];
									state = data.places[0]['state abbreviation'];
									$("#zip_return").html(city + ", " + state);
								}
							});	
						}
						else {
							$("#zip_return").html("unknown zip code");
						}
					}
					
					function phone_validation() {
						var phone = $("#add_phone").val().replace(/\D/g,'');	//strips non-numeric characters
						
						if(/^[0-9]{10,11}$/.test(phone)) {
							if(/^[0-9]{10}$/.test(phone)) {
								$("#phone_return").html("1-" + phone.substring(0,3) + "-" + phone.substring(3,6) + "-" + phone.substring(6,10));
							}
							else {
								$("#phone_return").html("1-" + phone.substring(1,4) + "-" + phone.substring(4,7) + "-" + phone.substring(7,11));
							}
						}
						else {
							$("#phone_return").html("unknown phone format");
						}
						
						$("#add_phone").html(phone);
					}
					
				</script>
				
				<form method="POST" class="add_menu_form" enctype="multipart/form-data">
				
					<strong>1. What is this place called?</strong><br>
					<input type="text" name="add_name" required pattern="^.{3,149}$" class="input_style_1">
					<br><br>
					
					<strong>2a. What's its street address?</strong><br>
					<input type="text" name="add_address" required pattern="^.{3,149}$" class="input_style_1">
					<br><br>
					
					<strong>2b. Zip code?</strong><br>
					<input onblur="zip_validation()" id="add_zip" type="text" name="add_zip" required pattern="^[0-9]{5,5}$" class="input_style_1" id="getZip">
					&nbsp;<span class="subheader_3" id="zip_return"></span>
					<br><br>
					
					<strong>3. Phone number?</strong><br>
					<input type="text" onblur="phone_validation()" id="add_phone" name="add_phone" required pattern="^[\d\-\(\)\s]{10,20}$" class="input_style_1" id="getPhone">
					&nbsp;<span class="subheader_3" id="phone_return"></span>
					<br><br>
					
					<strong>3. What does this place look like?</strong> <em>(Optional)</em><br>
					<input type="file" name="fileToUpload" id="fileToUpload" class="input_style_1">
					<br><br><br>
					
					<input type="submit" name="submit" value="Add Vendor" class="input_style_1">
					&nbsp;&nbsp;<a href="/">Go back to home page</a>

				</form>
			<? } else {?>
				<p>
				Sorry, you must be logged in to access this area.
				</p>
			<? } ?>
			
		</div>
	</div>
	
	<br><br><br>
	
</BODY>


</script>


</HTML>