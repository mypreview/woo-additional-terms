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

use WC_Order;
use WP_REST_Request;
use Automattic\WooCommerce\Blocks;
use Automattic\WooCommerce\StoreApi;

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
		add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( $this, 'save_terms_acceptance' ), 10, 2 );
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
	 * We will update the post meta
	 *
	 * @since 1.5.0
	 *
	 * @param WC_Order        $order   Order ID or order object.
	 * @param WP_REST_Request $request The API request currently being processed.
	 *
	 * @return void
	 */
	public function save_acceptance( $order, $request ) {

		if ( ! isset( $request['extensions'], $request['extensions']['_woo_additional_terms'] ) ) {
			return;
		}

		// Save the additional terms checkbox value as order meta.
		$order->update_meta_data( '_woo_additional_terms', wc_bool_to_string( isset( $request['extensions']['_woo_additional_terms']['wat_checkbox'] ) ) );

		// If the additional terms checkbox is checked, add note that customer accepted if not add note tha customer didn't accept.
		if ( isset( $request['extensions']['_woo_additional_terms']['wat_checkbox'] ) && wc_string_to_bool( $request['extensions']['_woo_additional_terms']['wat_checkbox'] ) ) {
			$order->add_order_note(
				__( 'Customer accepted the additional terms.', 'woo-additional-terms' )
			);

			return;
		}

		$order->add_order_note(
			__( 'Customer did not accept the additional terms.', 'woo-additional-terms' )
		);
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

		return array(
			'is_required'    => woo_additional_terms()->service( 'options' )->get( 'required', false ),
			'display_action' => woo_additional_terms()->service( 'options' )->get( 'display_action', 'embed' ),
			'page_content'   => woo_additional_terms()->service( 'terms' )->get( 'content' ),
			'checkbox_label' => woo_additional_terms()->service( 'terms' )->get( 'label' ),
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

								if ( ! is_string( $value ) || ! is_array( json_decode( $value, true ) ) ) {
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
