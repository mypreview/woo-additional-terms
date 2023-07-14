<?php
/**
 * Plugin upsell content.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.4.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Enhancements;

use Woo_Additional_Terms\Helper;

/**
 * Class Upsell.
 */
class Upsell {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woo_additional_terms_settings_sidebar', array( $this, 'sidebar' ) );
	}

	/**
	 * Display the upsell sidebar.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function sidebar() {

		woo_additional_terms()->service( 'template_manager' )->echo_template(
			'sidebar/upsell.php',
			array(
				'uri' => Helper\Links::pro_uri(),
			)
		);
	}
}
