<?php

namespace Reseller_Store_Advanced;

/**
 * Check if we are on a specific admin screen.
 *
 * @since 0.3.3
 *
 * @param  string $request_uri
 * @param  bool   $strict      (optional)
 *
 * @return bool  Returns `true` if the current admin URL contains the specified URI, otherwise `false`.
 */
function rstore2_is_admin_uri( $request_uri, $strict = true ) {

	$strpos = strpos( basename( filter_input( INPUT_SERVER, 'REQUEST_URI' ) ), $request_uri );
	$result = ( $strict ) ? ( 0 === $strpos ) : ( false !== $strpos );

	return ( is_admin() && $result );

}

/**
 * Safe redirect to any admin page.
 *
 * @since 0.3.3
 *
 * @param string                   $endpoint (optional)
 * @param array                    $args (optional)
 * @param int    status (optional)
 */
function rstore_admin_redirect( $endpoint = '', array $args = [], $status = 302 ) {

	// Allow full admin URL to be passed as $endpoint
	$endpoint = preg_replace( '/^.*\/wp-admin(\/|$)/', '', $endpoint );

	wp_safe_redirect(
		esc_url_raw(
			add_query_arg( $args, admin_url( $endpoint ) )
		),
		$status
	);

	exit;

}
