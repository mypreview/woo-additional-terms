<?php
/**
 * The plugin rate class.
 *
 * @since 1.4.1
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Enhancements;

use WP_Footer_Rate;
use Woo_Additional_Terms\Helper;

/**
 * Class Rate.
 */
class Rate {

	/**
	 * Rated (already) option name.
	 *
	 * @since 1.5.2
	 *
	 * @var string
	 */
	const OPTION_NAME = 'woo_additional_terms_rated';

	/**
	 * Rate transient name.
	 *
	 * @since 1.4.1
	 *
	 * @var string
	 */
	const TRANSIENT_NAME = 'woo_additional_terms_rate';


	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woo_additional_terms_admin_notices', array( $this, 'admin_notice' ) );
		add_action( 'woocommerce_settings_start', array( $this, 'wp_footer' ) );
	}

	/**
	 * Display the rate the plugin notice.
	 *
	 * @since 1.4.1
	 *
	 * @return void
	 */
	public function admin_notice() {

		// Bail early if the rate notice has been dismissed.
		if (
			get_option( self::OPTION_NAME )
			|| get_transient( self::TRANSIENT_NAME )
		) {
			return;
		}

		$usage_timestamp = woo_additional_terms()->service( 'options' )->get_usage_timestamp();

		// If the usage timestamp is empty, add it and bail.
		if ( empty( $usage_timestamp ) ) {
			// Add the activation timestamp.
			woo_additional_terms()->service( 'options' )->add_usage_timestamp();

			return;
		}

		// Bail early if the plugin recently installed.
		if ( time() < ( $usage_timestamp + WEEK_IN_SECONDS ) ) {
			return;
		}

		// Enqueue the assets.
		wp_enqueue_style( 'woo-additional-terms-rate' );
		wp_enqueue_script( 'woo-additional-terms-dismiss' );

		// Display the notice.
		woo_additional_terms()->service( 'template_manager' )->echo_template(
			'notices/rate.php',
			array(
				'usage_timestamp' => human_time_diff( $usage_timestamp ),
			)
		);
	}

	/**
	 * Ask for a review in the footer of the settings page.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function wp_footer() {

		new WP_Footer_Rate\Rate(
			woo_additional_terms()->service( 'file' )->plugin_basename(),
			woo_additional_terms()->get_slug(),
			_x( 'Additional Terms for WooCommerce', 'plugin name', 'woo-additional-terms' ),
			Helper\Settings::is_page()
		);
	}
}
