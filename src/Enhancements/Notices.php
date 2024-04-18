<?php
/**
 * The plugin admin notices class.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Enhancements;

/**
 * The plugin notices class.
 */
class Notices {

	/**
	 * The dismiss nonce name.
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	const DISMISS_NONCE_NAME = 'woo-additional-terms-dismiss';

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'admin_notices', array( $this, 'print' ) );
	}

	/**
	 * Display the admin notices.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function print() {

		/**
		 * Fires after the welcome admin notice.
		 *
		 * @since 1.0.0
		 */
		do_action( 'woo_additional_terms_admin_notices' );
	}
}
