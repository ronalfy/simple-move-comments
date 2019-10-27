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
	 * Output admin menu
	 *
	 * @since 2.0.0
	 * @access public
	 * @see register_sub_menu
	 */
	public function admin_page() {
		$license_message = '';
		if ( isset( $_POST['submit'] ) && isset( $_POST['options'] ) ) {
			check_admin_referer( 'save_smc_options' );
			$options = $_POST['options']; // phpcs:ignore
			$this->update_options( $options );
			printf( '<div class="updated"><p><strong>%s</strong></p></div>', esc_html__( 'Your options have been saved.', 'simple-move-comments' ) );

			// Check for valid license.
			$store_url  = 'https://mediaron.com';
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $options['license'],
				'item_name'  => rawurlencode( 'Simple Move Comments' ),
				'url'        => home_url(),
			);
			// Call the custom API.
			$response = wp_remote_post(
				$store_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// make sure the response came back okay.
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$license_message = $response->get_error_message();
				} else {
					$license_message = __( 'An error occurred, please try again.', 'simple-move-comments' );
				}
			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {
					delete_site_option( 'smc_license_status' );
					switch ( $license_data->error ) {

						case 'expired':
							$license_message = sprintf(
								__( 'Your license key expired on %s.', 'simple-move-comments' ), // phpcs:ignore
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'disabled':
						case 'revoked':
							$license_message = __( 'Your license key has been disabled.', 'simple-move-comments' );
							break;

						case 'missing':
							$license_message = __( 'Invalid license.', 'simple-move-comments' );
							break;
						case 'invalid':
						case 'site_inactive':
							$license_message = __( 'Your license is not active for this URL.', 'simple-move-comments' );
							break;

						case 'item_name_mismatch':
							$license_message = sprintf( __( 'This appears to be an invalid license key for %s.', 'simple-comment-editing-options' ), 'simple-move-comments' ); // phpcs:ignore
							break;

						case 'no_activations_left':
							$license_message = __( 'Your license key has reached its activation limit.', 'simple-move-comments' );
							break;
						default:
							$license_message = __( 'An error occurred, please try again.', 'simple-move-comments' );
							break;
					}
				} else {
					$license = sanitize_text_field( $options['license'] );
					update_site_option( 'smc_license', $license );
				}
				if ( empty( $license_message ) ) {
					update_site_option( 'smc_license_status', $license_data->license );
				}
			}
		}
		$options = get_site_option( 'smc_options', false );
		if ( ! $options ) {
			$options['license'] = '';
		}
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Simple Move Comments Settings', 'simple-move-comments' ); ?></h2>
			<form action="" method="POST">
				<?php wp_nonce_field( 'save_smc_options' ); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="smc-license"><?php esc_html_e( 'Enter Your License', 'simple-move-comments' ); ?></label></th>
							<td>
								<input id="smc-license" class="regular-text" type="text" value="<?php echo esc_attr( $options['license'] ); ?>" name="options[license]" /><br />
								<?php
								$license_status = get_site_option( 'smc_license_status', false );
								if ( false === $license_status ) {
									printf( '<p>%s</p>', esc_html__( 'Please enter your licence key.', 'simple-move-comments' ) );
								} else {
									printf( '<p>%s</p>', esc_html__( 'Your license is valid and you will now receive update notifications.', 'simple-move-comments' ) );
								}
								?>
								<?php
								if ( ! empty( $license_message ) ) {
									printf( '<div class="updated error"><p><strong>%s</p></strong></div>', esc_html( $license_message ) );
								}
								?>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button( __( 'Save Options', 'simple-move-comments' ) ); ?>
		<?php
	}

	/**
	 * Update options via sanitization
	 *
	 * @since 2.0.0
	 * @access public
	 * @param array $options array of options to save.
	 * @return void
	 */
	private function update_options( $options ) {
		foreach ( $options as $key => &$option ) {
			switch ( $key ) {
				default:
					$option = sanitize_text_field( $options[ $key ] );
					break;
			}
		}
		update_site_option( 'smc_options', $options );
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
			$url = add_query_arg( array( 'page' => 'smc' ), network_admin_url( 'settings.php' ) );
		} else {
			$url = add_query_arg( array( 'page' => 'smc' ), admin_url( 'options-general.php' ) );
		}
		return $url;
	}
}
