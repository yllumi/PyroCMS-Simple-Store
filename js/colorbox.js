$(document).ready(function() {
    setTimeout(function(){
		$(".product-images img").each(function() {
			var src_load = $(this).attr("src").replace('_thumb', '');
			var anchor = $("<a/>").attr({"href": src_load}).colorbox({width:"830px", height:"800px"});
			$(this).wrap(anchor);
		});
	}, 1250);
});
