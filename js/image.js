$(document).ready(function() {
    
	$('.progress').hide();
	
	var bar = $('.bar');
	var percent = $('.percent');
	var status = $('#status');
	   
	$('#ajax-form-upload').ajaxForm({
		beforeSend: function() {
			status.empty();
			var percentVal = '0%';
			bar.width(percentVal)
			percent.html(percentVal);
			$('.progress').show();
		},
		uploadProgress: function(event, position, total, percentComplete) {
			var percentVal = percentComplete + '%';
			bar.width(percentVal)
			percent.html(percentVal);
		},
		complete: function(xhr) {
			
			$('.progress').hide();
			
			var obj = jQuery.parseJSON(xhr.responseText);
	
			if(obj.status == 1){
			
				  if(obj.image_default == 1){
					  var btnDefault = '<a class="btn gray  default-button" title="'+obj.current_default+'" ref="'+obj.image_dt+'">'+obj.current_default+'</a>';
				  }else{
					  var btnDefault = '<a class="btn green set-default-button" href="#" title="'+obj.set_default+'" ref="'+obj.image_dt+'">'+obj.set_default+'</a>';
				  }
				  
				var result_html = '<div class="imgBox imgDt"><div id="thumbnail" class="imgContent"><img src="'+obj.thumbnail+'" alt="'+obj.products_image+'" /></div><a class="btn red delete-image-button" href="#" title="'+obj.delete_title+'" ref="'+obj.image_dt+'">'+obj.delete_image+'</a>'+btnDefault+'</div>';
		
				$('#img_no_img').before(result_html); 
				$('input[type=file]').val('');
			}
		}
	}); 
	
	$('a.delete-image-button').live("click", function(e){
		e.preventDefault();
		var isDel = window.confirm("Delete the images ?");
		if(isDel){
			var objCurrent = $(this);
			var refresh_url = SITE_URL + 'admin/products/ajax_delete_image/' + $(this).attr('ref');
			$.ajax({
			  url: refresh_url,
			  success: function(data) {
				  var obj = jQuery.parseJSON(data);
				  if(obj.status == 1){
					  objCurrent.parent().remove();
					  return true;
				  }
				}
			});
		}else{
			return false;
		}
	});  
	
	$('a.set-default-button').live("click", function(e){
		e.preventDefault();
		
		var objCurrent = $(this);
		var refresh_url = SITE_URL + 'admin/products/ajax_set_default/' + $(this).attr('ref');
		$.ajax({
		  url: refresh_url,
		  success: function(data) {
			  var obj = jQuery.parseJSON(data);
			  if(obj.status == 1){
				  $('div.imgBox.imgDt').each(function(i, ele){
					  $(ele).remove();
				  });
				  loadImages();
			  }
			}
		});
		
	});  

	var loadImages = function(){
		var refresh_url = SITE_URL + 'admin/products/ajax_images/' + $('#prm_post_dt').val();
        $.ajax({
          url: refresh_url,
          success: function(data) {
			  var obj = jQuery.parseJSON(data);
			  if(obj.status == 1){	
				  for(var k in obj.result){
					  
					  if(obj.result[k].image_default == 1){
						  var btnDefault = '<a class="btn gray default-button" title="'+obj.result[k].current_default+'" ref="'+obj.result[k].image_dt+'">'+obj.result[k].current_default+'</a>';
					  }else{
						  var btnDefault = '<a class="btn green set-default-button" href="#" title="'+obj.result[k].set_default+'" ref="'+obj.result[k].image_dt+'">'+obj.result[k].set_default+'</a>';
					  }
					  var result_html = '<div class="imgBox imgDt"><div id="thumbnail" class="imgContent"><img src="'+obj.result[k].image_thumbnail+'" alt="'+obj.result[k].products_image+'" /></div><a class="btn red delete-image-button" href="#" title="'+obj.result[k].delete_title+'" ref="'+obj.result[k].image_dt+'">'+obj.result[k].delete_image+'</a>'+btnDefault+'</div>';
					  $('#img_no_img').before(result_html); 
					  
				  }
			  }
          }
        });
	};
	
	loadImages();
});
