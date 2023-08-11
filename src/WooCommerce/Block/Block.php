<?php
/**
 * WooCommerce checkout editor block.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.5.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\WooCommerce\Block;

use WP_Error;
use WC_Order;
use Exception;
use WP_REST_Request;
use Automattic\WooCommerce\Blocks;
use Automattic\WooCommerce\StoreApi;
use Woo_Additional_Terms\Admin;

/**
 * Checkout block class.
 */
class Block implements Blocks\Integrations\IntegrationInterface {

	/**
	 * The name of the integration.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_name() {

		return '_woo_additional_terms';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function initialize() {

		$this->extend_store_api();

		add_filter( '__experimental_woocommerce_blocks_add_data_attributes_to_block', array( $this, 'add_attributes_to_frontend_blocks' ) );
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( $this, 'process_acceptance' ), 10, 2 );
		add_action( 'woo_additional_terms_checkout_save_acceptance', array( $this, 'save_acceptance' ), 10, 2 );
	}

	/**
	 * This allows dynamic (JS) blocks to access attributes in the frontend.
	 *
	 * @since 1.5.0
	 *
	 * @param array $allowed_blocks List of allowed blocks.
	 *
	 * @return array
	 */
	public function add_attributes_to_frontend_blocks( $allowed_blocks ) {

		$allowed_blocks[] = 'mypreview/woo-additional-terms';

		return $allowed_blocks;
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

		if ( ! isset( $request['extensions'], $request['extensions']['_woo_additional_terms'] ) ) {
			return;
		}

		$has_accepted = empty( $request['extensions']['_woo_additional_terms']['data'] ) ? null : wc_string_to_bool( $request['extensions']['_woo_additional_terms']['data'] );

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
		$order->update_meta_data( Admin\Order::META_KEY, wc_bool_to_string( $has_accepted ) );
	}

	/**
	 * Returns an array containing the handles of any scripts registered by our extension.
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_script_handles() {

		return array( 'woo-additional-terms', 'woo-additional-terms-checkout' );
	}

	/**
	 * Returns an array containing the handles of any editor scripts registered by our extension.
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_editor_script_handles() {

		return array( 'woo-additional-terms-block' );
	}

	/**
	 * Returns an associative array containing any data we want to be available to the scripts.
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_script_data() {

		$status = woo_additional_terms()->service( 'options' )->get( 'status', '' );

		// Bail early, in case settings status is not enabled.
		if ( ! wc_string_to_bool( $status ) ) {
			return array();
		}

		// Enqueue the frontend styles.
		wp_enqueue_style( 'woo-additional-terms' );

		return array(
			'is_required'    => wc_string_to_bool( woo_additional_terms()->service( 'options' )->get( 'required', 'no' ) ),
			'display_action' => woo_additional_terms()->service( 'options' )->get( 'action', 'embed' ),
			'page_content'   => woo_additional_terms()->service( 'terms' )->get( 'content' ),
			'checkbox_label' => woo_additional_terms()->service( 'terms' )->get( 'label' ),
			'error_message'  => woo_additional_terms()->service( 'options' )->get( 'error', __( 'Please accept the additional terms to continue.', 'woo-additional-terms' ) ),
		);
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

		$extend = StoreApi\StoreApi::container()->get( StoreApi\Schemas\ExtendSchema::class );

		$extend->register_endpoint_data(
			array(
				'endpoint'        => Blocks\StoreApi\Schemas\CheckoutSchema::IDENTIFIER,
				'namespace'       => $this->get_name(),
				'schema_callback' => fn() => array(
					'data' => array(
						'type'        => 'string',
						'context'     => array(),
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
}
