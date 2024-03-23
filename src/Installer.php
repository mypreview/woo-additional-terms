<?php
/**
 * The plugin installer class.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms;

use Woo_Additional_Terms\Enhancements;

/**
 * The plugin installer class.
 */
class Installer {

	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function activate() {

		// Add the activation timestamp, if not already added.
		woo_additional_terms()->service( 'options' )->add_usage_timestamp();
	}

	/**
	 * Deactivate the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function deactivate() {

		delete_transient( Enhancements\Rate::TRANSIENT_NAME );
	}
}
