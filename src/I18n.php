<?php
/**
 * The plugin internationalization class.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms;

/**
 * Loads and defines the internationalization files for this plugin.
 */
abstract class I18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function textdomain() {

		$domain = 'woo-additional-terms';
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		// Load the translation file for current language.
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . "{$domain}/{$domain}-{$locale}.mo" );
		load_plugin_textdomain( $domain, false, woo_additional_terms()->service( 'file' )->dirname() . '/languages/' );
	}
}
