<?php
/**
 * Migration for the version 1.6.0.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Migration;

/**
 * Migration class.
 */
class Migration160 extends Migration {

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function __construct() {

		// Parent constructor.
		parent::__construct( '1.6.0' );
	}

	/**
	 * Do the migration.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function migrate_callback() {

		// Bailout, if not eligible.
		if ( ! $this->is_eligible() ) {
			return;
		}

		woo_additional_terms()->service( 'options' )->update(
			array(
				'status'   => '1',
				'required' => 'yes',
				'page'     => wc_clean( get_option( '_woo_additional_terms_page_id', '' ) ),
				'notice'   => wc_clean( get_option( '_woo_additional_terms_notice', '' ) ),
				'error'    => wc_clean( get_option( '_woo_additional_terms_error', '' ) ),
			)
		);

		// Mark the migration as complete.
		$this->complete();
	}
}
