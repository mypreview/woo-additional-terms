<?php
/**
 * The plugin template manager class.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms;

/**
 * Class TemplateManager.
 */
class TemplateManager {

	/**
	 * Render the template.
	 *
	 * @since 1.6.0
	 *
	 * @param string $template_name The template name.
	 * @param array  $args          The template arguments.
	 *
	 * @return void
	 */
	public function echo_template( $template_name, $args = array() ) {

		// Supports internal WooCommerce caching.
		wc_get_template(
			$template_name,
			$args,
			'',
			trailingslashit( woo_additional_terms()->service( 'file' )->plugin_path( 'templates' ) )
		);
	}
}
