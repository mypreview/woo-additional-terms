<?php
/**
 * WooCommerce checkout customizations.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\WooCommerce;

use WC_Order;

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

		add_action( 'before_woocommerce_init', array( $this, 'should_enforce' ) );
		add_action( 'woo_additional_terms_enforce_terms', array( $this, 'enforce_terms' ) );
	}

	/**
	 * Determine if the additional terms should be enforced during the checkout.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function should_enforce() {

		$status = woo_additional_terms()->service( 'options' )->get( 'status', '' );

		// Bail early, in case settings status is not enabled.
		if ( ! wc_string_to_bool( $status ) ) {
			return;
		}

		/**
		 * Render the additional terms.
		 *
		 * @since 1.6.0
		 */
		do_action( 'woo_additional_terms_enforce_terms' );
	}

	/**
	 * Add the additional terms to the checkout page.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function enforce_terms() {

		add_filter( 'body_class', array( $this, 'body_classes' ) );
		add_filter( 'woocommerce_checkout_show_terms', '__return_true' );
		add_filter( 'woocommerce_enable_order_notes_field', '__return_true' );
		add_filter( 'woocommerce_checkout_posted_data', array( $this, 'posted_data' ) );
		add_action( 'woocommerce_checkout_after_terms_and_conditions', array( $this, 'show_checkbox' ) );
		add_action( 'woocommerce_after_checkout_validation', array( $this, 'show_error' ), 10, 2 );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_acceptance' ), 10, 2 );
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 1.6.0
	 *
	 * @param  array $classes Classes for the body element.
	 * @return array
	 */
	public function body_classes( $classes ) {

		/**
		 * Append a class to the body element when the additional terms are enforced.
		 */
		$classes[] = sanitize_html_class( woo_additional_terms()->get_slug() . '-enforce-terms' );

		return $classes;
	}

	/**
	 * Append additional terms data if submitted and available.
	 *
	 * @since 1.6.0
	 *
	 * @param array $posted_data Get posted data from the checkout form.
	 *
	 * @return array
	 */
	public function posted_data( $posted_data ) {

		// Exit early, in case the additional terms checkbox is not available.
		// @phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['_woo_additional_terms'] ) ) {
			return $posted_data;
		}

		$posted_data['_woo_additional_terms'] = wc_string_to_bool( wp_unslash( $_POST['_woo_additional_terms'] ) );
		// @phpcs:enable WordPress.Security.NonceVerification.Missing

		return apply_filters( 'woo_additional_terms_checkout_posted_data', $posted_data );
	}

	/**
	 * Show terms checkbox.
	 *
	 * @since 1.3.3
	 *
	 * @return void
	 */
	public function show_checkbox() {

		// Enqueue the scripts and styles.
		wp_enqueue_style( 'woo-additional-terms' );
		wp_enqueue_script( 'woo-additional-terms' );

		// Render the checkbox template.
		woo_additional_terms()->service( 'template_manager' )->echo_template(
			'checkout/checkbox.php',
			array(
				'is_required'    => woo_additional_terms()->service( 'options' )->get( 'required', false ),
				'display_action' => woo_additional_terms()->service( 'options' )->get( 'action', 'embed' ),
				'page_content'   => woo_additional_terms()->service( 'terms' )->get( 'content' ),
				'checkbox_label' => woo_additional_terms()->service( 'terms' )->get( 'label' ),
			)
		);
	}

	/**
	 * In case the required additional terms checkbox is not checked upon checking out,
	 * throw the error message to prevent checkout from being processed.
	 *
	 * @since 1.3.3
	 *
	 * @param array  $fields Fields submitted via checkout form.
	 * @param object $errors Errors object.
	 *
	 * @return void
	 */
	public function show_error( $fields, $errors ) {

		$is_required = woo_additional_terms()->service( 'options' )->get( 'required', false );

		// Bail early, in case the additional terms checkbox is not required.
		if ( ! wc_string_to_bool( $is_required ) ) {
			return;
		}

		// Bail early, in case the additional terms checkbox is already checked.
		if ( ! empty( $fields['_woo_additional_terms'] ) ) {
			return;
		}

		// Get the error message.
		$error_message = woo_additional_terms()->service( 'options' )->get( 'error', __( 'Please accept the additional terms to continue.', 'woo-additional-terms' ) );

		// Add the error message.
		// Prevent the checkout process from continuing.
		$errors->add( 'terms_error', $error_message );
	}

	/**
	 * Stores additional terms submissions after a new order being processed.
	 *
	 * @since 1.3.3
	 *
	 * @param int   $order_id Current order id.
	 * @param array $fields   Fields submitted via checkout form.
	 *
	 * @return void
	 */
	public function save_acceptance( $order_id, $fields ) {

		// Get the order object.
		$order = wc_get_order( $order_id );

		// Verify the order object.
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		// Save the additional terms checkbox value as order meta.
		$order->update_meta_data( '_woo_additional_terms', wc_bool_to_string( isset( $fields['_woo_additional_terms'] ) ) );

		// If the additional terms checkbox is checked, add note that customer accepted if not add note tha customer didn't accept.
		if ( isset( $fields['_woo_additional_terms'] ) && wc_string_to_bool( $fields['_woo_additional_terms'] ) ) {
			$order->add_order_note(
				__( 'Customer accepted the additional terms.', 'woo-additional-terms' )
			);

			return;
		}

		$order->add_order_note(
			__( 'Customer did not accept the additional terms.', 'woo-additional-terms' )
		);
	}
}
