jQuery(document).ready(function($) {
	
	$('#related-posts-select').change(function() {
		var select = $(this),
				container = $('#related-posts'),
				id = select.val(),
				title = this.options[this.options.selectedIndex].text;
		
		if ($('#related-post-' + id).length == 0) {
			container.prepend('<div class="related-post" id="related-post-' + id + '"><input type="hidden" name="related-posts[]" value="' + id + '"><span class="related-post-title">' + title + '</span><a href="#">Delete</a></div>');
		}
	});
	
	$('.related-post a').live('click', function() {
		var div = $(this).parent();
		
		div.css('background-color', '#ff0000').fadeOut('normal', function() {
			div.remove();
		});
		return false;
	});
	
	$('#related-posts').sortable();		
	
});