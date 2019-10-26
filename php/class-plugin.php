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

		// Add Ajax handler
		$this->move_admin_ajax = new Ajax\Ajax();
		$this->move_admin_ajax->register_hooks();
	}
}
