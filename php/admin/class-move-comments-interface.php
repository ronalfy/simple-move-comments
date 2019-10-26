<?php
/**
 * Add Settings page to Plugin and to sub-menu
 *
 * @package   Simple_Move_Comments
 */

namespace Simple_Move_Comments\Admin;

/**
 * Class Admin
 */
class Move_Comments_Interface {

	/**
	 * Initialize the Admin component.
	 */
	public function init() {

	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'comment_row_actions', array( $this, 'register_comment_row_actions' ), 10, 2 );
	}

	/**
	 * Adds a action to the comments screen.
	 *
	 * @param array  $actions The default comment actions.
	 * @param object $comment The comment to modify.
	 *
	 * @return array Updated actions.
	 */
	public function register_comment_row_actions( $actions, $comment ) {
		$nonce    = wp_create_nonce( 'move-comment-' . $comment->ID );
		$move_url = add_query_arg(
			array(
				'c'        => $comment->ID,
				'action'   => 'move_comment',
				'_wpnonce' => $nonce,
			),
			admin_url( 'edit-comments.php' )
		);

		$actions['move'] = sprintf( '<a href="%s" class="simple-move-comments">', esc_url( $move_url ) ) . esc_html__( 'Move', 'simple-move-comments' ) . '</a>';
		return $actions;
	}
}
