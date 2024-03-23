<?php
/**
 * The onboarding class for enhancing the new user experience.
 *
 * @since 1.4.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Enhancements;

use Woo_Additional_Terms\Helper;

/**
 * The onboarding class.
 */
class OnBoarding {

	/**
	 * Rated (already) transient name.
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	const OPTION_NAME = 'woo_additional_terms_onboarding';

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woo_additional_terms_admin_notices', array( $this, 'admin_notice' ) );
	}

	/**
	 * Display the onboarding notice.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function admin_notice() {

		global $pagenow;

		// Bail out if not on the plugins page.
		if ( 'plugins.php' !== $pagenow ) {
			return;
		}

		// Bail early, in case user can manage shop settings.
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// Bail out if the user has already dismissed the onboarding notice.
		if ( get_site_option( self::OPTION_NAME ) ) {
			return;
		}

		// Enqueue the assets.
		wp_enqueue_script( 'woo-additional-terms-dismiss' );

		woo_additional_terms()->service( 'template_manager' )->echo_template(
			'notices/onboarding.php',
			array(
				'help_uri'     => Helper\Links::docs_uri(),
				'settings_uri' => Helper\Settings::page_uri(),
			)
		);
	}
}
