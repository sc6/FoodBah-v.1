<!DOCTYPE>

<HTML>

<?	//--init--//
	include("../functions/connect.php");
	include("../functions/admins.php");
	
	
	function formatPhone($phone_number) {
		if(strlen($phone_number) == 11 && substr($phone_number, 0, 1) == "1") {
			return "+1 (".substr($phone_number, 1, 3).") ".substr($phone_number, 4, 3)."-".substr($phone_number, 7, 4);
		}
		else return $phone_number;
	}
	
	
	$items = array();
	$categories = array();
	
	
	//Get vendor information
	$query = "SELECT * FROM `dayztbns_foodbah`.`Vendors` WHERE id=".$_GET['q'].";";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	$vendor_id = $_GET['q'];
	$vendor_title = $row['vendor_name'];
	$vendor_address_1 = $row['address_1'];
	$vendor_address_2 = $row['address_2'];
	$vendor_address = $vendor_address_1 . "<br>" . $vendor_address_2;
	$vendor_phone = formatPhone($row['phone']);
	
	
	//Add items into $items array['category']['food_name']['attr_1']...
	$query = 'SELECT DISTINCT category FROM dayztbns_foodbah.Menus WHERE vendor_id='.$_GET['q'].' ORDER BY `order` ASC;';
	$result = mysqli_query($conn, $query);
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$category = $row['category'];
		$$category = array();
		$categories[] = $category;
		$query = 'SELECT * FROM dayztbns_foodbah.Menus WHERE vendor_id='.$_GET['q'].' AND category = "'.$category.'" ORDER BY `order` ASC;';
		$resultB = mysqli_query($conn, $query);
		while($rowsB = mysqli_fetch_array($resultB, MYSQLI_ASSOC)) {
			$$category[$rowsB['food']] = array();
			${$category}[$rowsB['food']]['name'] = $rowsB['food'];
			${$category}[$rowsB['food']]['price'] = number_format($rowsB['price']/100, 2);
			${$category}[$rowsB['food']]['description'] = $rowsB['description'];
			${$category}[$rowsB['food']]['id'] = $rowsB['id'];
			${$category}[$rowsB['food']]['rating'] = $rowsB['item_rating'];
			${$category}[$rowsB['food']]['tags'] = array();
			${$category}[$rowsB['food']]['image'] = "";
			
			//get item image top
			$query = "SELECT * FROM dayztbns_foodbah.Menu_Images WHERE menu_id=".${$category}[$rowsB['food']]['id']." ORDER BY points DESC;";
			$resultC = mysqli_query($conn, $query);
			$rowC = mysqli_fetch_array($resultC, MYSQLI_ASSOC);
			${$category}[$rowsB['food']]['image'] = "/assets/$vendor_id/".$rowC['name']."-icon-sm.jpg";
			
			//get item tags as array
			$tok = strtok($rowsB["tags"], ", ");
			while ($tok !== false) { //extracts tags into array
				${$row['category']}[$rowsB['food']]['tags'][] = $tok;
				$tok = strtok(",");
			}
			
			//get item rating %
			$tok = strtok($rowsB['item_rating'], ","); //extracts rating[0]
			if($tok === "0" || $tok == "") $tok = "no";
			${$category}[$rowsB['food']]['rating'] = $tok;
		}
	}
?>



<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title><?=$vendor_title?> Menu - FOODBAH</title>
	
	<!--<link href="/assets/style/style.css" rel="stylesheet" type="text/css" />-->
    <link href="./style.css" rel="stylesheet" type="text/css" />
	
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'>
	
	
	<link href='http://fonts.googleapis.com/css?family=Roboto:300' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Slabo+27px' rel='stylesheet' type='text/css'>
	
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	<link rel="icon" href="/favicon.ico" type="image/x-icon">
	
	<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>

</head>

