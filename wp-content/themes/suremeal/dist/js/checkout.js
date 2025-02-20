$(document).ready(function(){
	$(".open-menu").click(function(){
		$("body").addClass("overflow-hidden");
	});

	$(".close-menu").click(function(){
		$("body").removeClass("overflow-hidden");
	});
});