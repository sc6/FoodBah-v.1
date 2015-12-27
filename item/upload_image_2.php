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
$image_path = "/assets/$vendor_id/".$row['name'].".".$row['extension'];

mysqli_free_result($result);


?>

<HTML>



<HEAD>
<meta name="robots" content="noindex">
<?php include("../functions/imports.php"); ?>
<title><?=$item_name?> Upload Image - FOODBAH</title>
</HEAD>



<BODY>
	<?php //Hidden login bar & imports
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

	//checks if remember me token is active IF not logged in
	if(!isset($_SESSION['user_name']) && isset($_COOKIE['rememberme'])) {
		$db_temp = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		$query = "SELECT * FROM auth_tokens WHERE token = '".$_COOKIE['rememberme']."'";
		$result = $db_temp->query($query);
		$row = $result->fetch_array();
		
		$_SESSION['user_name'] = $row['user_name'];
		$_SESSION['user_login_status'] = 1;
	}
	?>

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
	$error_upload = "";
	if(isset($_FILES["fileToUpload"]) && isset($_SESSION['user_name'])) {
		$target_dir = "../assets/".$_GET['v']."/";
		$target_file = $target_dir . $item_id . "-" . ($image_qty+1) . "." . pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
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
				
				//INSERT INTO MENU_IMAGES
				$query = "INSERT INTO `dayztbns_foodbah`.`Menu_Images` (`menu_id`, `user_name`, `name`, `points`, `timestamp`, `extension`) VALUES ('$item_id', '".$_SESSION['user_name']."', '".$item_id . "-" . ($image_qty+1)."', '0', '".time()."', '$imageFileType');";
				$result = mysqli_query($conn, $query);
				$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
				mysqli_free_result($result);
				
				$user_id = $row['user_id'];
				
				
				//CREATE img_name-icon.jpg - Create square icon of image (on the server copy)
				$ser_filename = "../assets/$vendor_id/$item_id-" . ($image_qty+1) . "." . pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);
				$og_size = getimagesize($ser_filename);
				$og_width = $og_size[0];
				$og_height = $og_size[1];
				
				$og_min_length = min($og_width, $og_height);
				
				$left = ($og_width/2) - ($og_min_length/2);
				$top = ($og_height/2) - ($og_min_length/2);
				
				$canvas = imagecreatetruecolor(($og_min_length), ($og_min_length));
				
				if(pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION) == 'jpg' || pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION) == 'jpeg') $current_image = imagecreatefromjpeg($ser_filename);
				else if(pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION) == 'png') $current_image = imagecreatefrompng($ser_filename);
				else if(pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION) == 'gif') $current_image = imagecreatefrompng($ser_filename);
				else echo 'error #1';
				
				imagecopy($canvas, $current_image, 0, 0, $left, $top, $og_min_length, $og_min_length);
				imagejpeg($canvas, "../assets/$vendor_id/$item_id-" . ($image_qty+1) . "-icon.jpg", 100);
				
				
				//CREATE img_name-icon-sm.jpg
				$thumb = new Imagick("../assets/$vendor_id/$item_id-" . ($image_qty+1) . "-icon.jpg");
				$thumb->resizeImage(120,120,Imagick::FILTER_LANCZOS,1);
				$thumb->writeImage("../assets/$vendor_id/$item_id-" . ($image_qty+1) . "-icon-sm.jpg");
				$thumb->destroy();
				
				
			} else {
				//echo "Sorry, there was an error uploading your file.";
			}
		}
	}
	?>
	
	
	<div class="content_wrapper" style="width:700px; background-color:#DDDDDD; padding:25px;">
		<div class="add_rating_wrapper">
			<h4>
			Upload an Image
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
			
			<?if($error === 'not_logged_in') {?>
				Image submission is for logged in users only. Becoming a user is quick, and will take less
				than a minute of your time. This is something we must do in order to combat fake or misleading submissions. 
				Your contributions would be greatly appreciated.
				<br><br>
				<a href="/login/register.php">You can register by clicking here.</a>&nbsp;&nbsp;<a href="/login/">(Or, login here)</a>
				<br><br><br>
				<a href="/item/?q=<?=$item_id?>">Back to <?=$item_name?></a>
			
			
			<?} else {?>
				<div class="submission_item">
					<form method="POST" enctype="multipart/form-data">
						Select image to upload:
						<?
						if($error_upload === "Image upload successful.") echo '<br><br><span style="color:green; font-size:12px;">'.$error_upload.'</span>';
						elseif($error_upload != "") echo '<br><br><span style="color:red; font-size:12px;">'.$error_upload.'</span>';
						
						?>
						
						<br><br>
						<input type="file" name="fileToUpload" id="fileToUpload">
						</div>
						<br><br>
						<input class="add_rating_submit" type="submit" value="Upload Image" name="submit">
					</form>
					<br><br><br>
				
			<? } ?>
		</div>
	</div>
	
</BODY>

</HTML>