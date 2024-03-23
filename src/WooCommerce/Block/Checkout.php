<?php
/**
 * WooCommerce checkout customizations.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\WooCommerce\Block;

use Exception;
use WP_Error;
use WC_Order;
use WP_REST_Request;
use Woo_Additional_Terms\Admin;
use Automattic\WooCommerce\StoreApi\Schemas\V1\CheckoutSchema;

/**
 * Checkout class.
 */
class Checkout {

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woocommerce_blocks_loaded', array( $this, 'extend_store_api' ) );
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( $this, 'process_acceptance' ), 10, 2 );
		add_action( 'woo_additional_terms_checkout_save_acceptance', array( $this, 'save_acceptance' ), 10, 2 );
	}

	/**
	 * Add schema Store API to support posted data.
	 * Registers the checkout endpoint extension to inform our frontend component about the result of
	 * the validity of the additional terms checkbox and react accordingly.
	 *
	 * @since 1.5.0
	 *
	 * @throws Exception When the schema callback is not callable.
	 *
	 * @return void
	 */
	public function extend_store_api() {

		woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => CheckoutSchema::IDENTIFIER,
				'namespace'       => Block::NAME,
				'data_callback'   => null,
				'schema_type'     => ARRAY_A,
				'schema_callback' => fn() => array(
					'data' => array(
						'type'        => 'string',
						'context'     => array( 'view', 'edit' ),
						'arg_options' => array(
							'validate_callback' => function( $value ) {

								if ( ! is_string( $value ) ) {
									/* translators: %s: Render the type of the variable. */
									return new WP_Error( 'api-error', sprintf( esc_html__( 'Value of field %s was posted with incorrect data type.', 'woo-additional-terms' ), gettype( $value ) ) );
								}

								return true;
							},
						),
					),
				),
			)
		);
	}

	/**
	 * Fires after an order saved into the database.
	 * We will update the post meta.
	 *
	 * @since 1.5.0
	 *
	 * @param WC_Order        $order   Order ID or order object.
	 * @param WP_REST_Request $request The API request currently being processed.
	 *
	 * @return void
	 */
	public function process_acceptance( $order, $request ) {

		if ( ! isset( $request['extensions'], $request['extensions'][ Block::NAME ] ) ) {
			return;
		}

		$has_accepted = empty( $request['extensions'][ Block::NAME ]['data'] ) ? null : wc_string_to_bool( $request['extensions'][ Block::NAME ]['data'] );

		/**
		 * Fires after additional terms submissions is about to be saved.
		 *
		 * @since 1.6.1
		 *
		 * @param null|bool $has_accepted Whether the additional terms has been accepted.
		 * @param WC_Order  $order        Order object.
		 */
		do_action( 'woo_additional_terms_checkout_save_acceptance', $has_accepted, $order );
	}

	/**
	 * Stores additional terms submissions after a new order being processed.
	 *
	 * @since 1.6.1
	 *
	 * @param null|bool $has_accepted Whether the additional terms has been accepted.
	 * @param WC_Order  $order        Order object.
	 *
	 * @return void
	 */
	public function save_acceptance( $has_accepted, $order ) {

		// Leave if the order is not an instance of WC_Order.
		if ( ! $order instanceof WC_Order || is_null( $has_accepted ) ) {
			return;
		}

		// Add order note based on acceptance status.
		$note_message = $has_accepted
			? __( 'Customer accepted the additional terms.', 'woo-additional-terms' )
			: __( 'Customer did not accept the additional terms.', 'woo-additional-terms' );

		$order->add_order_note( $note_message );

		// Save the additional terms checkbox value as order meta.
		update_post_meta( $order->get_id(), Admin\Order::META_KEY, wc_bool_to_string( $has_accepted ) );
	}
}
