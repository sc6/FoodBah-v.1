<!DOCTYPE HTML>
<!-- INDEX for ITEM -->

<?php
include("../functions/connect.php");
include("../functions/admins.php");

class User {
	public $id;
	public $name;
	
	function __construct($_id, $_name) {
		$this->id = $_id;
		$this->name = $_name;
	}
}


//MENU
$query = "SELECT * FROM dayztbns_foodbah.Menus WHERE id=".$_GET['q'].";";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

$food_name 			= $row['food'];
$item_price			= number_format((float)($row['price']/100), 2, '.', '');
$vendor_id 			= $row['vendor_id'];
$food_id 			= $_GET['q'];
$food_category		= $row['category'];
$item_description 	= $row['description'];
$food_tags 			= array();
$food_nutrition 	= array();

$tok = strtok($row["tags"], ", ");
while ($tok !== false) {
	$food_tags[] = $tok;
	$tok = strtok(", ");
}
if($row["tags"] === '') {
	$food_tags[0] = 'no_tag';
}

$tok = strtok($row["nutrition"], ",");
$counter = 0;
$key = "";
while ($tok !== false) {
	if($counter%2 == 0) $key = $tok;
	else $food_nutrition[$key] = $tok;
	$tok = strtok(",");
	$counter++;
}


//VENDOR
$query = "SELECT * FROM dayztbns_foodbah.Vendors WHERE id=".$vendor_id.";";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

$vendor_name = $row['vendor_name'];


//MENU_IMAGES
$query = "SELECT * FROM dayztbns_foodbah.Menu_Images WHERE menu_id=".$food_id." ORDER BY points DESC;";
$result = mysqli_query($conn, $query);
$image_qty = mysqli_num_rows($result);
while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	
	$image_id[]			= $row['id'];
	$image_points[]		= 0;
	$image_paths[] 		= "/assets/$vendor_id/".$row['name'].".".$row['extension'];
	$image_icon_paths[] = "/assets/$vendor_id/".$row['name']."-icon.jpg";
	$image_authors[] 	= $row['user_name'];
	$image_time[] 		= $row['timestamp'];
		
}
mysqli_free_result($result);


//IMAGE_VOTES
for($i = 0; $i < count($image_id); $i++) {
	$query = "SELECT SUM(value) FROM dayztbns_foodbah.Image_Votes WHERE img_id = ".$image_id[$i].";";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	
	if($row['SUM(value)'] == '') $image_points[$i] = 0;
	else $image_points[$i] = $row['SUM(value)'];
	
	
	$update_query = "UPDATE `dayztbns_foodbah`.`Menu_Images` SET `points` = ".$image_points[$i]." WHERE `Menu_Images`.`id` = ".$image_id[$i].";";
	mysqli_query($conn, $update_query);
	}



//RATINGS
$query = "SELECT * FROM dayztbns_foodbah.Ratings WHERE item_id=".$food_id.";";
$result = mysqli_query($conn, $query);

$rating_sum = 0;
$rating_positives = 0;
$rating_negatives = 0;
$rating_percentage = 0;
$rating_authors = array();
$rating_values = array();
$rating_content = array();
$rating_times = array();

while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
	
	$rating_authors[] 	= $row['user_id'];
	$rating_values[]	= $row['rating'];
	$rating_content[] 	= htmlspecialchars(stripslashes($row['comment']));
	$rating_time[] 		= $row['timestamp'];
	
	if($row['rating'] == 1) $rating_sum++;
}
mysqli_free_result($result);

$rating_percentage = number_format((float)($rating_sum / count($rating_values))*100, 0, '.', '');

for($i = 0; $i < count($rating_authors); $i++) {
	$query = "SELECT * FROM dayztbns_login.users WHERE user_id=".$rating_authors[$i].";";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
	mysqli_free_result($result);
	$rating_authors[$i] = new User($rating_authors[$i], $row['user_name']);
}

//UPDATE ratings on MENU
$query = "UPDATE `dayztbns_foodbah`.`Menus` SET `item_rating` = '$rating_percentage,".count($rating_authors)."' WHERE `Menus`.`id` = $food_id;";
$result = mysqli_query($conn, $query);


?>

<HTML>


