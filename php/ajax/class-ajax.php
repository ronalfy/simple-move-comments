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
		add_action( 'wp_ajax_simple_move_comment', array( $this, 'move_comment' ) );
	}

	/**
	 * Performs a search for posts/pages
	 */
	public function comment_search() {
		if ( wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'move-comment-' . filter_input( INPUT_POST, 'comment_id' ) ) && current_user_can( 'edit_comment', absint( filter_input( INPUT_POST, 'comment_id' ) ) ) ) {
			$search = sanitize_text_field( filter_input( INPUT_POST, 'search' ) );
			$query  = new \WP_Query(
				array(
					'post_status'    => 'publish',
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

	/**
	 * Moves a comment based on comment ID and post ID
	 */
	public function move_comment() {
		if ( wp_verify_nonce( filter_input( INPUT_POST, 'nonce' ), 'move-comment-' . filter_input( INPUT_POST, 'comment_id' ) ) && current_user_can( 'edit_comment', absint( filter_input( INPUT_POST, 'comment_id' ) ) ) ) {
			$comment_post_id  = absint( filter_input( INPUT_POST, 'post_id' ) );
			$comment_id       = absint( filter_input( INPUT_POST, 'comment_id' ) );
			$child_comments   = $this->get_direct_subcomments( $comment_id );
			$comments_to_move = implode( ',', array_map( 'absint', $child_comments ) );

			if ( 0 === $comment_post_id ) {
				exit();
			}
			// Move comments to selected post.
			global $wpdb;
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->comments SET comment_post_ID = %s WHERE comment_ID IN ( $comments_to_move )", $comment_post_id ) ); // phpcs:ignore
			exit();
		} else {
			die( '' );
		}
	}

	/**
	 * Retrieve child comments.
	 *
	 * @param int $comment_id The comment ID to retrieve children for.
	 *
	 * @return array Array of comment IDs.
	 */
	private function get_direct_subcomments( $comment_id ) {
		$comments_args = array( 'parent' => $comment_id );
		$comments      = get_comments( $comments_args );
		$comments_id   = array( $comment_id );

		foreach ( $comments as $comment ) {
			$comments_id[] = $comment->comment_ID;
		}

		return $comments_id;
	}
}
