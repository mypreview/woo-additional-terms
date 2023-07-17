<?php
/**
 * Additional terms utility functions.
 *
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\WooCommerce;

use WP_Post;

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

		if ( preg_match( '/{{additional-terms|\[additional-terms\]}}/', $notice ) ) {
			$notice = str_replace( array( '{{additional-terms}}', '[additional-terms]' ), $this->get_page_link(), $notice );
		}

		return $notice;
	}

	/**
	 * Get the terms page URI.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	private function get_page_uri() {

		$terms_page_id = woo_additional_terms()->service( 'options' )->get( 'page', 0 );
		$terms_page    = get_post( $terms_page_id );

		// Check if the terms page exists.
		if ( ! $terms_page instanceof WP_Post ) {
			return '';
		}

		// Check if the terms page is published.
		if ( 'publish' !== $terms_page->post_status ) {
			return '';
		}

		return get_permalink( $terms_page_id );
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

		// Check if get_post() function exists.
		if ( ! function_exists( 'get_post' ) ) {
			return '';
		}

		$terms_page_id = woo_additional_terms()->service( 'options' )->get( 'page', 0 );

		// Bail early, in case the terms page ID is empty.
		if ( empty( $terms_page_id ) ) {
			return '';
		}

		$terms_page = get_post( $terms_page_id );

		// Check if the terms page exists.
		if ( ! $terms_page instanceof WP_Post ) {
			return '';
		}

		// Check if the terms page is published.
		if ( 'publish' !== $terms_page->post_status ) {
			return '';
		}

		// Check if the terms page has content.
		if ( empty( $terms_page->post_content ) ) {
			return '';
		}

		return $terms_page->post_content;
	}
}
