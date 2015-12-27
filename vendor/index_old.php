<!DOCTYPE HTML>
<!-- INDEX for VENDOR -->

<?php
include("../functions/connect.php");
include("../functions/admins.php");

class FoodItem {
	public $id;
    public $food;
    public $price;
	public $description;
	public $order;
	public $rating;
	
	function __construct($_id, $_food, $_price, $_description, $_order, $_rating) {
		$this->id = $_id;
		$this->food = $_food;
		$this->price = $_price;
		$this->description = $_description;
		$this->order = $_order;
		$this->rating = $_rating;
	}
}


//--VENDORS--
$query = "SELECT * FROM dayztbns_foodbah.Vendors WHERE id=".$_GET['q'].";";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

$vendor_name 		= $row["vendor_name"];	
$vendor_id 			= $_GET["q"];
$vendor_tags		= array();
$menu_finished 		= $row["menu_finished"];
$vendor_address_1 	= $row["address_1"];
$vendor_address_2 	= $row["address_2"];
$vendor_phone		= $row["phone"];
$vendor_hours_raw 	= $row["hours"];
$vendor_hours		= array();

$tok = strtok($row["tags"], ", ");
while ($tok !== false) {
	$vendor_tags[] = $tok;
	$tok = strtok(", ");
}

$tok = strtok($vendor_hours_raw, ",");
while ($tok !== false) {
	$vendor_hours[] = $tok;
	$tok = strtok(",");
}

function formatPhone($phone_number) {
	if(strlen($phone_number) == 11 && substr($phone_number, 0, 1) == "1") {
		return "+1 (".substr($phone_number, 1, 3).") ".substr($phone_number, 4, 3)."-".substr($phone_number, 7, 4);
	}
	else return $phone_number;
}


$vendor_phone = formatPhone($vendor_phone);


//--MENUS--
$query = 'SELECT DISTINCT category FROM dayztbns_foodbah.Menus WHERE vendor_id='.$_GET['q'].' ORDER BY `order` ASC;';
$result = mysqli_query($conn, $query);
$num_rows_b = mysqli_num_rows($result);

$menu_categories = array();
$menu_items = array();


while ($row_b = mysqli_fetch_assoc($result)) {
	$menu_categories[] = $row_b['category'];
}
mysqli_free_result($result);

if(!isset($_GET['m']) || $_GET['m'] === 'pictoral') {
	for ($i = 0; $i < $num_rows_b; $i++) {
		$query = "SELECT * FROM dayztbns_foodbah.Menus WHERE vendor_id=".$_GET['q']." AND category='".$menu_categories[$i]."' ORDER by `order` ASC";
		$result = mysqli_query($conn, $query);
		$num_rows_c = mysqli_num_rows($result);
		$menu_items[$i] = array();
		while ($row_c = mysqli_fetch_assoc($result)) {
			$menu_items[$i][] = new FoodItem($row_c['id'], $row_c['food'], $row_c['price'], $row_c['description'], $row_c['order'], $row_c['item_rating']);
		}
		mysqli_free_result($result);
	}
}
else if($_GET['m'] === 'top') {
	for ($i = 0; $i < $num_rows_b; $i++) {
		$query = "SELECT * FROM dayztbns_foodbah.Menus WHERE vendor_id=".$_GET['q']." AND category='".$menu_categories[$i]."' ORDER by `item_rating` DESC";
		$result = mysqli_query($conn, $query);
		$num_rows_c = mysqli_num_rows($result);
		$menu_items[$i] = array();
		while ($row_c = mysqli_fetch_assoc($result)) {
			$menu_items[$i][] = new FoodItem($row_c['id'], $row_c['food'], $row_c['price'], $row_c['description'], $row_c['order'], $row_c['item_rating']);
		}
		mysqli_free_result($result);
	}
}
else if($_GET['m'] === 'new') {
	for ($i = 0; $i < $num_rows_b; $i++) {
		$query = "SELECT * FROM dayztbns_foodbah.Menus WHERE vendor_id=".$_GET['q']." AND category='".$menu_categories[$i]."' AND item_rating='';";
		$result = mysqli_query($conn, $query);
		$num_rows_c = mysqli_num_rows($result);
		$menu_items[$i] = array();
		while ($row_c = mysqli_fetch_assoc($result)) {
			$menu_items[$i][] = new FoodItem($row_c['id'], $row_c['food'], $row_c['price'], $row_c['description'], $row_c['order'], $row_c['item_rating']);
		}
		mysqli_free_result($result);
	}
}

