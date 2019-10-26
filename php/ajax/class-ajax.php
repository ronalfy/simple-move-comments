<?php
/**
 * Ajax action for moving comments.
 *
 * @package   Simple_Move_Comments
 */

namespace Simple_Move_Comments\Ajax;

/**
 * Class Admin
 */
class Ajax {

	/**
	 * Initialize the Admin component.
	 */
	public function init() {

	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'wp_ajax_simple_move_comment_search', array( $this, 'comment_search' ) );
	}

	/**
	 * Performs a search for posts/pages
	 */
	public function comment_search() {
		if ( wp_verify_nonce( filter_input( INPUT_GET, 'nonce' ), 'move-comment-' . filter_input( INPUT_GET, 'comment_id' ) ) ) {
			$search = sanitize_text_field( filter_input( INPUT_GET, 'search' ) );
			$query  = new \WP_Query(
				array(
					'post_status'     => 'publish',
					'posts_per_page' => 10,
					's'              => $search,
				)
			);
			if ( $query->have_posts() ) {
				$posts = $query->get_posts();
				die( wp_json_encode( $posts ) );
			}
		} else {
			die( '' );
		}
	}
}
