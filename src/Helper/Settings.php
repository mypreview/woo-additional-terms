<?php
/**
 * Settings helpers.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Helper;

/**
 * Helpers for the plugin settings.
 */
abstract class Settings {

	/**
	 * Check if the current page is the settings page.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	public static function is_page() {

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		return (
			isset( $_GET['page'] )
			&& 'wc-settings' === $_GET['page']
			&& isset( $_GET['tab'] )
			&& woo_additional_terms()->get_slug() === $_GET['tab']
		);
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Get the plugin settings URI.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public static function page_uri() {

		// e.g, "http://example.com/wp-admin/admin.php?page=wc-settings&tab=woo-additional-terms".
		return add_query_arg(
			array(
				'page' => 'wc-settings',
				'tab'  => woo_additional_terms()->get_slug(),
			),
			admin_url( 'admin.php' )
		);
	}
}
