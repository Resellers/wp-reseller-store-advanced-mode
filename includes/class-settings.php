<?php

namespace Reseller_Store_Advanced;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Settings {

	/**
	 * Post type slug.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	const SLUG = 'reseller_product';

	/**
	 * Settings Page
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	const PAGE_SLUG = 'edit.php?post_type=reseller_product';

	/**
	 * Array of Currencies.
	 *
	 * @since NEXT
	 */
	static $currencies = [ 'USD','AED','ARS','AUD','BRL','CAD','CHF','CLP','CNY','COP','CZK','DKK','EGP','EUR','GBP','HKD','HUF','IDR','ILS','INR','JPY','KRW','MAD','MXN','MYR','NOK','NZD','PEN','PHP','PKR','PLN','RON','RUB','SAR','SEK','SGD','THB','TRY','TWD','UAH','UYU','VND','ZAR' ];

	/**
	 * Array of markests.
	 *
	 * @since NEXT
	 */
	static $markets = [ 'default', 'da-DK', 'de-DE', 'el-GR', 'en-US', 'es-MX', 'fi-FI', 'fr-FR', 'hi-IN', 'id-ID', 'it-IT', 'ja-JP', 'ko-KR', 'mr-IN', 'nb-NO', 'nl-NL', 'pl-PL', 'pt-BR', 'pt-PT', 'ru-RU', 'sv-SE', 'ta-IN', 'th-TH', 'tr-TR', 'uk-UA', 'vi-VN', 'zh-CN', 'zh-TW' ];


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

		add_action( 'admin_enqueue_scripts',  [ $this, 'admin_enqueue_scripts' ] );
		add_action( 'admin_init', [ $this, 'reseller_register_settings' ] );
		add_action( 'admin_menu', [ $this, 'register' ] );
		add_action( 'wp_ajax_rstore_advanced_save', [ __CLASS__, 'save' ] );

		$api_tld = rstore_get_option( 'api_tld' );
		if ( ! empty( $api_tld ) ) {
			add_filter( 'rstore_api_tld', [ $this, 'api_tld_filter' ] );
		}

		$rstore_sync_ttl = rstore_get_option( 'rstore_sync_ttl' );
		if ( $rstore_sync_ttl ) {
			add_filter( 'rstore_sync_ttl', [ $this, 'rstore_sync_ttl_filter' ] );
		}

		$api_market = rstore_get_option( 'api_market' );
		if ( ! empty( $api_market ) &&  $api_market !== 'default' ) {
			add_filter( 'rstore_api_market_id', [ $this, 'api_market_filter' ] );
		}

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 * @since  NEXT
	 */
	public function admin_enqueue_scripts() {

		if ( ! rstore2_is_admin_uri( self::PAGE_SLUG ) ) {

			return;

		}

		$suffix = SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'rstore-advanced-settings', Plugin::assets_url( "js/advanced-settings{$suffix}.js" ), [ 'jquery' ], rstore()->version, true );

		wp_enqueue_script( 'rstore-magnific-popup', Plugin::assets_url( "js/magnific-popup{$suffix}.js" ), [ 'jquery' ], rstore()->version, true );

		wp_enqueue_script( 'rstore-clipboard', Plugin::assets_url( "js/clipboard.min.js" ), [ 'jquery' ], rstore()->version, true );

		wp_enqueue_style( 'rstore-magnific-popup-css', Plugin::assets_url( "css/magnific-popup{$suffix}.css"), rstore()->version, true  );

