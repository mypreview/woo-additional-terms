<?php
/**
 * WooCommerce checkout editor block.
 *
 * @since 1.5.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\WooCommerce\Block;

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

/**
 * Checkout block class.
 */
class Block implements IntegrationInterface {

	/**
	 * The name of the integration.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	const NAME = '_woo_additional_terms';

	/**
	 * The name of the block.
	 *
	 * @since 1.6.7
	 *
	 * @return string
	 */
	const IDENTIFIER = 'mypreview/woo-additional-terms';

	/**
	 * The name of the integration.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_name() {

		return self::NAME;
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public function initialize() {

		add_filter( '__experimental_woocommerce_blocks_add_data_attributes_to_block', array( $this, 'add_attributes_to_frontend_blocks' ) );
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

		$allowed_blocks[] = self::IDENTIFIER;

		return $allowed_blocks;
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
			'error_message'  => woo_additional_terms()->service( 'terms' )->get( 'error' ),
		);
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @since 1.5.0
	 *
	 * @param string $file Local path to the file.
	 *
	 * @return string
	 */
	protected function get_file_version( $file ) {

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}

		return woo_additional_terms()->get_version();
	}
}
