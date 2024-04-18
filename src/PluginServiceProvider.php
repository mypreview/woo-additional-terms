<?php
/**
 * The implementation of the Pimple service provider interface.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms;

/**
 * Class PluginServiceProvider.
 */
class PluginServiceProvider implements Vendor\Pimple\ServiceProviderInterface {

	/**
	 * Registers services on the given container.
	 *
	 * This method should only be used to configure services and parameters.
	 * It should not get services.
	 *
	 * @since 1.6.0
	 *
	 * @param Vendor\Pimple\Container $pimple Container instance.
	 */
	public function register( $pimple ) {

		// Plugin core.
		$pimple['template_manager'] = fn() => new TemplateManager();

		// Plugin settings.
		$pimple['settings']         = fn() => new Settings\Settings();
		$pimple['settings_general'] = fn() => new Settings\Sections\General();
		$pimple['options']          = fn() => new Settings\Options();

		// Plugin WooCommerce.
		$pimple['terms'] = fn() => new WooCommerce\Terms();
	}
}
