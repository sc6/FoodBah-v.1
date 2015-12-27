
$(document).ready(function(){
	
	$(".menu_item").hover(function() {
		$(this).css("background-color", "#BBB");
	});
	
	$("#1").click(function() {
		$(".menu_item_selected").removeClass("menu_item");
		$(this).addClass("menu_item_selected");
	});
	
	$("#2").click(function() {
		$(".menu_item").css("background-color", "#DDD");
		$(this).css("background-color", "#BBB");
		$(".menu_right").fadeOut(100);
		$(".menu_right").fadeIn(100);
	});
	
	$(".add_menu_prompt").click(function() {
		$(".add_menu_form").css("display", "block");
	});
	
})