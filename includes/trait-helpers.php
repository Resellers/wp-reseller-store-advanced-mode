<?php
/**
 * Reseller Store Settings Helpers.
 *
 * Reseller store product helpers trait.
 *
 * @trait    Reseller_Store_Settings/Helpers
 * @package  Reseller_Store_Settings
 * @category trait
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store_Settings;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

trait Helpers {

	/**
	 * Return the plugin base directory path.
	 *
	 * @since 0.2.0
	 *
	 * @param string $path (optional) Additional path.
	 *
	 * @return string
	 */
	public static function base_dir( $path = '' ) {

		return Plugin::load()->base_dir . $path;

	}

	/**
	 * Return the plugin assets URL.
	 *
	 * @since 0.2.0
	 *
	 * @param  string $path (optional) Additional path.
	 *
	 * @return string
	 */
	public static function assets_url( $path = '' ) {

		return Plugin::load()->assets_url . $path;

	}

}
