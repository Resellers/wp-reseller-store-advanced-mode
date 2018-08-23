<?php
/**
 * Plugin Name: GoDaddy Reseller Store Advanced Settings
 * Description: Advanced debug settings for the reseller store plugin
 * Version: 1.5.1
 * Author: Reseller Team
 * Author URI: https://www.godaddy.com/reseller-program
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: reseller-store-advanced
 * Domain Path: /languages
 *
 * This plugin, like WordPress, is licensed under the GPL.
 * Use it to make something cool, have fun, and share what you've learned with others.
 *
 * Copyright © 2017 GoDaddy Operating Company, LLC. All Rights Reserved.
 */

namespace Reseller_Store_Advanced;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

require_once __DIR__ . '/includes/autoload.php';

final class Plugin {

	use Singleton, Data, Helpers;

	/**
	 * Plugin version.
	 *
	 * @since 0.3.3
	 *
	 * @var string
	 */
	const VERSION = '1.5.1';

	/**
	 * Plugin prefix.
	 *
	 * @since 0.3.3
	 *
	 * @var string
	 */
	const PREFIX = 'rstore_';

	/**
	 * Class contructor
	 */
	private function __construct() {

		$this->version    = self::VERSION;
		$this->basename   = plugin_basename( __FILE__ );
		$this->base_dir   = plugin_dir_path( __FILE__ );
		$this->assets_url = plugin_dir_url( __FILE__ ) . 'assets/';

		add_action(
			'plugins_loaded', function() {

				load_plugin_textdomain( 'reseller-store-advanced', false, dirname( $this->basename ) . '/languages' );

			}
		);

		register_activation_hook(
			__FILE__, function() {
				update_option( 'wpem_done', true );
			}
		);

		new Settings;
		new Export;

	}

}

Plugin::load();
