<?php
/**
 * Add move comments to edit comments screen.
 *
 * @package   Simple_Move_Comments
 */

namespace Simple_Move_Comments\Frontend;

/**
 * Class Admin
 */
class Move_Comments_Frontend {

	/**
	 * Initialize the Admin component.
	 */
	public function init() {

	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_scripts' ) );
		add_action( 'comment_text', array( $this, 'add_frontend_button' ), 10, 2 );
	}

	/**
	 * Add a move comment button to the comment text
	 *
	 * @param string $comment_text The comment text to modify.
	 * @param object $comment      The comment object.
	 */
	public function add_frontend_button( $comment_text, $comment ) {
		if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
			return $comment_text;
		}
		$nonce    = wp_create_nonce( 'move-comment-' . $comment->comment_ID );
		$move_url = add_query_arg(
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'c'        => $comment->comment_ID,
				'action'   => 'move_comment',
				'_wpnonce' => $nonce,
			),
			admin_url( 'comment.php' )
		);

		$comment_html = sprintf(
			'<div><a href="%s" onclick="simple_move_comments(event,this)">%s</a></div>',
			esc_url( $move_url ),
			esc_html__( 'Move Comment', 'simple-move-comments' )
		);
		$comment_text = $comment_text . $comment_html;
		return $comment_text;
	}

	/**
	 * Enqueue scripts on the front end.
	 */
	public function enqueue_frontend_scripts() {
		if ( ! is_singular() ) {
			return;
		}
		wp_enqueue_script(
			'simple-move-comments',
			SIMPLE_MOVE_COMMENTS_URL . '/js/frontend-move-comments.js',
			array( 'jquery', 'wp-ajax-response' ),
			SIMPLE_MOVE_COMMENTS_VERSION,
			true
		);
		wp_enqueue_script(
			'swal',
			SIMPLE_MOVE_COMMENTS_URL . '/js/sweetalert.js',
			array( 'jquery' ),
			SIMPLE_MOVE_COMMENTS_VERSION,
			true
		);
	}
}
