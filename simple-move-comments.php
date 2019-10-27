<?php
/**
 * Simple Move Comments
 *
 * @package   Simple_Move_Comments
 * @copyright Copyright(c) 2019, MediaRon LLC
 * @license http://opensource.org/licenses/GPL-2.0 GNU General Public License, version 2 (GPL-2.0)
 *
 * Plugin Name: Simple Move Comments
 * Plugin URI: https://mediaron.com/simple-move-comments
 * Description: Allows you to move comments to a different post or page.
 * Version: 2.0.1
 * Author: MediaRon LLC
 * Author URI: https://mediaron.com
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: simple-move-comments
 * Domain Path: languages
 */

define( 'SIMPLE_MOVE_COMMENTS_VERSION', '2.0.1' );
define( 'SIMPLE_MOVE_COMMENTS_PLUGIN_NAME', 'Simple Move Comments' );
define( 'SIMPLE_MOVE_COMMENTS_DIR', plugin_dir_path( __FILE__ ) );
define( 'SIMPLE_MOVE_COMMENTS_URL', plugins_url( '/', __FILE__ ) );
define( 'SIMPLE_MOVE_COMMENTS_SLUG', plugin_basename( __FILE__ ) );
define( 'SIMPLE_MOVE_COMMENTS_FILE', __FILE__ );

// Setup the plugin auto loader.
require_once 'php/autoloader.php';

/**
 * Admin notice if User Profile Picture isn't an adequate version.
 */
function simple_move_comments_version_error() {
	printf(
		'<div class="error"><p>%s</p></div>',
		esc_html__( 'Simple Move Comments requires a PHP version of 5.6 or above.', 'sample-wpajax-plugin' )
	);
}

// If the PHP version is too low, show warning and return.
if ( version_compare( phpversion(), '5.6', '<' ) ) {
	add_action( 'admin_notices', 'simple_move_comments_version_error' );
	return;
}

/**
 * Get the plugin object.
 *
 * @return \Sample_Move_Comments\Plugin
 */
function simple_move_comments() {
	static $instance;

	if ( null === $instance ) {
		$instance = new \Simple_Move_Comments\Plugin();
	}

	return $instance;
}

/**
 * Setup the plugin instance.
 */
simple_move_comments()
	->set_basename( plugin_basename( __FILE__ ) )
	->set_directory( plugin_dir_path( __FILE__ ) )
	->set_file( __FILE__ )
	->set_slug( 'simple-move-comments' )
	->set_url( plugin_dir_url( __FILE__ ) )
	->set_version( __FILE__ );

/**
 * Sometimes we need to do some things after the plugin is loaded, so call the Plugin_Interface::plugin_loaded().
 */
add_action( 'plugins_loaded', array( simple_move_comments(), 'plugin_loaded' ), 20 );
add_action( 'init', 'simple_move_comments_add_i18n' );

/**
 * Add i18n to Sample Plugin.
 */
function simple_move_comments_add_i18n() {
	load_plugin_textdomain( 'sample-wpajax-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