		wp_enqueue_style( 'rstore-advanced-settings-css', Plugin::assets_url( "css/advanced-settings{$suffix}.css"), rstore()->version, true  );

	}

	/**
	 * Register the settings page.
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function register() {

		add_submenu_page(
			self::PAGE_SLUG,
			esc_html__( 'Reseller Store Advanced Options', 'reseller-store-advanced' ),
			esc_html__( 'Advanced Options', 'reseller-store-advanced' ),
			'manage_options',
			self::SLUG . '_settings',
		[ $this, 'edit_settings' ] );

	}

	/**
	 * Register the api tld filter
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function api_tld_filter() {

		return rstore_get_option( 'api_tld' );
	}

	/**
	 * Register the api market filter
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function api_market_filter() {

		return rstore_get_option( 'api_market' );
	}

	/**
	 * Register the rstore_sync_ttl_filter filter
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function rstore_sync_ttl_filter() {

		return rstore_get_option( 'rstore_sync_ttl' );
	}


	function edit_settings() {

		if ( ! rstore2_is_admin_uri( self::PAGE_SLUG, false ) ) {

			return;

		}

		$this->settings_output();

	}

	static function reseller_settings() {

		$settings = array();
		$settings[] = array(
			'name' => 'pl_id',
			'label' => esc_html__( 'Private Label Id', 'reseller-store-advanced' ),
			'type' => 'number',
		 	'description' => esc_html__( 'The private label id that you have set for your storefront.', 'reseller-store-advanced' ),
		);
		$settings[] = array(
			'name' => 'currency',
			'label' => esc_html__( 'Currency', 'reseller-store-advanced' ),
			'type' => 'select',
			'list' => self::$currencies,
			'description' => esc_html__( 'Set the currency to display on your storefront.', 'reseller-store-advanced' ),
		);
		$settings[] = array(
			'name' => 'api_market',
			'label' => esc_html__( 'Override Api Market', 'reseller-store-advanced' ),
			'type' => 'select',
			'list' => self::$markets,
			'description' => esc_html__( 'Override your default language selected in the wordpress setup.', 'reseller-store-advanced' ),
		);
		$settings[] = array(
			'name' => 'sync_ttl',
			'label' => esc_html__( 'Api Sync TTL (seconds)', 'reseller-store-advanced' ),
			'type' => 'number',
		  'description' => esc_html__( 'Reseller store will check the api for changes periodically. The default is 15 minutes (900 seconds).', 'reseller-store-advanced' ),
		);
		$settings[] = array( 'name' => 'last_sync','label' => esc_html__( 'Last Api Sync', 'reseller-store-advanced' ), 'type' => 'time' );
		$settings[] = array( 'name' => 'next_sync','label' => esc_html__( 'Next Api Sync', 'reseller-store-advanced' ), 'type' => 'time' );
		$settings[] = array(
			'name' => 'api_tld',
			'label' => esc_html__( 'Api Url', 'reseller-store-advanced' ),
			'type' => 'text',
			'description' => esc_html__( 'Set url for internal testing.', 'reseller-store-advanced' ),
		);
		return $settings;
	}

	function reseller_register_settings() {
		$settings = self::reseller_settings();
		foreach ( $settings as $setting ) {
			register_setting( 'reseller_settings',$setting['name'] );
		}
	}

	function settings_output() {

		$settings = self::reseller_settings();

		?>
		<style type="text/css">
				.rstore-spinner {
			visibility: hidden;
			max-width: 20px;
			height: auto;
			margin-bottom: -4px;
		}
		</style>


		<div class="wrap">
			<h1> <?php esc_html_e( 'Reseller Advanced Settings', 'reseller-store-advanced' ) ?> </h1>
			<form id="rstore-settings-form" >
			<table class="form-table">
			<tbody>

		<?php
		wp_nonce_field( 'rstore_advanced_save', 'nonce' );

		settings_fields( 'reseller_settings' );

		foreach ( $settings as $setting ) {
			switch ( $setting['type'] ) {
				case 'text':
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><input type="text" id="' . $setting['name'] . '" name="' . $setting['name'] . '" value="' . rstore_get_option( $setting['name'] ) . '" class="regular-text">';
				break;
				case 'number':
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><input type="number" id="' . $setting['name'] . '" name="' . $setting['name'] . '" value="' . rstore_get_option( $setting['name'] ) . '" class="regular-text">';
				break;
				case 'time':
					$sync_time = get_date_from_gmt( date( 'Y-m-d H:i:s', rstore_get_option( $setting['name'] ) ), get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><label id="' . $setting['name'] . '" >' . $sync_time . '</label>';
				break;
				case 'checkbox':
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><input type="checkbox" id="' . $setting['name'] . '" name="' . $setting['name'] . '" value="1" ' . checked( rstore_get_option( $setting['name'], 0 ), 1, false ) . '  />';
					break;
				case 'select':
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><select title="' . $setting['label'] . '" id="' . $setting['name'] . '" name="' . $setting['name'] . '" >';
					foreach ( $setting['list'] as $item ) {
						if ( $item === rstore_get_option( $setting['name'] ) ) {
							echo "<option selected=\"selected\" value=\"$item\">$item</option>";
						} else {
							echo "<option value=\"$item\">$item</option>";
						}
					}
					echo  '</select>';
				break;
			}
			if ( array_key_exists( 'description', $setting ) ) {
				echo '<p class="description" id="tagline-description">' . $setting['description'] . '</p></td>';
			}
			echo '</td></tr>';
		}
		?>
			</tbody>
			</table>
			<p class="submit">
				<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'reseller-store-advanced' ); ?></button>
				<img src="<?php echo esc_url( includes_url( 'images/spinner-2x.gif' ) ); ?>" class="rstore-spinner">
			</p>
			</form>
		</div>

		<?php

		$this->export_button();

	}

	function export_button() {
		?>
			<div class="wrap">
				<form id='rstore-settings-export'>
				<?php wp_nonce_field( 'rstore_export', 'nonce' ); ?>
					<input type="hidden" name="action" value="rstore_export">
					<button type="submit" class="button link" ><?php esc_html_e( 'Export Posts to JSON', 'reseller-store-advanced' ); ?></button>
				</form>

				<div id="json-generator" class="json-generator mfp-hide mfp-with-anim">
					<div class="json-content">
						<div id="header">
						</div>
					</div>
					<div class="container">
					 <button id='clipboard' class="button button-primary" data-clipboard-action="copy" data-clipboard-target="#json-text"><?php esc_html_e( 'Copy to clipboard', 'reseller-store-advanced' ); ?></button>
						<div id="json-content">
							<p><textarea id="json-text"> </textarea></p>
						</div>
					</div>
				</div>
		</div>

		<?php

	}

	/**
	 * Save Advanced Settings
	 *
	 * @action wp_ajax_rstore_advanced_save
	 * @global wpdb $wpdb
	 * @since  NEXT
	 *
	 * @param int $pl_id (optional)
	 */
	public static function save() {

		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'rstore_advanced_save' ) ) {
			wp_send_json_error(
				esc_html__( 'Error: Invalid Session. Refresh the page and try again.', 'reseller-store-advanced' )
			);
			return;
		}

		$pl_id = absint( filter_input( INPUT_POST, 'pl_id' ) );

		if ( 0 === $pl_id ) {
			wp_send_json_error(
				esc_html__( 'Error: Invalid Private Label ID.', 'reseller-store-advanced' )
			);
			return;

		}

		$settings = self::reseller_settings();
		foreach ( $settings as $setting ) {

			if ( $setting['type'] === 'time' ) {
				 continue;
			}

			$val = filter_input( INPUT_POST, $setting['name'] );
			if ( $setting['type'] === 'number' ) {
				  $val = absint( $val );
			}

			if ( empty( $val ) ) {
				rstore_delete_option( $setting['name'] );
			} else {
				rstore_update_option( $setting['name'], $val );
			}
		}

		// force a rsync update
		rstore_delete_option( 'next_sync' );

		wp_send_json_success();
	}


}
