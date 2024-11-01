

jQuery("div#twire-pagination a").live('click',
	function() { 
		jQuery('#ajax-loader').toggle();

		var fpage = jQuery(this).attr('href');
		fpage = fpage.split('=');

		jQuery.post( ajaxurl, {
			action: 'get_twire_posts',
			'cookie': encodeURIComponent(document.cookie),
			'_wpnonce': jQuery("input#_wpnonce").val(),
			'twpage': fpage[1],
			'bp_twire_item_id': jQuery("input#bp_twire_item_id").val()
		},
		function(response)
		{	
			jQuery('#ajax-loader').toggle();
			
			response = response.substr(0, response.length-1);

			jQuery("form#twire-post-list-form").fadeOut(200, 
				function() {
					jQuery("form#twire-post-list-form").html(response);
					jQuery("form#twire-post-list-form").fadeIn(200);
				}
			);

			return false;
		});
		
		return false;
	}
);

jQuery("textarea#twire-post-textarea").live('keypress', 
	function() {
		if (jQuery('textarea#twire-post-textarea').length)
		{
			jQuery('textarea#twire-post-textarea').limit('140','#twireCharsLeft');
		}

		return true;
});

jQuery(document).ready(function () {
		if (jQuery('textarea#twire-post-textarea').length)
		{
			jQuery('textarea#twire-post-textarea').limit('140','#twireCharsLeft');
		}

		return true;
});
