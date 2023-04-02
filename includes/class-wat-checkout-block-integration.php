<?php
/**
 * Register the "Additional Terms" block for use in the Checkout Block offered by "WooCommerce Blocks".
 *
 * @link https://mypreview.one/woo-additional-terms
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.5.0
 *
 * @package woo-additional-terms
 *
 * @subpackage woo-additional-terms/includes
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;
use Automattic\WooCommerce\Blocks\StoreApi\Schemas\CheckoutSchema;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\StoreApi;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'WAT_Checkout_Block_Integration' ) ) :

	/**
	 * The checkout block integration class.
	 */
	class WAT_Checkout_Block_Integration implements IntegrationInterface {

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

			$this->register_frontend_scripts();
			$this->register_editor_scripts();
			$this->register_editor_blocks();
			$this->extend_store_api();

			add_filter( '__experimental_woocommerce_blocks_add_data_attributes_to_block', array( $this, 'add_attributes_to_frontend_blocks' ) );
			add_action( 'woocommerce_store_api_checkout_update_order_from_request', array( $this, 'save_terms_acceptance' ), 10, 2 );
		}

		/**
		 * Registers all the static resources for the front-end.
		 *
		 * @since 1.5.0
		 *
		 * @return void
		 */
		public function register_frontend_scripts() {

			wp_register_script( Woo_Additional_Terms::SLUG . '-checkout', trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/js/' . WOO_ADDITIONAL_TERMS_MIN_DIR . 'checkout.js', array( 'react', 'wp-compose', 'wp-data', 'wp-element', 'wp-i18n', 'wc-blocks-data-store', 'wc-blocks-checkout', 'wc-settings' ), WOO_ADDITIONAL_TERMS_VERSION, true );
		}

		/**
		 * Registers all the static resources for the editor.
		 *
		 * @since 1.5.0
		 *
		 * @return void
		 */
		public function register_editor_scripts() {

			wp_register_script( Woo_Additional_Terms::SLUG . '-editor', trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/js/' . WOO_ADDITIONAL_TERMS_MIN_DIR . 'block.js', array( 'react', 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-primitives', 'wc-blocks-checkout', 'wc-settings' ), WOO_ADDITIONAL_TERMS_VERSION, true );
		}

		/**
		 * Returns an array containing the handles of any scripts registered by our extension.
		 *
		 * @since 1.5.0
		 *
		 * @return array
		 */
		public function get_script_handles() {

			return array( Woo_Additional_Terms::SLUG . '-checkout' );
		}

		/**
		 * Returns an array containing the handles of any editor scripts registered by our extension.
		 *
		 * @since 1.5.0
		 *
		 * @return array
		 */
		public function get_editor_script_handles() {

			return array( Woo_Additional_Terms::SLUG . '-editor' );
		}

		/**
		 * Returns an associative array containing any data we want to be available to the scripts on the front-end.
		 *
		 * @since 1.5.0
		 *
		 * @return array
		 */
		public function get_script_data() {

			$notice  = get_option( '_woo_additional_terms_notice', '' );
			$page_id = get_option( '_woo_additional_terms_page_id', null );

			if (
				! empty( $page_id )
				&& ! empty( $notice )
				&& false !== strpos( $notice, Woo_Additional_Terms::SHORTCODE )
				&& get_post( $page_id )
			) {
				$notice = str_replace(
					Woo_Additional_Terms::SHORTCODE,
					sprintf(
						'<a href="%s" class="woo-additional-terms__link" target="_blank" rel="noopener noreferrer nofollow">%s</a>',
						esc_url( get_permalink( $page_id ) ),
						wp_kses_post( get_the_title( $page_id ) )
					),
					$notice
				);
			}

			return array(
				'notice'  => wp_kses(
					$notice,
					array(
						'a' => array(
							'href'   => array(),
							'class'  => array(),
							'target' => array(),
							'rel'    => array(),
						),
					)
				),
				'content' => Woo_Additional_Terms::terms_page_content( $page_id, false ),
			);
		}

		/**
		 * Register editor block.
		 *
		 * @since 1.5.0
		 *
		 * @return void
		 */
		public function register_editor_blocks() {}

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
		 *
		 * @phpcs:disable WordPress.Security.NonceVerification.Missing
		 */
		public function save_terms_acceptance( $order, $request ) {

			if ( ! isset( $request['extensions'], $request['extensions']['_woo_additional_terms'], $request['extensions']['_woo_additional_terms']['wat_checkbox'] ) ) {
				return;
			}

			$order->update_meta_data(
				'_woo_additional_terms',
				array(
					'id'     => wc_clean( get_option( '_woo_additional_terms_page_id', null ) ),
					'notice' => wc_clean( get_option( '_woo_additional_terms_notice', '' ) ),
				)
			);
		}

		/**
		 * Add schema Store API to support posted data.
		 * Registers the checkout endpoint extension to inform our frontend component about the result of
		 * the validity of the additional terms checkbox and react accordingly.
		 *
		 * @since 1.5.0
		 *
		 * @return void
		 */
		public function extend_store_api() {
			$extend = StoreApi::container()->get(
				ExtendSchema::class
			);

			$extend->register_endpoint_data(
				array(
					'endpoint'        => CheckoutSchema::IDENTIFIER,
					'namespace'       => $this->get_name(),
					'schema_callback' => function() {

						return array(
							'wat_checkbox' => array(
								'type'        => 'boolean',
								'context'     => array(),
								'arg_options' => array(
									'validate_callback' => function( $value ) {

										if ( ! is_bool( $value ) ) {
											/* translators: %s: Render the type of the variable. */
											return new \WP_Error( 'api-error', sprintf( esc_html__( 'Value of field %s was posted with incorrect data type.', 'woo-additional-terms' ), gettype( $value ) ) );
										}

										return true;
									},
								),
							),
						);
					},
				)
			);
		}

	}
endif;
