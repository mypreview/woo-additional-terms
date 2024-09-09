<?php
/**
 * The `Woo Additional Terms` bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * You can redistribute this plugin/software and/or modify it under
 * the terms of the GNU General Public License as published by the
 * Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * @link https://www.mypreview.one
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 *
 * @copyright © 2015 - 2024 MyPreview. All Rights Reserved.
 *
 * @wordpress-plugin
 * Plugin Name: Woo Additional Terms
 * Plugin URI: https://mypreview.one/woo-additional-terms
 * Description: Improve your checkout process by adding an extra checkbox for terms and conditions. Keep track of acceptance to ensure transparency and security.
 * Version: 1.6.8.1
 * Author: MyPreview
 * Author URI: https://mypreview.one/woo-additional-terms
 * Requires at least: 5.9
 * Requires PHP: 7.4
 * License: GPL-3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain: woo-additional-terms
 * Domain Path: /languages
 *
 * WC requires at least: 5.5
 * WC tested up to: 9.3
 */

use Woo_Additional_Terms\Plugin;
use WC_Install_Notice\Nag;

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * Loads the PSR-4 autoloader implementation.
 */
require_once untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/vendor/autoload.php';

/**
 * Initialize the plugin.
 *
 * @since 1.6.0
 *
 * @return null|Plugin
 */
function woo_additional_terms() {

	static $instance;

	if ( is_null( $instance ) ) {
		$version  = get_file_data( __FILE__, array( 'Version' => 'Version' ), false );
		$instance = new Plugin( $version['Version'] ?? '1.0.0', __FILE__ );
	}

	return $instance;
}

/**
 * Load the plugin after all plugins are loaded.
 *
 * @since 1.6.0
 *
 * @return void
 */
function woo_additional_terms_load() {

	// Fetch the instance.
	woo_additional_terms();
}

if ( ! (
		( new Nag() )
		->set_file_path( __FILE__ )
		->set_plugin_name( 'Additional Terms for WooCommerce' )
		->does_it_requires_nag()
	)
) {

	add_action( 'woocommerce_loaded', 'woo_additional_terms_load', 20 );

	// Register activation and deactivation hooks.
	register_activation_hook( __FILE__, array( 'Woo_Additional_Terms\\Installer', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Woo_Additional_Terms\\Installer', 'deactivate' ) );
}
