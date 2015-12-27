<?php

include('../functions/connect.php');

$query = "SELECT * FROM dayztbns_foodbah.Menus WHERE vendor_id=".$_GET['q']." AND category='".$menu_categories[$i]."' ORDER by `order` ASC";
	$result = mysqli_query($conn, $query);
	$num_rows_c = mysqli_num_rows($result);
	
	$menu_items[$i] = array();
	
	while ($row_c = mysqli_fetch_assoc($result)) {
		$menu_items[$i][] = new FoodItem($row_c['food'], $row_c['price'], $row_c['description'], $row_c['order']);
	}
	
	mysqli_free_result($result);

?>


$(document).ready(function(){
	<?php
		//creates a toggle function to open menu_right for each menu item id
		echo count($menu_items[$i]);
		foreach($menu_items[$i] as $obj) {
			echo '$(#'.$obj->order.').click(function() {';	
			echo '	$(".menu_right").toggle(function() {';
			echo '	});';
			echo '});';
		}
	?>
})

<?php
header("Content-type: text/javascript");
exit();
?>
