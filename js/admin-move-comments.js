jQuery(function($) {
	jQuery( '.simple-move-comments' ).on( 'click', function(e) {
		e.preventDefault();
		var url = wpAjax.unserialize($(this).attr('href'));
		var nonce = url._wpnonce;
		var comment_id = url.c;
		console.log( url );
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
			  return fetch(ajaxurl + '?action=simple_move_comment_search&search=' + search + '&nonce=' + nonce + '&comment_id=' + comment_id )
				.then(response => {
				  if (!response.ok) {
					throw new Error(response.statusText)
				  }
				  return response.json()
				})
				.catch(error => {
				  Swal.showValidationMessage(
					`Request failed: ${error}`
				  )
				})
			},
			allowOutsideClick: () => !Swal.isLoading()
		  }).then((result) => {
			if (result.value) {
			  Swal.fire({
				title: `${result.value.login}'s avatar`,
				imageUrl: result.value.avatar_url
			  })
			}
		  });
	});
});