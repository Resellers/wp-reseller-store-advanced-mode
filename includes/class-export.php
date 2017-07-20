<?php

namespace Reseller_Store_Advanced;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Export {

	/**
	 * Hold error object.
	 *
	 * @since NEXT
	 *
	 * @var WP_Error
	 */
	private $error;

	/**
	 * Class constructor.
	 *
	 * @since NEXT
	 */
	public function __construct() {
		add_action( 'wp_ajax_rstore_export', [ __CLASS__, 'export_posts' ] );
	}


	function export_posts() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'rstore_export' ) ) {
			wp_send_json_error(
				esc_html__( 'Error: Invalid Session. Refresh the page and try again.', 'reseller-store-advanced' )
			);
			return;
		}

		global $wpdb;

		$posts = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` = %s ORDER BY `ID`;",
				SETTINGS::SLUG
			)
		);

		$products = array();

		foreach ( $posts as $post_id ) {
			$post = get_post( $post_id );
			$title = $post->post_title;
			$content = apply_filters( 'the_content', $post->post_content );

			$product = self::rstore_get_product_meta( $post_id, 'id' );

			$products[ $product ] = array(
				'title' => $title,
				'content' => $content,
			);
		}

		wp_send_json( $products );

	}


	/**
	 * Return a product meta value, or its global setting fallback.
	 *
	 * @since NEXT
	 *
	 * @param  int    $post_id
	 * @param  string $key
	 * @param  mixed  $default          (optional)
	 * @param  bool   $setting_fallback (optional)
	 *
	 * @return mixed
	 */
	static function rstore_get_product_meta( $post_id, $key, $default = false, $setting_fallback = false ) {

		$key = rstore_prefix( $key );

		$meta = get_post_meta( $post_id, $key, true );

		return ( $meta ) ? $meta : ( $setting_fallback ? get_option( $key, $default ) : $default );

	}

}
