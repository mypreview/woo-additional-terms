<?php
/**
 * The plugin assets (static resources).
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms;

use Woo_Additional_Terms\Enhancements\Notices;

/**
 * Load plugin static resources (CSS and JS files).
 */
abstract class Assets {

	/**
	 * Enqueue editor scripts and styles.
	 *
	 * @since 1.5.0
	 *
	 * @return void
	 */
	public static function enqueue_editor() {

		wp_register_script(
			'woo-additional-terms-block',
			woo_additional_terms()->service( 'file' )->asset_path( 'block.js' ),
			array( 'react', 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-primitives', 'wc-blocks-checkout', 'wc-settings' ),
			woo_additional_terms()->get_version(),
			true
		);
	}

	/**
	 * Enqueue admin scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function enqueue_admin() {

		$version = woo_additional_terms()->get_version();

		wp_register_style(
			'woo-additional-terms-admin',
			woo_additional_terms()->service( 'file' )->asset_path( 'admin.css' ),
			array( 'woocommerce_admin_styles' ),
			$version,
			'screen'
		);
		wp_register_script(
			'woo-additional-terms-admin',
			woo_additional_terms()->service( 'file' )->asset_path( 'admin.js' ),
			array( 'jquery' ),
			$version,
			true
		);

		wp_register_style(
			'woo-additional-terms-rate',
			woo_additional_terms()->service( 'file' )->asset_path( 'rate.css' ),
			array(),
			$version,
			'screen'
		);

		wp_register_script(
			'woo-additional-terms-dismiss',
			woo_additional_terms()->service( 'file' )->asset_path( 'dismiss.js' ),
			array( 'jquery' ),
			$version,
			true
		);
		wp_localize_script(
			'woo-additional-terms-dismiss',
			'woo_additional_terms_params',
			array(
				'dismiss_nonce' => wp_create_nonce( Notices::DISMISS_NONCE_NAME ),
			)
		);
	}

	/**
	 * Enqueue front-end scripts and styles.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function enqueue_frontend() {

		$version = woo_additional_terms()->get_version();

		wp_register_style(
			'woo-additional-terms',
			woo_additional_terms()->service( 'file' )->asset_path( 'style.css' ),
			array(),
			$version,
			'screen'
		);
		wp_register_script(
			'woo-additional-terms',
			woo_additional_terms()->service( 'file' )->asset_path( 'script.js' ),
			array( 'jquery', 'wc-checkout' ),
			$version,
			true
		);

		wp_register_script(
			'woo-additional-terms-checkout',
			woo_additional_terms()->service( 'file' )->asset_path( 'checkout.js' ),
			array( 'react', 'wp-compose', 'wp-data', 'wp-element', 'wp-i18n', 'wc-blocks-data-store', 'wc-blocks-checkout', 'wc-settings' ),
			$version,
			true
		);

		wp_register_style(
			'jquery.fancybox',
			woo_additional_terms()->service( 'file' )->asset_path( 'jquery.fancybox.css' ),
			array( 'woo-additional-terms' ),
			'3.5.6',
			'screen'
		);
		wp_register_script(
			'jquery.fancybox',
			woo_additional_terms()->service( 'file' )->asset_path( 'jquery.fancybox.js' ),
			array( 'jquery', 'woo-additional-terms' ),
			'3.5.6',
			true
		);
	}
}
