<?php
/**
 * Plugin documentation content.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Enhancements;

use Woo_Additional_Terms\Helper;

/**
 * Class Docs.
 */
class Docs {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woo_additional_terms_settings_sidebar', array( $this, 'sidebar' ), 20 );
	}

	/**
	 * Display the docs sidebar.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function sidebar() {

		woo_additional_terms()->service( 'template_manager' )->echo_template(
			'sidebar/docs.php',
			array(
				'uri' => Helper\Links::docs_uri(),
			)
		);
	}
}
