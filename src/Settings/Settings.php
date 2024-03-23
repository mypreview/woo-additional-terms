<?php
/**
 * The plugin settings.
 *
 * @since 1.4.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Settings;

use WC_Settings_Page;
use Woo_Additional_Terms\Helper;

/**
 * Class Settings.
 */
class Settings extends WC_Settings_Page {

	/**
	 * Setup settings class.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function __construct() {

		$this->assign();
		$this->setup();
		$this->enqueue();

		parent::__construct();
	}

	/**
	 * Assign the settings properties.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	private function assign() {

		$this->id    = sanitize_key( woo_additional_terms()->get_slug() );
		$this->label = _x( 'Additional Terms', 'settings tab label', 'woo-additional-terms' );
	}

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function setup() {

		add_filter( 'admin_body_class', array( $this, 'add_body_class' ) );
		add_action( "woocommerce_settings_{$this->id}", array( $this, 'sidebar' ) );
	}

	/**
	 * Add plugin specific class to body.
	 *
	 * @since 1.4.0
	 *
	 * @param string $classes Classes to be added to the body element.
	 *
	 * @return string
	 */
	public function add_body_class( $classes ) {

		// Bail early if the current page is not the settings page.
		if ( ! Helper\Settings::is_page() ) {
			return $classes;
		}

		$classes .= sprintf( ' %s-page', sanitize_html_class( $this->id ) );

		return $classes;
	}

	/**
	 * Display the sidebar.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	public function sidebar() {

		woo_additional_terms()->service( 'template_manager' )->echo_template(
			'sidebar/sidebar.php'
		);
	}

	/**
	 * Enqueue the settings assets.
	 *
	 * @since 1.4.0
	 *
	 * @return void
	 */
	private function enqueue() {

		// Bail early if the current page is not the settings page.
		if ( ! Helper\Settings::is_page() ) {
			return;
		}

		// Enqueue the settings assets.
		wp_enqueue_style( 'woo-additional-terms-admin' );
		wp_enqueue_script( 'woo-additional-terms-admin' );
	}

	/**
	 * Get own sections.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	protected function get_own_sections() {

		return array(
			'' => _x( 'General', 'settings tab', 'woo-additional-terms' ),
		);
	}

	/**
	 * Get settings array.
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	protected function get_settings_for_default_section() {

		return woo_additional_terms()->service( 'settings_general' )->get_fields();
	}
}