<body>

	<?php include("../functions/login_hidden.php"); ?>

	<div class="top" id="top">
		<table width="100%">
			<tr>
				<td style="width:40%;color:white;">
					<span class="vendor_title"><?=$vendor_title?></span>
				</td>
				<td style="width:30%;color:white;text-align:center;">
					<span class="vendor_address"><?=$vendor_address?></span>
					<br><br>
					<span class="vendor_phone"><?=$vendor_phone?></span>
				</td>
				<td style="width:20%">
					<iframe
						frameborder="0" style="border:0;opacity:0.85;"
						src="https://www.google.com/maps/embed/v1/place?key=AIzaSyAiW37FcBHQT20y-vRnXF1-I4Pqcrg8Rg8
						&q=<?=$vendor_address_1."%20".$vendor_address_2?>">
					</iframe>
				</td>
			</tr>
		</table>
		<br>
		<br>
		&nbsp;&nbsp;
		<!--my account-->
		<?if(isset($_SESSION['user_name'])) { ?>
			<a href="/login" style="text-decoration:underline;color:inherit"><?=$_SESSION['user_name']?></a>
		<? } else { ?>
			<a href="/login" style="text-decoration:underline;color:inherit">Log in</a>
		<? } ?>
		&nbsp;::&nbsp;
		<a href="/" style="text-decoration:underline;color:inherit">Go back</a>
		&nbsp;-&nbsp;
		<a href="./add_to_menu.php?v=<?=$vendor_id?>" style="text-decoration:underline;color:inherit">Add a missing item</a>
		<!--option:change vendor image-->
		<? if(!file_exists("../assets/title_img/$vendor_id.jpg")) { ?>
			&nbsp;-&nbsp;
			<a href="change_vendor_picture.php?v=<?=$vendor_id?>" style="text-decoration:underline;color:inherit">Change Header Image</a><br>
		<? } ?>
	</div>

	<div class="left" id="left">
		<br>
		<br>
		<span class="vendor_title"><?=$vendor_title?></span>
		<br>
		<br>
		<span class="vendor_address"><?=$vendor_address?></span>
		<br>
		<br>
		<span class="vendor_phone"><?=$vendor_phone?></span>
		<br>
		<br>
		<br>
		<br>
		<iframe
			frameborder="0" style="border:0;width:85%;height:450px;opacity:0.85;"
			src="https://www.google.com/maps/embed/v1/place?key=AIzaSyAiW37FcBHQT20y-vRnXF1-I4Pqcrg8Rg8
			&q=<?=$vendor_address_1."%20".$vendor_address_2?>">
		</iframe>
		<br>
		<br>
		<br>
		<!--option:my account-->
		<?if(isset($_SESSION['user_name'])) { ?>
			<a href="/login" style="text-decoration:underline;color:inherit"><?=$_SESSION['user_name']?></a>
		<? } else { ?>
			<a href="/login" style="text-decoration:underline;color:inherit">Log in</a>
		<? } ?>
		<br><br>
		<a href="/" style="text-decoration:underline;color:inherit">Back to index</a>
		<br><br>
		<a href="./add_to_menu.php?v=<?=$vendor_id?>" style="text-decoration:underline;color:inherit">Add a missing item</a>
		<!--option:change vendor image-->
		<? if(!file_exists("../assets/title_img/$vendor_id.jpg")) { ?>
			<br><br>
			<a href="change_vendor_picture.php?v=<?=$vendor_id?>" style="text-decoration:underline;color:inherit">Change Header Image</a><br>
		<? } ?>
	</div>

	<div class="right" id="right">
		<br>
		<br>
		<? if(file_exists("../assets/title_img/$vendor_id.jpg")) { ?>
			<img src="/assets/title_img/<?=$vendor_id?>.jpg" style="width:70%">
			<br>
			<br>
			<br>
			<br>
		<? } ?>
		<?foreach($categories as $category) { ?>
			<a class="menu_category"><?=$category?></a>
			<br>
			<br>
			<br>
			<div class="divider_1"></div>
			<br>
			
			<?foreach($$category as $item_name => $item_attr) { ?>
				<a href="/item/?q=<?=${$category}[$item_name]["id"]?>" class="item_link">
					<table class="menu_item">
						<tr>
							<td rowspan="2" class="item_img_container">
								<?if(file_exists("..".${$category}[$item_name]["image"])) {?>
									<img src="<?=${$category}[$item_name]["image"]?>">
								<? } else echo ""; ?>
							</td>
							<td>
								<span class="item_name"><?=${$category}[$item_name]["name"]?></span> <span class="item_price">$<?=${$category}[$item_name]["price"]?></span>
								<br>
								<span class="item_description"><?=${$category}[$item_name]["description"]?></span>&nbsp;
							</td>
						</tr>
						<tr>
							<td>
								<span class="item_rating_good"><?=${$category}[$item_name]['rating']?>% likes</span>
								<?foreach(${$category}[$item_name]['tags'] as $tag) { ?>
									<span class="item_tag"><?=$tag?></span>&nbsp;
								<? } ?>
							</td>
						</tr>
					</table>
				</a>
				<br><br>
			<? } ?>
			<br>
			<br>
		<? } ?>
	</div>
	
	<br><br>
	<br><br>
	<br><br>
	
	<script>
	if($(window).width() < 1200) {
		$(".left").css("display", "none");
		$(".right").css("overflow-y", "visible");
		$(".right").css("float", "none");
		$(".top").css("display", "inline-block");
	}
	else {
		$(".left").css("display", "inline-block");
		$(".right").css("overflow-y", "scroll");
		$(".right").css("float", "right");
		$(".top").css("display", "none");
	}
	
	$(window).resize(function() {
		if($(window).width() < 1200) {
			$(".left").css("display", "none");
			$(".right").css("overflow-y", "visible");
			$(".right").css("float", "none");
			$(".top").css("display", "inline-block");
		}
		else {
			$(".left").css("display", "inline-block");
			$(".right").css("overflow-y", "scroll");
			$(".right").css("float", "right");
			$(".top").css("display", "none");
		}
	});
	</script>
	
	
</body>


</HTML>