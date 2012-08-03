jQuery(document).ready(function($) {
	var acs_action = 'rel_search'; //autocomplete search action
	
	$('#related-posts-select').change(function() {
		var select = $(this),
				container = $('#related-posts'),
				id = select.val(),
				title = this.options[this.options.selectedIndex].text;
		
		if ($('#related-post-' + id).length == 0) {
			container.prepend(related_box(id,title));
		}
	});
	
	$(document).on('click', 'a.related-post', function() {
		var div = $(this).parent();
		
		div.css('background-color', '#ff0000').fadeOut('normal', function() {
			div.remove();
		});
		return false;
	});
	
	$(document).on('click', 'input#rel_text_search', function(){
		if($('input#rel_text_search').is(':checked')){
			$('#related-posts-select').hide('slow');
			$('#rel_search_container').show('slow');
		} else {
			$('#related-posts-select').show('slow');
			$('#rel_search_container').hide('slow');
		}
	})
	
	$('#related-posts').sortable();
	
	$('#rel_search_id').autocomplete({  
	    source: function(req, response){  
	        $.getJSON(RelatedObj.url+'?callback=?&action='+acs_action, req, response);  
	    },  
	    select: function(event, ui) {
	    	$('#related-posts').prepend(related_box(ui.item.ID, ui.item.label));
	    	return true;
	    },  
	    minLength: 3
	});		
	
	var related_box = function(id, title) {
		return '<div class="related-post" id="related-post-' + id + '">'+
			'<input type="hidden" name="related-posts[]" value="' + id + '">'+
			'<span class="related-post-title">' + title + '</span><a href="#">Delete</a></div>';
	}
});