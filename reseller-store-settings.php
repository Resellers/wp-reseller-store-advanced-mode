<?php
/**
 * Plugin Name: Reseller Store Settings
 * Description: Advanced settings for the Reseller Store plugin.
 * Version: 1.8.2
 * Author: Reseller Team
 * Author URI: https://www.godaddy.com/reseller-program
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: reseller-store-settings
 * Domain Path: /languages
 *
 * This plugin, like WordPress, is licensed under the GPL.
 * Use it to make something cool, have fun, and share what you've learned with others.
 *
 * Copyright © 2019 GoDaddy Operating Company, LLC. All Rights Reserved.
 */

namespace Reseller_Store_Settings;

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
	const VERSION = '1.8.2';

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
			'plugins_loaded',
			function() {

				load_plugin_textdomain( 'reseller-store-settings', false, dirname( $this->basename ) . '/languages' );

			}
		);

		register_activation_hook(
			__FILE__,
			function() {
				update_option( 'wpem_done', true );
			}
		);

		new Settings();
		new Export();

	}

}

Plugin::load();
