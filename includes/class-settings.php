<?php

namespace Reseller_Store_Advanced;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}

final class Settings {

	/**
	 * Top-level domain for URLs.
	 *
	 * @since NEXT
	 *
	 * @var string
	 */
	private $tld = 'secureserver.net';

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

		add_filter( 'rstore_api_tld', [ $this, 'api_tld_filter' ] );

	}

	/**
	 * Enqueue admin scripts.
	 *
	 * @action admin_enqueue_scripts
	 * @since  NEXT
	 */
	public function admin_enqueue_scripts() {

		if ( ! rstore_is_admin_uri( self::PAGE_SLUG ) ) {

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
		$api_tld = rstore_get_option('api_tld');

		if ( '' !== $api_tld) {
			return $api_tld;
		} else {
			return $this->tld;
		}
	}


	 function edit_settings() {

		if ( ! rstore_is_admin_uri( self::PAGE_SLUG, false ) ) {

			return;

		}

		$this->settings_output();

	}

	function reseller_settings() {
		$settings = array();
		$settings[] = array( 'name' => 'pl_id','label' => esc_html__( 'Private Label Id', 'reseller-store-advanced' ), 'type' => 'text' );
		$settings[] = array( 'name' => 'currency', 'label' => esc_html__( 'Currency', 'reseller-store-advanced' ), 'type' => 'currency' );
		$settings[] = array( 'name' => 'api_tld', 'label' => esc_html__( 'API Url', 'reseller-store-advanced' ), 'type' => 'text' );
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

		<?php
		echo '<div class="wrap">';
			esc_html__( '<h1>Reseller Advanced Mode Settings</h1>', 'reseller-store-advanced' );

			echo '<form id="rstore-settings-form" >';
			echo '<table class="form-table">';
			echo '<tbody>';


			settings_fields( 'reseller_settings' );


		foreach ( $settings as $setting ) {
			switch ( $setting['type'] ) {
				case 'text':
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><input type="text" style="width:100%;" id="' . $setting['name'] . '" name="' . $setting['name'] . '" value="' . rstore_get_option( $setting['name'] ) . '" /></td>';
					echo '</tr>';
				break;
				case 'checkbox':
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';

					echo '<td><input type="checkbox" id="' . $setting['name'] . '" name="' . $setting['name'] . '" value="1" ' . checked( 1, rstore_get_option( $setting['name'], 1 ), false ) . '  /></td>';
					echo '</tr>';
					break;

				case 'currency':
					$currencies = array('AED','ARS','AUD','BRL','CAD','CHF','CLP','CNY','COP','CZK','DKK','EGP','EUR','GBP','HKD','HUF','IDR','ILS','INR','JPY','KRW','MAD','MXN','MYR','NOK','NZD','PEN','PHP','PKR','PLN','RON','RUB','SAR','SEK','SGD','THB','TRY','TWD','UAH','USD','UYU','VND','ZAR');
					echo '<tr>';
					echo '<th><label for="' . $setting['name'] . '">' . $setting['label'] . '</label></th>';
					echo '<td><select title="'. $setting['label'] .'" style="width:100%;" id="' . $setting['name'] . '" name="' . $setting['name'] . '" >';
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
			</p>
				<img src="<?php echo esc_url( includes_url( 'images/spinner-2x.gif' ) ); ?>" class="rstore-spinner">
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
		$api_tld = filter_input( INPUT_POST, 'currency' );

		if ( 0 === $pl_id ) {

			wp_send_json_error(
				esc_html__( 'Error: Invalid Private Label ID.', 'reseller-store-advanced' )
			);
			return;

		}

		rstore_update_option( 'pl_id', $pl_id );

		if ( '' !== $currency ) {
			rstore_update_option( 'currency', $currency );
		}

		rstore_update_option( 'currency', $api_tld );

		wp_send_json_success();
	}


}
