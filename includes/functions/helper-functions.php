<?php
/**
 * GoDaddy Reseller Store helper functions.
 *
 * Contains the Reseller Store helper functions used throughout the plugin.
 *
 * @package  Reseller_Store/Plugin
 * @author   GoDaddy
 * @since    1.0.0
 */

namespace Reseller_Store_Advanced;

/**
 * Check if we are on a specific admin screen.
 *
 * @since 0.2.0
 *
 * @param  string $request_uri Request URL to check.
 * @param  bool   $strict      (optional) strict check.
 *
 * @return bool  Returns `true` if the current admin URL contains the specified URI, otherwise `false`.
 */
function rstore_is_admin_uri( $request_uri, $strict = true ) {

	$strpos = strpos( basename( filter_input( INPUT_SERVER, 'REQUEST_URI' ) ), $request_uri );
	$result = ( $strict ) ? ( 0 === $strpos ) : ( false !== $strpos );

	return ( is_admin() && $result );

}

/**
 * Add the plugin prefix to a string.
 *
 * @since 0.2.0
 *
 * @param  string $string      Reseller store prefix.
 * @param  bool   $use_dashes (optional) Whether the prefix should use dashes.
 *
 * @return string  Returns a string prepended with the plugin prefix.
 */
function rstore_prefix( $string, $use_dashes = false ) {

	$prefix = ( $use_dashes ) ? str_replace( '_', '-', Plugin::PREFIX ) : Plugin::PREFIX;

	return ( 0 === strpos( $string, $prefix ) ) ? $string : $prefix . $string;

}

/**
 * Return a plugin option.
 *
 * @since 0.2.0
 *
 * @param  string $key      Option key to retrieve.
 * @param  mixed  $default (optional) Default option value.
 *
 * @return mixed  Returns the option value if the key exists, otherwise the `$default` parameter value.
 */
function rstore_get_option( $key, $default = false ) {

	return get_option( rstore_prefix( $key ), $default );

}

/**
 * Update a plugin option.
 *
 * @since 0.2.0
 *
 * @param  string $key   Option key to update.
 * @param  mixed  $value New option value.
 *
 * @return bool  Returns `true` on success, `false` on failure.
 */
function rstore_update_option( $key, $value ) {

	return update_option( rstore_prefix( $key ), $value );

}

/**
 * Delete a plugin option.
 *
 * @since 0.2.0
 *
 * @param  string $key Option key to delete.
 *
 * @return bool Returns `true` on success, `false` on failure.
 */
function rstore_delete_option( $key ) {

	return delete_option( rstore_prefix( $key ) );

}
