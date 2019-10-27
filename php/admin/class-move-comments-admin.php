<?php
/**
 * Add a setting page for Simple Move Comments.
 *
 * @package   Simple_Move_Comments
 */

namespace Simple_Move_Comments\Admin;

/**
 * Class Admin
 */
class Move_Comments_Admin {

	/**
	 * Initialize the Admin component.
	 */
	public function init() {
		// Add settings link.
		$prefix = is_multisite() ? 'network_admin_' : '';
		add_action( $prefix . 'plugin_action_links_' . SIMPLE_MOVE_COMMENTS_SLUG, array( $this, 'plugin_settings_link' ) );

		// Init admin menu.
		if ( is_multisite() ) {
			add_action( 'network_admin_menu', array( $this, 'register_sub_menu' ) );
		} else {
			add_action( 'admin_menu', array( $this, 'register_sub_menu' ) );
		}
	}

	/**
	 * Register any hooks that this component needs.
	 */
	public function register_hooks() {
		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Initializes admin menus
	 *
	 * @since 2.0.0
	 * @access public
	 * @see init
	 */
	public function register_sub_menu() {
		if ( is_multisite() ) {
			$hook = add_submenu_page(
				'settings.php',
				__( 'Simple Move Comments', 'simple-move-comments' ),
				__( 'Simple Move Comments', 'simple-move-comments' ),
				'manage_network',
				'smc',
				array( $this, 'admin_page' )
			);
		} else {
			$hook = add_submenu_page(
				'options-general.php',
				__( 'Simple Move Comments', 'simple-move-comments' ),
				__( 'Simple Move Comments', 'simple-move-comments' ),
				'manage_options',
				'smc',
				array( $this, 'admin_page' )
			);
		}
	}

	/**
	 * Adds plugin settings page link to plugin links in WordPress Dashboard Plugins Page
	 *
	 * @since 2.0.0
	 * @access public
	 * @see __construct
	 * @param array $settings Uses $prefix . "plugin_action_links_$plugin_file" action.
	 * @return array Array of settings
	 */
	public function plugin_settings_link( $settings ) {
		$admin_anchor = sprintf(
			'<a href="%s">%s</a>',
			esc_url( $this->get_url() ),
			esc_html__( 'Settings', 'simple-move-comments' )
		);
		if ( ! is_array( $settings ) ) {
			return array( $admin_anchor );
		} else {
			return array_merge( array( $admin_anchor ), $settings );
		}
	}

	/**
	 * Return the URL to the admin panel page.
	 *
	 * Return the URL to the admin panel page.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string URL to the admin panel page.
	 */
	public function get_url() {
		if ( is_multisite() ) {
			$url = add_query_arg( array( 'page' => SIMPLE_MOVE_COMMENTS_SLUG ), network_admin_url( 'settings.php' ) );
		} else {
			$url = add_query_arg( array( 'page' => SIMPLE_MOVE_COMMENTS_SLUG ), admin_url( 'options-general.php' ) );
		}
		return $url;
	}
}
