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
		add_action('admin_menu', [ $this, 'register' ] );
		add_action( 'wp_ajax_rstore_advanced_save', [ __CLASS__, 'save' ] );

		$api_tld_override = rstore_get_option('api_tld_override');
		if ( $api_tld_override ) {
			add_filter( 'rstore_api_tld', [ $this, 'api_tld_filter' ] );
		}

		$rstore_sync_ttl = rstore_get_option('rstore_sync_ttl');
		if ( $rstore_sync_ttl ) {
			add_filter( 'rstore_sync_ttl', [ $this, 'rstore_sync_ttl_filter' ] );
		}

		$api_market = rstore_get_option('api_market');
		if ( ! empty( $api_market ) ) {
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

		$suffix = '';//SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'rstore-advanced-settings', Plugin::assets_url( "js/advanced-settings{$suffix}.js" ), [ 'jquery' ], rstore()->version, true );

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
		return rstore_get_option('api_tld');
	}

	/**
	 * Register the api market filter
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function api_market_filter() {
		return rstore_get_option('api_market');
	}

	/**
	 * Register the rstore_sync_ttl_filter filter
	 *
	 * @action init
	 * @since  NEXT
	 */
	public function rstore_sync_ttl_filter() {
		return rstore_get_option('rstore_sync_ttl');
	}


	function edit_settings() {

		if ( ! rstore2_is_admin_uri( self::PAGE_SLUG, false ) ) {

			return;

		}

		$this->settings_output();

	}

	function reseller_settings() {
		$settings = array();
		$settings[] = array( 'name' => 'pl_id','label' => esc_html__( 'Private Label Id', 'reseller-store-advanced' ), 'type' => 'number' );
		$settings[] = array( 'name' => 'currency', 'label' => esc_html__( 'Currency', 'reseller-store-advanced' ), 'type' => 'currency' );
		$settings[] = array( 'name' => 'sync_ttl','label' => esc_html__( 'Api Sync TTL (seconds)', 'reseller-store-advanced' ), 'type' => 'number' );
		$settings[] = array( 'name' => 'last_sync','label' => esc_html__( 'Last Api Sync', 'reseller-store-advanced' ), 'type' => 'time' );
		$settings[] = array( 'name' => 'api_market', 'label' => esc_html__( 'Override Api Market', 'reseller-store-advanced' ), 'type' => 'text',
			'description' => esc_html__( 'Must be in the format xx-XX (i.e. en-US, fr-FR, etc.)', 'reseller-store-advanced' ) );
		$settings[] = array( 'name' => 'api_tld_override', 'label' => esc_html__( 'Override Api Url', 'reseller-store-advanced' ), 'type' => 'checkbox' );
		$settings[] = array( 'name' => 'api_tld', 'label' => esc_html__( 'Api Url', 'reseller-store-advanced' ), 'type' => 'text' );
		return $settings;
	}

	function reseller_register_settings() {
		$settings = $this->reseller_settings();
		foreach ( $settings as $setting ) {
			register_setting( 'reseller_settings',$setting['name'] );
		}
	}

	function settings_output() {
		$settings = $this->reseller_settings();

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
		settings_fields( 'reseller_settings' );

		foreach ( $settings as $setting ) {
			switch ( $setting['type'] ) {
				case 'text':
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><input type="text" id="' . $setting['name'] . '" name="' . $setting['name'] . '" value="' . rstore_get_option( $setting['name'] ) . '" class="regular-text">';
					if ( array_key_exists( 'description', $setting ) ) {
						echo '<p class="description" id="tagline-description">' . $setting['description'] . '</p></td>';
					}
					echo '</td>';
					echo '</tr>';
				break;
				case 'number':
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><input type="number" id="' . $setting['name'] . '" name="' . $setting['name'] . '" value="' . rstore_get_option( $setting['name'] ) . '" class="regular-text"></td>';
					echo '</tr>';
				break;
				case 'time':
					$sync_time = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),  rstore_get_option( $setting['name'] ), false );
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><label id="' . $setting['name'] . '" >' . $sync_time . '</label></td>';
					echo '</tr>';
				break;
				case 'checkbox':
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><input type="checkbox" id="' . $setting['name'] . '" name="' . $setting['name'] . '" value="1" ' . checked( rstore_get_option( $setting['name'], 0 ), 1, false ) . '  /></td>';
					echo '</tr>';
					break;

				case 'currency':
					$currencies = array('AED','ARS','AUD','BRL','CAD','CHF','CLP','CNY','COP','CZK','DKK','EGP','EUR','GBP','HKD','HUF','IDR','ILS','INR','JPY','KRW','MAD','MXN','MYR','NOK','NZD','PEN','PHP','PKR','PLN','RON','RUB','SAR','SEK','SGD','THB','TRY','TWD','UAH','USD','UYU','VND','ZAR');
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><select title="'. $setting['label'] .'" id="' . $setting['name'] . '" name="' . $setting['name'] . '" >';
					foreach ( $currencies as $currency ) {
						if ($currency === rstore_get_option( $setting['name'], 'USD' ) ) {
							echo "<option selected=\"selected\" value=\"$currency\">$currency</option>";
						}
						else {
							echo "<option value=\"$currency\">$currency</option>";
						}
					}
					echo  '</select></td>';
					echo '</tr>';
				break;
			}
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
		$pl_id = absint( filter_input( INPUT_POST, 'pl_id' ) );
		$currency = filter_input( INPUT_POST, 'currency' );
		$api_tld_override = filter_input( INPUT_POST, 'api_tld_override' );
		$api_tld = filter_input( INPUT_POST, 'api_tld' );
		$api_market = filter_input( INPUT_POST, 'api_market' );
		$sync_ttl = absint( filter_input( INPUT_POST, 'sync_ttl' ) );


		if ( 0 === $pl_id ) {

			wp_send_json_error(
				esc_html__( 'Error: Invalid Private Label ID.', 'reseller-store-advanced' )
			);
			return;

		}

		rstore_update_option( 'pl_id', $pl_id );
		rstore_update_option( 'currency', $currency );
		rstore_update_option( 'api_tld', $api_tld );
		rstore_update_option( 'api_tld_override', $api_tld_override );
		rstore_update_option( 'api_market', $api_market );

		if ( 0 < $sync_ttl ) {
			rstore_update_option( 'sync_ttl', $sync_ttl );
		}
		else {
			rstore_update_option( 'sync_ttl', '' );
		}

		//force a rsync update
		rstore_update_option( 'last_sync', 0 );

		wp_send_json_success();
	}


}
