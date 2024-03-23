<?php
/**
 * Register the inner block for the WooCommerce checkout block.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\WooCommerce\Block;

/**
 * Register editor block.
 */
class Register {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woocommerce_blocks_checkout_block_registration', array( $this, 'block_registration' ) );
	}

	/**
	 * Registers block type and registers with WC Blocks Integration Interface.
	 *
	 * @since 1.6.0
	 *
	 * @param object $integration_registry WC Blocks integration registry.
	 *
	 * @return void
	 */
	public function block_registration( $integration_registry ) {

		$integration_registry->register( new Block() );
	}
}
