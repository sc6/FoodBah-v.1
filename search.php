<!DOCTYPE HTML>

<?php

include("functions/connect.php");

class FoodItem {
	public $id;
	public $vendor;
    public $food;
    public $price;
	public $description;
	public $order;
	public $rating;
	public $image_name;
	
	function __construct($_id, $_vendorid, $_food, $_price, $_description, $_order, $_rating, $_image_name) {
		$this->id = $_id;
		$this->vendor = $_vendorid;
		$this->food = $_food;
		$this->price = $_price;
		$this->description = $_description;
		$this->order = $_order;
		$this->rating = $_rating;
		$this->image_name = $_image_name;
	}
}

$error_code = null;

$search_results = array();
$search_vendors = array();


if(isset($_POST['search_bar_main']) && preg_match('/^[a-zA-Z ]{3,120}$/', $_POST['search_bar_main']) === 1) {
	//--MENUS--	
	$query = "SELECT * FROM `dayztbns_foodbah`.`Menus` WHERE `food` LIKE '%".$_POST['search_bar_main']."%' OR `tags` LIKE '%".$_POST['search_bar_main']."%' ORDER BY `item_rating` DESC LIMIT 0,50";
	$result = mysqli_query($conn, $query);
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$query = "SELECT * FROM `dayztbns_foodbah`.`Menu_Images` WHERE menu_id = ".$row['id']." ORDER BY points LIMIT 0,1;";
		$result_B = mysqli_query($conn, $query);
		$row_B = mysqli_fetch_array($result_B, MYSQLI_ASSOC);
		
		$image_name = $row_B['name']. '.' .$row_B['extension'];
		$tok = strtok($row['item_rating'], ","); //trickery here, extract item rating percentage
		if($tok === "0" || $tok == "") $tok = "new"; else $tok = $tok."%";
		
		$search_results[] = new FoodItem($row['id'], $row['vendor_id'], $row['food'], number_format((($row['price'])/100), 2), $row['description'], $row['order'], $tok, $image_name);

	}
	
	mysqli_free_result($result);
	
	//find unique vendors
	$query = "SELECT DISTINCT vendor_id FROM `dayztbns_foodbah`.`Menus` WHERE food LIKE '%".$_POST['search_bar_main']."%' OR tags LIKE '%".$_POST['search_bar_main']."%' ORDER BY item_rating LIMIT 0,50";
	$result = mysqli_query($conn, $query);
	
	while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		$search_vendors[] = $row['vendor_id'];
	}
	mysqli_free_result($result);
}
else {
	$error_code = "bad_search";
}

?>

<HTML>


<HEAD>
<meta name="robots" content="noindex">
<?php include("functions/imports.php"); ?>
<title><?=$_POST['search_bar_main']?> Results - FOODBAH</title>
</HEAD>

<BODY>
	<?php include("functions/login_bar.php"); ?>
	<script src="/functions/ads.js"></script>
	<?php include("functions/header_bar.php"); ?>
	
	<div class="content_wrapper">
	
		<?php
		echo "
		<h4>'" . $_POST['search_bar_main'] . "' Results</h4>
		<br>
		";
		?>
	
		<?php
		foreach ($search_vendors as $vendor_id) {
			$query = "SELECT * FROM `dayztbns_foodbah`.`Vendors` WHERE id=$vendor_id;";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			echo "
			<div class='search_vendor_info'>
			<table>
				<tr>
					<td class='search_vendor_image'>
						<!--<img src='/assets/title_img/$vendor_id.jpg' class='search_vendor_image'>-->
					</td>
					<td class='search_vendor_name'>
						<a href='/vendor/?q=$vendor_id'>".$row['vendor_name']."</a>
					</td>
				</tr>
			</table>
			</div>
			";
			
			foreach ($search_results as $item) {
				if($item->vendor == $vendor_id) {
					echo "
						<div class='search_item_info'>
							<div class='search_item_name'>
								<a href='/item/?q=".$item->id."'>".$item->food."</a>
							</div>
						
							<div class='search_item_price'>
								".$item->price."
							</div>

							<div class='search_item_expand'>
								<img class='search_item_image' src=/assets/$vendor_id/".$item->image_name.">
							</div>
							
						</div>
						
						<div class='search_item_rating'>
							".$item->rating."
						</div>
						<br>
					";
				}
			}
			
			echo '<br><br>';
			mysqli_free_result($result);
		}
		?>
		
		This search section is still in development. Please excuse any bugs.<br>
		<a href="/index.php">Go back to index</a>
		
		<br><br><br><br><br>
		
		
	</div>
	
</BODY>
</HTML>