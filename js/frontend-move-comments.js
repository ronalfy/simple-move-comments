function simple_move_comments(e, linkObject) {
	e.preventDefault();
	var url = wpAjax.unserialize(jQuery(linkObject).attr('href'));
	console.log(url);
	var nonce = url._wpnonce;
	var comment_id = url.c;
	var ajax_url = url.ajax_url;
	console.log(ajax_url);
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
			jQuery.post(ajax_url, {action: 'simple_move_comment_search', search: search, nonce: nonce, comment_id: comment_id}, function( response ) {
			var inputs = {};
			jQuery.each( response, function( post, post_data ) {
				inputs[post_data.ID] = post_data.post_title;
			});
			Swal.fire({
				title: 'Select a Post',
				input: 'select',
				confirmButtonText: 'Move Comment',
				inputOptions: inputs,
				inputPlaceholder: 'Select a Post',
				showCancelButton: true,
				preConfirm: (search) => {
					jQuery.post(ajax_url, {action: 'simple_move_comment', post_id: jQuery('.swal2-select :selected').val(), nonce: nonce, comment_id: comment_id}, function( response ) {
						Swal.fire({
							type: 'success',
							title: 'Success.',
							text: 'The Comment Has Been Moved!',
						})
					}, 'json');
				}
			  });
		  }, 'json');
		}
	});
}