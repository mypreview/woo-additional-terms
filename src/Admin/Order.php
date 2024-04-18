<?php
/**
 * The WooCommerce order extensions.
 *
 * @since 1.6.1
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Admin;

use WC_Order;

/**
 * Class Order.
 */
class Order {

	/**
	 * The meta key for additional terms.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const META_KEY = '_woo_additional_terms';

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'show_terms_acceptance' ) );
	}

	/**
	 * Show the terms acceptance on the order page.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order $order The order object.
	 *
	 * @return void
	 */
	public function show_terms_acceptance( $order ) {

		woo_additional_terms()->service( 'template_manager' )->echo_template(
			'order/terms-acceptance.php',
			array(
				'meta' => get_post_meta( $order->get_id(), self::META_KEY, true ),
			)
		);
	}
}