?>




<HTML>



<HEAD>
<?php include("../functions/imports.php"); ?>
<script src="/assets/jquery.bpopup.min.js"></script>
<title><?=$vendor_name?> Menu - FOODBAH</title>
</HEAD>





<BODY>
	<?php include("../functions/login_bar.php"); ?>

	<script src="/functions/ads.js"></script>
	<?php include("../functions/header_bar.php"); ?>
	
	
	<div class="content_wrapper">
		
		<!--Image Header-->
		<div class="vendor_header">
			<script>
				var img_height;
				function translateDown() {
					$('#vendor_header_img').animate({
						top: "-"+(img_height-300)+"px"
					}, 30000, "linear", function() {
						$(this).delay(10000);
						translateUp();
					}
					);
				}
				function translateUp() {
					$('#vendor_header_img').animate({
						top: "0px"
					}, 30000, "linear", function() {
						$(this).delay(10000);
						translateDown();
					}
					);
				}
				function fireTranslation() {
					img_height = $('#vendor_header_img').height();
					$('#vendor_header_img').css('top', '0px');
					translateDown();
				}
			</script>
			<? if(file_exists("../assets/title_img/$vendor_id.jpg")) { ?>
				<img src="../assets/title_img/<?=$vendor_id?>.jpg" id="vendor_header_img" onload="fireTranslation();">
			<? } ?>
			<p id="name">
				<?=$vendor_name?>
			</p>
		</div>
		<!--END Image Header-->
		
		
		<div class="menu_wrapper">

			<?php
			if($menu_finished == 1){ //change back to 0 to enable menu_add
			?>
				<div class="add_menu_prompt">
					This menu is incomplete. <em>Click here to help finish it</em>.
					<img src="../assets/site_img/cancel.png" onMouseOver="this.src='../assets/site_img/cancel_hover.png'" onMouseOut="this.src='../assets/site_img/cancel.png'"width="23px" class="add_prompt_cancel"/>
				</div>
			<?php
			}
			?>
			<!--Add to Menu-->
			
			<!--Upload Image Setup-->
			<div id="upload_image_dialog" class="hidden uploadImageWindow">
			</div>
			
			<script>
			function upload_image(item_id, vendor_id) {
				$("#upload_image_dialog").bPopup({
					content:'iframe',
					loadUrl: '/item/upload_image_2.php?q='+item_id+'&v='+vendor_id
				});
			}
			</script>
			<!--Upload Image Setup-->
			
			<br><br>		
			
			<!--Menu Items-->
			<div class="menu_left">			
				<!--Vendor Info Pane-->
				<div class="vendor_info">
					<? if($vendor_address_1 != "") { ?>
						<strong>Address</strong><br>
						<?=$vendor_address_1?><br>
						<?=$vendor_address_2?>
						<br><br>
					<? } ?>
					
					<? if($vendor_phone != "") { ?>
						<strong>Phone</strong><br>
						<?=$vendor_phone?>
						<br><br>
					<? } ?>
					
					<? if($vendor_hours_raw != "") { ?>
						<strong>Hours</strong><br>
						Sunday: <span id="right"><?=$vendor_hours[0]?> - <?=$vendor_hours[1]?></span>
						<br>
						Monday: <span id="right"><?=$vendor_hours[2]?> - <?=$vendor_hours[3]?></span>
						<br>
						Tuesday: <span id="right"><?=$vendor_hours[4]?> - <?=$vendor_hours[5]?></span>
						<br>
						Wednesday: <span id="right"><?=$vendor_hours[6]?> - <?=$vendor_hours[7]?></span>
						<br>
						Thursday: <span id="right"><?=$vendor_hours[8]?> - <?=$vendor_hours[9]?></span>
						<br>
						Friday: <span id="right"><?=$vendor_hours[10]?> - <?=$vendor_hours[11]?></span>
						<br>
						Saturday: <span id="right"><?=$vendor_hours[12]?> - <?=$vendor_hours[13]?></span>
						<br><br>
					<? } ?>
					<? if(isset($_SESSION['user_name'])) { ?>
						<a href="add_to_menu.php?v=<?=$vendor_id?>">Add Menu Item</a><br>
					<? } ?>
					
					<? if(!file_exists("../assets/title_img/$vendor_id.jpg")) { ?>
						<a href="change_vendor_picture.php?v=<?=$vendor_id?>">Change Header Image</a><br>
					<? } ?>
					
					<?if(isset($_SESSION['user_name']) && $admins[$_SESSION['user_name']] === 'admin') { ?>
						
						<a href="change_vendor_picture.php?v=<?=$vendor_id?>">ADMIN: Change Picture</a>
					<? } ?>
				</div>
				<!--END Vendor Info Pane-->
			
			<?php
				echo '
					<script>
					$(".menu_left").css("width", "100%");
					</script>
					';

				for($i = 0; $i < count($menu_categories); $i++) {
					echo '<h4>'.$menu_categories[$i].'</h4>';
					foreach($menu_items[$i] as $obj) {
						$tok = strtok($obj->rating, ","); //trickery here to extract rating%
						if($tok === "0" || $tok == "") $tok = "new"; else $tok = $tok."%";
						
						//MENU_IMAGES
						$query = "SELECT * FROM dayztbns_foodbah.Menu_Images WHERE menu_id=".$obj->id." ORDER BY points DESC;";
						$result = mysqli_query($conn, $query);
						$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
							
						$image_path = "/assets/$vendor_id/".$row['name']."-icon-sm.jpg";
								
						mysqli_free_result($result);
						
						echo "
						<a href='/item/?q=".$obj->id."' class='a_hidden'>
							<div class='menu_item_pictoral' id=item_".$obj->id.">";
							if(isset($_SESSION['user_name']) && ($row['name'] == '' && $row['extension'] == '')) {
								//echo "<a onclick='upload_image(".$obj->id.", $vendor_id)'>[+ Image]</a><br><br>"; //add image (logged in)
							}
							echo
							($row['name'] != '' && $row['extension'] != '' ? "<img src='$image_path'>" : "").
								"<p>
								".$obj->food."
								<br><br>
								 $".number_format((($obj->price)/100), 2)."
								(".$tok.")
								</p>
							</div>
						</a>
							";
					}
					echo '<br /><br />';
				}
			?>	
			</div>
			<!--Menu Items-->
			
			
			<div class="menu_right">
			</div>
		</div>
	</div>
	
	<br><br><br><br><br>
	
	<script>
	$(document).ready(function(){
		var open_add_menu = true;
		
		<?php
			//handles the background color at hover and selected
			for($i = 0; $i < count($menu_categories); $i++) {
				foreach($menu_items[$i] as $obj) {
					echo '$("#'.$obj->id.'").click(function(){';
					echo    '$(".menu_item_selected").removeClass("menu_item_selected");';
					echo    '$(this).addClass("menu_item_selected");';
					echo '});';
				}
			}
		?>
		
		$(".add_menu_prompt").click(function() {
			$(this).fadeOut(200, function() {
				$(".add_menu_wrapper").fadeIn();
			});
		});
		
		$(".add_prompt_cancel").click(function(e) {
			$(".add_menu_prompt").fadeOut();
			e.stopPropagation();
		});
		
		$(".add_menu_cancel").click(function() {
			$(".add_menu_wrapper").fadeOut(function() {
				$(".add_menu_name").val("Example: Cheeseburger");
				$(".add_menu_price").val("$5.99");
			});
		});
	})
	</script>

	<script src="https://www.google.com/recaptcha/api.js" async defer></script>

</BODY>


</HTML>