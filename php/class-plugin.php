<?php
/**
 * Primary plugin file.
 *
 * @package   Simple_Move_Comments
 */

namespace Simple_Move_Comments;

/**
 * Class Plugin
 */
class Plugin extends Plugin_Abstract {
	/**
	 * Execute this once plugins are loaded.
	 */
	public function plugin_loaded() {
		// Set the interface for moving comments.
		$this->move_comments_interface = new Admin\Move_Comments_Interface();
		$this->move_comments_interface->register_hooks();

		// Add Ajax handler.
		$this->move_admin_ajax = new Ajax\Ajax();
		$this->move_admin_ajax->register_hooks();

		// Add Front end interface.
		$this->frontend = new Frontend\Move_Comments_Frontend();
		$this->frontend->register_hooks();

		// Add admin interface.
		$this->admin_settings = new Admin\Move_Comments_Admin();
		$this->admin_settings->register_hooks();
	}
}
