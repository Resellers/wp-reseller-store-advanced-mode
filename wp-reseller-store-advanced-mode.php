<?php
/**
 * Plugin Name: GoDaddy Reseller Store Advanced Settings
 * Description: A boilerplate WordPress plugin by GoDaddy.
 * Version: 0.1.0
 * Author: GoDaddy
 * Author URI: https://reseller.godaddy.com/
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: reseller-store
 * Domain Path: /languages
 *
 * This plugin, like WordPress, is licensed under the GPL.
 * Use it to make something cool, have fun, and share what you've learned with others.
 *
 * Copyright © 2017 GoDaddy Operating Company, LLC. All Rights Reserved.
 */

namespace Reseller_Store_Advanced;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

require_once __DIR__ . '/includes/autoload.php';

final class Plugin {

	use Singleton, Data, Helpers;

	/**
	 * Plugin version.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	const VERSION = '0.1.0';

	/**
	 * Plugin prefix.
	 *
	 * @since NEXT
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

		/**
		 * Register custom WP-CLI command
		 */
		if ( defined( 'WP_CLI' ) && WP_CLI ) {

			\WP_CLI::add_command( 'foo-plugin', '\Foo\CLI' );

		}

		/**
		 * Load languages
		 */
		add_action( 'plugins_loaded', function() {

			load_plugin_textdomain( 'foo-plugin', false, dirname( __FILE__ ) . '/languages' );

		} );

		new Settings;
		new Export;

	}

}

rstore_advanced();
