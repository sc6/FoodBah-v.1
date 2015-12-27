<?php
include("../functions/connect.php");

$query = "SELECT * FROM dayztbns_foodbah.Menus WHERE id=".$_POST['q'].";";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_free_result($result);

$food_name = $row['food'];
$item_rating = $row['item_rating'];
$vendor_id = $row['vendor_id'];
$food_id = $_POST['q'];
$food_description = $row['description'];
$food_tags = array();

$tok = strtok($row["tags"], ", ");
while ($tok !== false) {
	$food_tags[] = $tok;
	$tok = strtok(", ");
}

$tok = strtok($item_rating, ",");
$item_rating = $tok;
if($item_rating == "0" || $item_rating == "") $item_rating = "This item is new.";
else $item_rating = "$item_rating% of raters enjoyed this item.";
?>

<h4><?=$food_name?></h4>
<span class="item_rating_sum"><?=$item_rating?></span>
<br><br>
<?php
	if(count($food_tags) > 0) {
		foreach($food_tags as $tag) {
			echo '<li class="item_tag">'.$tag.'</li> ';
		}
		echo '<br />';
	}
?>
<br />
<img src="/assets/<?=$vendor_id?>/<?=$food_id?>.png" class="food_img"/>
<br /><br />
<div class="food_description">
<?=$food_description?>
</div>
<br />
<a href="/item/?q=<?=$_POST['q']?>">&gt;&gt; Visit full page</a>

<?php
exit();
?>