jQuery(function($) {
	jQuery( '.simple-move-comments' ).on( 'click', function(e) {
		e.preventDefault();
		var url = wpAjax.unserialize($(this).attr('href'));
		var nonce = url._wpnonce;
		var comment_id = url.c;
		Swal.fire({
			title: 'Search for a post',
			input: 'text',
			inputAttributes: {
			  autocapitalize: 'off'
			},
			showCancelButton: true,
			confirmButtonText: 'Search',
			showLoaderOnConfirm: true,
			preConfirm: (search) => {
			  $.post(ajaxurl, {action: 'simple_move_comment_search', search: search, nonce: nonce, comment_id: comment_id}, function( response ) {
				var inputs = {};
				$.each( response, function( post, post_data ) {
					inputs[post_data.ID] = post_data.post_title;
				});
				Swal.fire({
					title: 'Select a Post',
					input: 'select',
					confirmButtonText: 'Move Comment',
					inputOptions: inputs,
					inputPlaceholder: 'Select a Post',
					showCancelButton: true,
				  });
			  }, 'json');
			}
		});
	} );
});