<HEAD>
<?php include("../functions/imports.php"); ?>
<title><?=$food_name?> Information - <?=$vendor_name?> - FOODBAH</title>
</HEAD>

<BODY>
	<?php include("../functions/login_bar.php"); ?>
	<script src="/functions/ads.js"></script>
	<?php include("../functions/header_bar.php"); ?>
	
	<?php
	//Image_Votes
	if(isset($_SESSION['user_name'])) {
		for($i = 0; $i < count($image_id); $i++) {
			$query = "SELECT * FROM dayztbns_foodbah.Image_Votes WHERE user_name = '".$_SESSION['user_name']."' AND img_id = ".$image_id[$i].";";
			$result = mysqli_query($conn, $query);
			$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
			if (mysqli_num_rows($result) > 0) {
				$image_rated[$i] = $row['value'];
			}
			mysqli_free_result($result);
		}
	}
?>
	
	
	<div class="content_wrapper">
	
		<!--Breadcrumbs-->
		<div class="breadcrumbs">
		<? echo "<a href='/vendor/?q=$vendor_id'>$vendor_name</a> &nbsp;&gt;&nbsp; <a href='/vendor/?q=$vendor_id'>$food_category</a> &nbsp;&gt;&nbsp; $food_name" ?>
		</div>
		<br /><br />
		
		<div class="full_section_header">

			<!--Title-->
			<div class="item_title"><?=$food_name?></div>
			
			<!--Price-->
			<div class="item_price">$<?=$item_price?></div>
			<br><br>
			
			<!--Description-->
			<div class="item_description">
			<?=$item_description?>
			</div>
			
			<br>
			
		</div>
		
		
		<div class="full_section">
		<br>
			<!--At a glance
			<div class="sub_full_section">
				<span class="subheader">At a Glance:</span>
				<br>
				
				<!--Facts
				<ul>
					<?if(count($rating_values) > 0):?>
						<li><?=$rating_percentage?>% of raters were satisfied with this menu item.</li>
					<?endif;?>
					<li><?=count($rating_values)?> users have rated this item.</li>
				</ul>
				<br>
				
				<!--Tags
				<? if(count($food_tags > 0)) {
					foreach($food_tags as $tag) {
						echo "<li class='item_tag'>$tag</li>";
					}
				}
				?>
				
				<br><br>
				
			</div>-->

			<div class="item_left">
			
				<!--First Image-->
				<?if(count($image_paths) > 0) {?>
				<div class="image_icon_big" id="img_0">
					<img src="<?=$image_icon_paths[0]?>" alt="<?$image_name[0]?>" style="width:100%">
					<div class="overlay" id="actions_0">
						1. <?=$image_authors[0]?><br />
						(<span id="image_points_0"><?=$image_points[0]?></span> points)
						<div class="item_image_actions">
							<? if(isset($image_rated[0]) && $image_rated[0] == 1) { ?>
								<div class="item_image_action_up"><img src="/assets/site_img/up_active.png" width="20px" title="You liked this image" style="opacity:0.5" id="up_<?=$image_id[0]?>"></div> &nbsp;
								<div class="item_image_action_down"><img src="/assets/site_img/down.png" width="20px" title="You liked this image" style="opacity:0.5" id="down_<?=$image_id[0]?>"></div> &nbsp;
							<? } else if(isset($image_rated[0]) && $image_rated[0] == -1) { ?>
								<div class="item_image_action_up"><img src="/assets/site_img/up.png" width="20px" title="You disliked this image" style="opacity:0.5" id="up_<?=$image_id[0]?>"></div> &nbsp;
								<div class="item_image_action_down"><img src="/assets/site_img/down_active.png" width="20px" title="You disliked this image" style="opacity:0.5" id="down_<?=$image_id[0]?>"></div> &nbsp;
							<? } else { ?>
								<div class="item_image_action_up"><img src="/assets/site_img/up.png" width="20px" title="This image represents the menu item well" style="cursor:pointer" id="up_<?=$image_id[0]?>"></div> &nbsp;
								<div class="item_image_action_down"><img src="/assets/site_img/down.png" width="20px" title="This image is unrelated to the menu item" style="cursor:pointer" id="down_<?=$image_id[0]?>"></div> &nbsp;
							<? } ?>
							
							<? if($admins[$_SESSION['user_name']] === 'admin') { ?>
							<a href="/admin.php?q=<?=$image_id[0]?>&v=removal"><img src="/assets/site_img/flag.png" width="20px" title="ADMIN: REMOVE IMAGE" style="cursor:hover"></a>
							<? } else { ?>
							 <a href="#report"><img src="/assets/site_img/flag.png" width="20px" title="Report this image" style="cursor:hover"></a>
							<? } ?>
						</div>
					</div>
				</div><br />
				<? } ?>
				
				<br><br>
				
				<!--Ratings-->
				<? if(count($rating_authors) > 0) {
					for($i = 0; $i < count($rating_authors); $i++) {
						echo "
						<div class='".($rating_values[$i] == 1 ? "good_" : "bad_")."rating' id='rating_$i'>
							<div class='content' id='rating_content_$i'>
							".$rating_content[$i]."
							</div>
							<div class='caption' id='rating_caption_$i'>
							Rating by ".$rating_authors[$i]->name."
							</div>
						</div>
						";
					}
				}
				?>
			</div>
			
			
			<div class="item_right">
			
				
			
				<!--At a Glance-->
				<div class="item_facts">
					<a href="rate.php?q=<?=$food_id?>&v=<?=$vendor_id?>">Submit a rating.</a> We want to hear from you.
					<br><br>
					<a href="upload_image.php?q=<?=$food_id?>&v=<?=$vendor_id?>">Upload an image.</a> Photographers wanted.
					<? if(in_array('no_price', $food_tags) || $admins[$_SESSION['user_name']] === 'admin') {?>
						<br><br>
						<a href="add_price.php?q=<?=$food_id?>&v=<?=$vendor_id?>">Update price.</a> Is this price accurate?
					<? } ?>
				
				</div>
				<br>
				
				<? //Nutrition Block
				if(isset($food_nutrition['calories'])) {
					?>
					<div class="nutrition_block">
						serving size <?=$food_nutrition['serving']?> (<?=$food_nutrition['serving_weight']?>)
						<div class="nutrition_bigbreak"></div>
						<strong>total calories</strong>&nbsp;&nbsp;<?=$food_nutrition['calories']?>
						<div class="nutrition_break"></div>
						<div style="text-align:right">
							<span>% daily value</span>
						</div>
						<div class="nutrition_minibreak"></div>
						<table style="width:100%"><tr>
						<td><strong>total fat</strong>&nbsp;&nbsp;<?=$food_nutrition['total_fat']?></td>
						<td style="text-align:right"><?=$food_nutrition['total_fat_dv']?></td>
						</tr></table>
						<div class="nutrition_minibreak"></div>
						<table style="width:100%"><tr>
						<td>saturated fat&nbsp;&nbsp;<?=$food_nutrition['saturated_fat']?></td>
						<td style="text-align:right"><?=$food_nutrition['saturated_fat_dv']?></td>
						</tr></table>
						<div class="nutrition_minibreak"></div>
						<table style="width:100%"><tr>
						<td>trans fat&nbsp;&nbsp;<?=$food_nutrition['trans_fat']?></td>
						<td style="text-align:right"><?=$food_nutrition['trans_fat_dv']?></td>
						</tr></table>
						<div class="nutrition_minibreak"></div>
						<table style="width:100%"><tr>
						<td><strong>cholesterol</strong>&nbsp;&nbsp;<?=$food_nutrition['cholesterol']?></td>
						<td style="text-align:right"><?=$food_nutrition['cholesterol_dv']?></td>
						</tr></table>
						<div class="nutrition_minibreak"></div>
						<table style="width:100%"><tr>
						<td><strong>sodium</strong>&nbsp;&nbsp;<?=$food_nutrition['sodium']?></td>
						<td style="text-align:right"><?=$food_nutrition['sodium_dv']?></td>
						</tr></table>
						<div class="nutrition_minibreak"></div>
						<table style="width:100%"><tr>
						<td><strong>total carbohydrates</strong>&nbsp;&nbsp;<?=$food_nutrition['total_carbs']?></td>
						<td style="text-align:right"><?=$food_nutrition['total_carbs_dv']?></td>
						</tr></table>
						<div class="nutrition_minibreak"></div>
						<table style="width:100%"><tr>
						<td>dietary fiber&nbsp;&nbsp;<?=$food_nutrition['dietary_fiber']?></td>
						<td style="text-align:right"><?=$food_nutrition['dietary_fiber_dv']?></td>
						</tr></table>
						<div class="nutrition_minibreak"></div>
						sugar&nbsp;&nbsp;<?=$food_nutrition['sugar']?></td>
						<div class="nutrition_minibreak"></div>
						<table style="width:100%"><tr>
						<td><strong>protein</strong>&nbsp;&nbsp;<?=$food_nutrition['protein']?></td>
						<td style="text-align:right"><?=$food_nutrition['protein_dv']?></td>
						</tr></table>
						<div class="nutrition_break"></div>
						<table style="width:100%"><tr>
						<?
						$n_counter = -1;
						
						if(isset($food_nutrition['vitamin_A_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">vitamin A <?=$food_nutrition['vitamin_A_dv']?></td>
							
						<? } if(isset($food_nutrition['vitamin_C_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">vitamin C <?=$food_nutrition['vitamin_C_dv']?></td>
							
						<? } if(isset($food_nutrition['calcium_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">calcium <?=$food_nutrition['calcium_dv']?></td>
						<? } if(isset($food_nutrition['iron_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">iron <?=$food_nutrition['iron_dv']?></td>
						<? } if(isset($food_nutrition['thiamin_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">thiamin <?=$food_nutrition['thiamin_dv']?></td>
						<? } if(isset($food_nutrition['riboflavin_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">riboflavin <?=$food_nutrition['riboflavin_dv']?></td>
						<? } if(isset($food_nutrition['niacin_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">niacin <?=$food_nutrition['niacin_dv']?></td>
						<? } if(isset($food_nutrition['vitamin_B12_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">vitamin B12 <?=$food_nutrition['vitamin_B12_dv']?></td>
						<? } if(isset($food_nutrition['folate_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">folate <?=$food_nutrition['folate_dv']?></td>
						<? } if(isset($food_nutrition['phosphorus_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">phosphorus <?=$food_nutrition['phosphorus_dv']?></td>
						<? } if(isset($food_nutrition['magnesium_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td style="width:50%">magnesium <?=$food_nutrition['magnesium_dv']?></td>
						<? } if(isset($food_nutrition['zinc_dv'])) {
							$n_counter++;
							if($n_counter>1) {
								echo '</tr></table><div class="nutrition_minibreak"></div><table style="width:100%"><tr>';
								$n_counter = 0;
							}?>
							<td width="50%">zinc <?=$food_nutrition['zinc_dv']?></td>
						<?}?>
						</tr></table>
						<br />
						*percent daily values are based on a 2000 calorie diet
					</div>
					<br /><br />
				<? } ?>
				
				<!--Tags-->
				<? 
				foreach ($food_tags as $tag) {
					if($tag === 'no_tag') {
						echo "
						<div class='item_tag_wrapper'>
							<li class='item_tag'>no_tag</li><br /><br />
							This menu item has no tags and is not part of any special groups. 
							Help us find a tag for this item.<br />
							<a href='#'>(Add a tag)</a>
						</div>
						";
					}
					if($tag === 'limited') {
						echo "
						<div class='item_tag_wrapper'>
							<li class='item_tag'>limited</li><br /><br />
							This item is limited by time or supply, so get yours quickly.
							This menu item will be gone soon. <br />
						</div>
						";
					}
				}?>
			
				<!--Images-->
				<? if(count($image_paths) > 1) {
					for($i = 1; $i < count($image_paths); $i++) {
					?>
						<?if($i==1) { //biggest image?>
						<div class="item_image_wrapper" id="img_<?=$i?>" style="float:right; margin:20px;">
							<div class="item_image">
								<img src="<?=$image_paths[$i]?>" alt="<?$image_name[$i]?>" style="max-width:100%; max-height:100%">
							</div>
							<div class="item_image_caption" id="actions_<?=$i?>">
								<?=($i+1)?>. <?=$image_authors[$i]?><br />
								(<span id="image_points_<?=$i?>"><?=$image_points[$i]?></span> points)
								<div class="item_image_actions">
									<? if(isset($image_rated[$i]) && $image_rated[$i] == 1) { ?>
										<div class="item_image_action_up"><img src="/assets/site_img/up_active.png" width="20px" title="You liked this image" style="opacity:0.5" id="up_<?=$image_id[$i]?>"></div> &nbsp;
										<div class="item_image_action_down"><img src="/assets/site_img/down.png" width="20px" title="You liked this image" style="opacity:0.5" id="down_<?=$image_id[$i]?>"></div> &nbsp;
									<? } else if(isset($image_rated[$i]) && $image_rated[$i] == -1) { ?>
										<div class="item_image_action_up"><img src="/assets/site_img/up.png" width="20px" title="You disliked this image" style="opacity:0.5" id="up_<?=$image_id[$i]?>"></div> &nbsp;
										<div class="item_image_action_down"><img src="/assets/site_img/down_active.png" width="20px" title="You disliked this image" style="opacity:0.5" id="down_<?=$image_id[$i]?>"></div> &nbsp;
									<? } else { ?>
										<div class="item_image_action_up"><img src="/assets/site_img/up.png" width="20px" title="This image represents the menu item well" style="cursor:pointer" id="up_<?=$image_id[$i]?>"></div> &nbsp;
										<div class="item_image_action_down"><img src="/assets/site_img/down.png" width="20px" title="This image is unrelated to the menu item" style="cursor:pointer" id="down_<?=$image_id[$i]?>"></div> &nbsp;
									<? } ?>
									<? if($admins[$_SESSION['user_name']] === 'admin') { ?>
										<a href="/admin.php?q=<?=$image_id[$i]?>&v=removal"><img src="/assets/site_img/flag.png" width="20px" title="ADMIN: REMOVE IMAGE" style="cursor:hover"></a>
									<? } else { ?>
										<a href="#report"><img src="/assets/site_img/flag.png" width="20px" title="Report this image" style="cursor:hover"></a>
									<? } ?>
								</div>
							</div>
						</div>
						<?} else if($i < 8) { //smaller images?>
						<?}?>
					<?
					}
				}
				?>
				<br><br>
			</div>
	</div>

		
	<script>
	$(document).ready(function(){
		
		$(".item_description").hover(function() {
			$(".item_description_overlay").fadeIn(100);
		},
		function() {
			$(".item_description_overlay").fadeOut(100);
		});

		function addVote(img_id, value) {
			$.ajax({
				type: "POST",
				data: {q:img_id,v:value},
				url: "add_vote.php",
				success: function(data) {	
				}
			});
		}

		<?php
		for($i = 0; $i < $image_qty; $i++) {
			echo "
			$('#img_$i').hover(function() {
					$('#actions_$i').fadeIn(100);
				},
				function() {
					$('#actions_$i').fadeOut(100);
			});
			";
			if(isset($_SESSION['user_name']) && !$image_rated[$i]) {
				echo "				
					$('#up_".$image_id[$i]."').on('click',function() {
						addVote(".$image_id[$i].", 1);
						$('#up_".$image_id[$i]."').attr('src', '/assets/site_img/up_active.png');
						var up = $('#image_points_".$i."').text();
						up++;
						$('#image_points_".$i."').text(up);
						$('#up_".$image_id[$i]."').off('click').css('cursor','default').prop('title', 'You have already voted.');
						$('#down_".$image_id[$i]."').off('click').css('cursor','default').prop('title', 'You have already voted.');
					});
					
					$('#down_".$image_id[$i]."').on('click',function() {
						addVote(".$image_id[$i].", -1);
						$('#down_".$image_id[$i]."').attr('src', '/assets/site_img/down_active.png');
						var down = $('#image_points_".$i."').text();
						down--;
						$('#image_points_".$i."').text(down);
						$('#up_".$image_id[$i]."').off('click').css('cursor','default').prop('title', 'You have already voted.');
						$('#down_".$image_id[$i]."').off('click').css('cursor','default').prop('title', 'You have already voted.');
					});
				";
			}
			else if(!isset($_SESSION['user_name'])) {
			echo "
				$('#up_".$image_id[$i]."').css('cursor','default').prop('title', 'You must log in to contribute.');
				$('#down_".$image_id[$i]."').css('cursor','default').prop('title', 'You must log in to contribute.');
				";
			}
		} ?>
		
		

	})
	</script>
	
	
</BODY>

</HTML>