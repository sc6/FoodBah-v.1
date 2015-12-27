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
$menu_finished 	=	$row["menu_finished"];

$query = "SELECT DISTINCT `category` FROM `dayztbns_foodbah`.`Menus` WHERE vendor_id = $vendor_id;";
$result = mysqli_query($conn, $query);

$vendor_categories = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
	$vendor_categories[] = $row['category'];
mysqli_free_result($result);


?>
<HTML>


<HEAD>
<meta name="robots" content="noindex">
<?php include("../functions/imports.php"); ?>
<title>Add to <?=$vendor_name?> - FOODBAH</title>

<script>
function checkCategory() {
	var selected = $("#add_category option:selected").text();
	if(selected == "Other (Not Listed)") {
		$("#addCategoryOther").fadeIn();
	}
	else $("#addCategoryOther").css("display", "none");
}

function addCategoryOther() {
	var value = $('#otherInput').val();
	$("#other").attr("value", value);
	$("#other").text(value);
}
</script>

</HEAD>

<BODY class="plain">
	<?php include("../functions/login_bar.php"); ?>
	<script src="/functions/ads.js"></script>
	<?php include("../functions/header_bar.php"); ?>
	
	<?//--FORM INPUT (ADD_ITEM)--
	$catalog_num = 9999;
	$error = "";
	
	if(isset($_POST['add_item']) && isset($_POST['add_price']) && isset($_SESSION['user_name']) && isset($_POST['add_category'])) {
		
		//*****************
		// SECURITY
		//*****************
		function sc_sanitizeString($db, $str) {
			//$str = str_replace('"', '&quot;', $str);	//replace "
			//$str = str_replace("'", '&#39;', $str);		//replace '
			$str = stripslashes($str);					//strip slashes
			$str = htmlentities($str);
			$str = mysqli_real_escape_string($db, $str);		//final 'catch-all'
			return $str;
		}
		
		$add_item = sc_sanitizeString($conn, $_POST['add_item']);
		$add_category = sc_sanitizeString($conn, $_POST['add_category']);
		$add_price = $_POST['add_price'];
		
		//******************
		//******************
		
		$query = "SELECT * FROM dayztbns_foodbah.Menus WHERE category='$add_category' AND vendor_id=$vendor_id;";
		$result = mysqli_query($conn, $query);
		if(mysqli_num_rows($result) == 0) {
			$query = "SELECT DISTINCT category FROM `dayztbns_foodbah`.`Menus` WHERE vendor_id=$vendor_id";
			$result_B = mysqli_query($conn, $query);
			$catalog_num = ((mysqli_num_rows($result_B)+1) * 100) + 1;
			mysqli_free_result($result_B);
		}
		else {
			$query = "SELECT * FROM dayztbns_foodbah.Menus WHERE category='$add_category' AND vendor_id=$vendor_id ORDER BY `Menus`.`order` DESC";
			$result_C = mysqli_query($conn, $query);
			$row_C = mysqli_fetch_array($result_C, MYSQLI_ASSOC);
			mysqli_free_result($result_C);
			
			$catalog_num = $row_C['order'] + 1;
		}
		mysqli_free_result($result);
		
		if ($stmt = mysqli_prepare($conn, "INSERT INTO `dayztbns_foodbah`.`Menus` (`vendor_id`, `food`, `price`, `description`, `category`, `order`) 
				VALUES ('".$_GET['v']."', ?, ?, '', ?, ?)")) {
			$price = filter_var($_POST['add_price'], FILTER_SANITIZE_NUMBER_INT);
			mysqli_stmt_bind_param($stmt, "sisi", $add_item, $price, $add_category, $catalog_num);
			mysqli_stmt_execute($stmt);
			mysqli_stmt_close($stmt);
			$error = $add_item . ' has been successfully added.';
		}
	}
	?>
	
	
	
	
	<div class="index_capsule">	
		<div class="index_panel">
		<span class="subheader_1">Add menu item for <?=$vendor_name?></span>
		<br><br>
		<?if($error != "") { ?><?=$error?><br><br> <? } ?>
		<br>

		<?if(isset($_SESSION['user_name'])) { ?>
			<form method="POST" class="add_menu_form">
				<table><tr>
					<td><span class="subheader_3">Enter its name</span></td>
					<td><input type="text" name="add_item" required pattern="^[a-zA-Z0-9 \x22\x27:%\-&\(\)]{3,149}$" class="login_flat"></td>
				</tr><tr>
					<td><span class="subheader_3">Enter its category</span></td>
					<td>
						<select id="add_category" name="add_category" onchange="checkCategory()">
						<? foreach($vendor_categories as $category) { ?>
							<option value="<?=$category?>"><?=$category?></option>
						<? } ?>
						<option id="other" value="">Other (Not Listed)</option>
						</select>
					</td>
					
					<td id="addCategoryOther" <?if(count($vendor_categories) != 0) {?> style="display:none" <? } ?>>
						<span class="subheader_3">Other:</span> <input type="text" id="otherInput" onkeyup="addCategoryOther()" class="login_flat">
					</td>
					
				</tr><tr>
					<td><span class="subheader_3">Enter its price</span></td>
					<td><input type="text" name="add_price" required pattern="^\$?[0-9]{1,5}\.[0-9]{2}$" class="login_flat"></td>
				</tr><tr>
					<td><span class="subheader_3">And you're done</td>
					<td><input class="login_flat" type="submit" value="Add Item" name="submit"></td>
				</tr></table>
			</form>
			<br><br>
			<?if($error != "") { ?><a href="/vendor/?q=<?=$vendor_id?>">I'm done, take me back.</a>
			<? } else { ?><a href="/vendor/?q=<?=$vendor_id?>">Never mind, take me back.</a><? } ?>
			
		<? } else {?>
			<p>
			Sorry, you must be logged in to access this section.
			</p>
		<? } ?>
		
		</div>
	</div>
	
	<br><br><br>
	
</BODY>
</HTML>