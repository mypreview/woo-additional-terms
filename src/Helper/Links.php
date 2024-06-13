<?php
/**
 * Helper links used throughout the plugin.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Helper;

/**
 * Helper links.
 */
abstract class Links {

	/**
	 * Get the url for the documentation with the given path.
	 *
	 * @since 1.6.0
	 *
	 * @param string $path The path to the documentation page.
	 * @param array  $args The query args.
	 *
	 * @return string
	 */
	public static function docs_uri( $path = '', $args = array() ) {

		return path_join(
			'https://mypreview.github.io/woo-additional-terms',
			add_query_arg( $args, $path )
		);
	}

	/**
	 * Get the url for the pro version with the given path.
	 *
	 * @since 1.6.0
	 *
	 * @param string $path The path to the pro version page.
	 * @param array  $args The query args.
	 *
	 * @return string
	 */
	public static function pro_uri( $path = '', $args = array() ) {

		return path_join(
			'https://woocommerce.com/products/additional-terms-pro/',
			add_query_arg( $args, $path )
		);
	}
}
