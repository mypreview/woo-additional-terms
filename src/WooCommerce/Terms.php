<?php
/**
 * Additional terms utility functions.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\WooCommerce;

use WP_Post;
use Elementor;

/**
 * Terms class.
 */
class Terms {

	/**
	 * Get the internal function.
	 *
	 * @since 1.6.0
	 *
	 * @param string $name The function name.
	 *
	 * @return array|bool
	 */
	public function get( $name ) {

		// Call the internal function if exists.
		if ( method_exists( $this, "get_{$name}" ) ) {
			return call_user_func( array( $this, "get_{$name}" ) );
		}

		return false;
	}

	/**
	 * Get the terms page checkbox label.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	private function get_label() {

		$notice = woo_additional_terms()->service( 'options' )->get( 'notice', '' );

		if ( preg_match( '/{{additional-terms}}|\[additional-terms\]/', $notice ) ) {
			$notice = str_replace( array( '{{additional-terms}}', '[additional-terms]' ), $this->get_page_link(), $notice );
		}

		return $notice;
	}

	/**
	 * Get the terms error message.
	 * Note that empty error message indicates that the terms are not required.
	 *
	 * @since 1.6.4
	 *
	 * @return string
	 */
	private function get_error() {

		$is_required = woo_additional_terms()->service( 'options' )->get( 'required', 'no' );

		// Bail early, in case the terms are not required.
		if ( ! wc_string_to_bool( $is_required ) ) {
			return '';
		}

		return woo_additional_terms()->service( 'options' )->get( 'error', __( 'Please accept the additional terms to continue.', 'woo-additional-terms' ) );
	}

	/**
	 * Get the terms page URI.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	private function get_page_uri() {

		$terms_page_id = woo_additional_terms()->service( 'options' )->get( 'page', false );

		if ( ! is_numeric( $terms_page_id ) ) {
			return '';
		}

		$terms_page = get_post( $terms_page_id );

		// Check if the terms page exists, and is a page.
		if ( ! ( $terms_page instanceof WP_Post ) || 'page' !== $terms_page->post_type ) {
			return '';
		}

		$display_action = woo_additional_terms()->service( 'options' )->get( 'action', 'embed' );

		// Bail early, in case the display action is set to "New Tab", and the terms page is not published.
		if ( 'publish' !== $terms_page->post_status && 'newtab' === $display_action ) {
			return '';
		}

		return get_permalink( $terms_page );
	}

	/**
	 * Get the terms page hyperlink.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	private function get_page_link() {

		$terms_page_uri = $this->get_page_uri();

		// Check if the terms page URI is empty.
		if ( empty( $terms_page_uri ) ) {
			return '';
		}

		$display_action    = woo_additional_terms()->service( 'options' )->get( 'action', 'embed' );
		$anchor_attributes = array(
			'href'        => esc_url( $terms_page_uri ),
			'class'       => 'woo-additional-terms__link',
			'target'      => '_blank',
			'data-action' => $display_action,
		);

		// Append additional attributes in case the display action is set to "Modal".
		if ( 'modal' === $display_action ) {

			// Enqueue the scripts and styles.
			wp_enqueue_style( 'jquery.fancybox' );
			wp_enqueue_script( 'jquery.fancybox' );

			$anchor_attributes['data-fancybox']         = '';
			$anchor_attributes['data-animation-effect'] = 'fade';
			$anchor_attributes['data-width']            = '1000';
			$anchor_attributes['data-height']           = '500';
			$anchor_attributes['data-src']              = '#woo-additional-terms-content';
		}

		return sprintf(
			'<a %s>%s</a>',
			wc_implode_html_attributes( $anchor_attributes ),
			esc_html( get_the_title( woo_additional_terms()->service( 'options' )->get( 'page', 0 ) ) )
		);
	}

	/**
	 * Get the terms page content.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	private function get_content() {

		$display_action = woo_additional_terms()->service( 'options' )->get( 'action', 'embed' );

		// Bail early, in case the display action is set to "New Tab".
		if ( 'newtab' === $display_action ) {
			return '';
		}

		$terms_page_id = woo_additional_terms()->service( 'options' )->get( 'page', false );

		// Bail early, in case the terms page ID is not valid.
		if ( empty( $terms_page_id ) || ! function_exists( 'get_post' ) ) {
			return '';
		}

		$terms_page = get_post( $terms_page_id );

		// Check if the terms page exists, and has content.
		if ( ! ( $terms_page instanceof WP_Post ) || empty( $terms_page->post_content ) ) {
			return '';
		}

		// Use Elementor to render the post content, in case the page is built with Elementor.
		if ( class_exists( Elementor\Plugin::class ) && Elementor\Plugin::$instance->db->is_built_with_elementor( $terms_page_id ) ) {
			return Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $terms_page_id, true );
		}

		return wc_format_content( $terms_page->post_content );
	}
}
