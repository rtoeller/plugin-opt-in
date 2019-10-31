jQuery(document).ready(function(){

	jQuery('.laden').click(function(){
		var id = jQuery(this).before('input[name="overlay_content_id"]').val();
		
		//,var id = jQuery(this).parent().next('input[name="overlay_content_id"]').val();
		console.log('id '+id);
		jQuery('html, body').animate({
	         scrollTop: jQuery('#overlay'+id).offset().top - 250
	     }, 600);  
        
        jQuery.ajax({
			type: 'post',
			url: '/wp-content/plugins/plugin-opt-in/include/get_content.php',
			data: {
				contentbox_id: id
			},
			success: function(response) {
			    jQuery('#overlay'+id).before(response);
                jQuery('#overlay'+id).remove();
			},
			error: function (response) {
				console.log("Ajax call failed.");
				console.log(response);
            }
		});
	});
	backend();
});

function backend(){
    jQuery('.upload_image_button').click(function() {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = jQuery(this);
        jQuery('.remove_image_button').removeAttr('style');
        wp.media.editor.send.attachment = function(props, attachment) {
			jQuery('.image_for_overlay img').attr('src', attachment.url);
			jQuery('.image_for_overlay img').removeAttr('srcset');
			jQuery('.image_for_overlay').next('.image_size').show();
			jQuery('input[name="overlay_image"]').val(attachment.id);
			wp.media.editor.send.attachment = send_attachment_bkp;
		}

        wp.media.editor.open(button);
        return false;
    });


	jQuery('.btn').click(function() {
		jQuery(this).closest('.label_buttons').children().removeClass('active');
		jQuery(this).addClass('active');
		var labelVal = jQuery(this).text();
		labelVal = labelVal.replace('Yes', '1');
		labelVal = labelVal.replace('No', '0');
		jQuery(this).closest('.label_buttons').next('input[type="hidden"]').val(labelVal);
        var id = jQuery(this).closest('.backend_optin_iframe__container').children('input[name="contentbox_id"]').val();
		datenschutz(labelVal, id);
	});

    jQuery('.remove_image_button').click(function(e) {
    	e.preventDefault();
        jQuery('.image_for_overlay img').removeAttr('srcset');
        jQuery('.image_for_overlay img').removeAttr('src');
        jQuery('input[name="overlay_image"]').val(0);
		jQuery(this).closest('.image_for_overlay').next('.image_size').hide();
        jQuery(this).css('display', 'none');
	});

    jQuery('.backend_optin_iframe__container .plus').click(function() {
		jQuery(this).closest('.backend_optin_iframe__container').children('form').children('.backend_optin_iframe__inner').addClass('active');
        jQuery(this).closest('.backend_optin_iframe__container').children('.backend_optin_iframe__inner').addClass('active');
		jQuery(this).prev('.minus').addClass('active');
        jQuery(this).removeClass('active');
	});
    jQuery('.backend_optin_iframe__container .minus').click(function() {
        jQuery(this).closest('.backend_optin_iframe__container').children('form').children('.backend_optin_iframe__inner').removeClass('active');
        jQuery(this).closest('.backend_optin_iframe__container').children('.backend_optin_iframe__inner').removeClass('active');
        jQuery(this).next('.plus').addClass('active');
        jQuery(this).removeClass('active');
	});

	jQuery('.new_row').click(function () {
        jQuery(this).hide();
		var count = jQuery('.backend_optin_iframe__container').length;
		if( count == 0 ) {
			var after = 'h1';
		}
		else {
			var after = '.backend_optin_iframe__container';
		}
		jQuery(after).last().after(
			'	<div class="backend_optin_iframe__container">\n' +
			'		<form method="post">\n' +
	'                   <div class="header">\n' +
	'                        <h2>New Content Box</h2>\n' +
	'                        <div class="icon minus active">-</div>\n' +
	'                        <div class="icon plus">+</div>\n' +
	'                    </div>\n' +
				'		<div class="backend_optin_iframe__inner active iframe0>">\n' +
				'            <input type="hidden" name="contentbox_id" value="0"/>\n' +
				' 				<table>\n'+
				' 					<tr>\n' +
				' 						<th>\n' +
				' 							<label>Name:</label>\n' +
				' 						</th>\n' +
				' 						<td>\n' +
				' 							<input type="text" name="contentbox_name" value=""/>\n' +
				' 						</td>\n' +
				'					</tr>\n' +
				' 					<tr>\n' +
				'						<th>\n' +
				' 							<label>Contentbox Code:</label>\n' +
				' 						</th>\n' +
				' 						<td>\n' +
				' 							<textarea name="contentbox_code"></textarea>\n' +
				'						</td>\n' +
				'					</tr>\n' +
				' 				</table>\n' +
				'				<input type="submit" name="save" value="Save"/>\n' +
				' 				<input type="submit" name="delete" value="Delete"/>\n' +
				'			</div>\n ' +
        '			</div>'
		);
    });

	// Function???
	/*jQuery('.cancel').click(function () {
        jQuery('.backend_optin_iframe__container').last().remove();
        jQuery('.newRow').show();
    });*/

	jQuery('input[name="contentbox_name"]').change(function () {
		var val = jQuery(this).val();
		var id = jQuery(this).closest('.backend_optin_iframe__inner').children('input[name="contentbox_id"]').val();
		jQuery(this).closest('form').children('.header').children('h2').html(val+' (not saved)');
    });
}

function datenschutz(val, id) {
	if(val == 1) {
        jQuery('.iframe'+id+' .select_pages').addClass('active');
    }
    else {
        jQuery('.iframe'+id+' .select_pages').removeClass('active');
    }
}