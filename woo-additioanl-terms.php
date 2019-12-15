<?php
/**
 * The `Woo Additional Terms` bootstrap file.
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 * 
 * Woo Additional Terms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * @link              		https://www.mypreview.one
 * @since             		1.2.0
 * @package           		woo-additional-terms
 * @author     		  		MyPreview (Github: @mahdiyazdani, @mypreview)
 * @copyright 		  		© 2015 - 2019 MyPreview. All Rights Reserved.
 *
 * @wordpress-plugin
 * Plugin Name:       		Woo Additional Terms
 * Plugin URI:        		https://www.mypreview.one
 * Description:       		Add additional terms and condition checkbox to the WooCommerce checkout.
 * Version:           		1.2.0
 * Author:            		MyPreview
 * Author URI:        		https://www.mypreview.one
 * License:           		GPL-2.0
 * License URI:       		http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       		woo-additional-terms
 * Domain Path:       		/languages
 * WC requires at least: 	3.4.0
 * WC tested up to: 		3.8.1
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    wp_die();
} // End If Statement

/**
 * Gets the path to a plugin file or directory.
 * @see 	https://codex.wordpress.org/Function_Reference/plugin_basename
 * @see 	http://php.net/manual/en/language.constants.predefined.php
 */
define( 'WOO_ADDITIONAL_TERMS_VERSION', '1.2.0' );
define( 'WOO_ADDITIONAL_TERMS_FILE', __FILE__ );
define( 'WOO_ADDITIONAL_TERMS_BASENAME', basename( WOO_ADDITIONAL_TERMS_FILE ) );
define( 'WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME', plugin_basename( WOO_ADDITIONAL_TERMS_FILE ) );
define( 'WOO_ADDITIONAL_TERMS_DIR_URL', plugin_dir_url( WOO_ADDITIONAL_TERMS_FILE ) );
define( 'WOO_ADDITIONAL_TERMS_DIR_PATH', plugin_dir_path( WOO_ADDITIONAL_TERMS_FILE ) ); 

if ( ! class_exists( 'Woo_Additional_Terms' ) ) :

	/**
	 * The Woo Additional Terms - Class
	 */
	final class Woo_Additional_Terms {

		/**
         * Instance of the class.
         * 
         * @var  object   $_instance
         */
		private static $_instance = NULL;

		/**
		 * Main `Woo_Additional_Terms` instance
		 * Ensures only one instance of `Woo_Additional_Terms` is loaded or can be loaded.
		 *
		 * @access 	public
		 * @return  instance
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			} // End If Statement

			return self::$_instance;

		}

		/**
		 * Setup class.
		 *
		 * @access 	protected
		 * @return  void
		 */
		protected function __construct() {

			add_action( 'plugins_loaded', 																array( $this, 'maybe_migrate_data' ),  		10 );
			add_action( 'init', 																		array( $this, 'textdomain' ), 				10 );
			add_action( 'admin_notices', 																array( $this, 'activation' ), 				10 );
			add_filter( 'woocommerce_settings_tabs_array', 												array( $this, 'add_settings_tab' ), 	999, 1 );
			add_action( 'woocommerce_settings_tabs_woo-additional-terms', 								array( $this, 'render_plugin_page' ), 		10 );
			add_action( 'woocommerce_update_options_woo-additional-terms', 								array( $this, 'update_plugin_page' ), 		10 );
			add_action( 'woocommerce_checkout_after_terms_and_conditions', 								array( $this, 'print_checkbox' ), 			10 );
			add_action( 'woocommerce_checkout_process', 												array( $this, 'checkbox_error' ), 		 	99 );
			add_filter( sprintf( 'plugin_action_links_%s', WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME ), 		array( $this, 'additional_links' ), 	 10, 1 );

		}

		/**
		 * Cloning instances of this class is forbidden.
		 *
		 * @access 	protected
		 * @return  void
		 */
		protected function __clone() {

			_doing_it_wrong( __FUNCTION__, _x( 'Cloning instances of this class is forbidden.', 'clone', 'woo-additional-terms' ) , WOO_ADDITIONAL_TERMS_VERSION );

		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @access 	public
		 * @return  void
		 */
		public function __wakeup() {

			_doing_it_wrong( __FUNCTION__, _x( 'Unserializing instances of this class is forbidden.', 'wakeup', 'woo-additional-terms' ) , WOO_ADDITIONAL_TERMS_VERSION );

		}

		/**
		 * Migrate data from versions prior to 1.1.0.
		 *
		 * @access 	public
		 * @return  void
		 */
		public function maybe_migrate_data() {

			$get_page_id = (int) get_option( 'wc_settings_tab_wat_section_page_id' );
			$get_notice = (string) get_option( 'wc_settings_tab_wat_section_notice' );
			$get_error = (string) get_option( 'wc_settings_tab_wat_section_notice_error' );

			if ( isset( $get_page_id ) && ! empty( $get_page_id ) ) {
				delete_option( 'wc_settings_tab_wat_section_page_id' );
				update_option( '_woo_additional_terms_page_id', intval( $get_page_id ) );
			} // End If Statement

			if ( isset( $get_notice ) && ! empty( $get_notice ) ) {
				delete_option( 'wc_settings_tab_wat_section_notice' );
				update_option( '_woo_additional_terms_notice', sanitize_textarea_field( $get_notice ) );
			} // End If Statement

			if ( isset( $get_error ) && ! empty( $get_error ) ) {
				delete_option( 'wc_settings_tab_wat_section_notice_error' );
				update_option( '_woo_additional_terms_error', sanitize_text_field( $get_error ) );
			} // End If Statement

		}

		/**
		 * Load languages file and text domains.
		 * Define the internationalization functionality.
		 *
		 * @access 	public
		 * @return  void
		 */
		public function textdomain() {

			load_plugin_textdomain( 'woo-additional-terms', FALSE, dirname( dirname( WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME ) ) . '/languages/' );

		}

		/**
		 * Query WooCommerce activation.
		 *
		 * @access 	public
		 * @return  void
		 */
		public function activation() {

			if ( ! $this->is_woocommerce() ) {
				$message = sprintf( esc_html_x( 'Woo Additional Terms plugin requires the following plugin:%sWooCommerce%s', 'admin notice', 'woo-additional-terms' ), '<a href="https://wordpress.org/plugins/woocommerce" target="_blank" rel="noopener noreferrer nofollow"><em>', '</em></a>' );
				printf( '<div class="notice notice-warning notice-alt"><p>%s</p></div>', $message );
			} // End If Statement

		}

		/**
		 * Create plugin options tab (page).
		 * Add a new settings tab to the WooCommerce settings tabs array.
		 *
		 * @access 	public
		 * @param 	array 	$settings_tab 	Array of WooCommerce setting tabs & their labels.
		 * @return  array 	$settings_tab 	Modified list of of WooCommerce setting tabs & their labels.
		 */
		public function add_settings_tab( $settings_tabs ) {

			$settings_tabs['woo-additional-terms'] = _x( 'Additional Terms', 'tab title', 'woo-additional-terms' );

        	return $settings_tabs;

		}

		/**
		 * Render and display plugin options page.
		 * Uses the WooCommerce admin fields API to output settings.
		 *
		 * @access 	public
		 * @return  void
		 */
		public function render_plugin_page() {

			woocommerce_admin_fields( self::get_settings() );

		}

		/**
		 * Render and display plugin options page.
		 * Uses the WooCommerce options API to save settings.
		 *
		 * @access 	public
		 * @return  void
		 */
		public function update_plugin_page() {

			woocommerce_update_options( self::get_settings() );

		}

		/**
		 * Display additional terms and condition checkbox on 
		 * the checkout page before the submit (place order) button.
		 *
		 * @access 	public
		 * @return  void
		 */
		public function print_checkbox() {

			$get_page_id = (int) get_option( '_woo_additional_terms_page_id' );
    		$get_notice = (string) get_option( '_woo_additional_terms_notice' );

    		// Bail out, if the page ID is not defined yet!
	    	if ( ! isset( $get_page_id ) || empty( $get_page_id ) ) {
	    		return;
	    	} // End If Statement

	    	if ( FALSE !== strpos( $get_notice, '[additional-terms]' ) ) {
    			$get_notice = str_replace( '[additional-terms]', sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer nofollow">%s</a>', esc_url( get_permalink( $get_page_id ) ), esc_html( get_the_title( $get_page_id ) ) ), $get_notice ); // @codingStandardsIgnoreLine
			} // End If Statement

			woocommerce_form_field( '_woo_additional_terms', array(
			    'type' => 'checkbox',
			    'class' => array( 'woo-additional-terms woocommerce-terms-and-conditions-wrapper' ),
			    'label_class' => array( 'woocommerce-form__label woocommerce-form__label-for-checkbox checkbox' ),
			    'input_class' => array( 'woocommerce-form__input woocommerce-form__input-checkbox input-checkbox' ),
			    'required' => TRUE,
			    'label' => wp_kses_post( $get_notice )
			) );

		}

		/**
		 * Show notice if customer does not accept additional terms and conditions.
		 *
		 * @access 	public
		 * @return  void
		 */
		public function checkbox_error() {

			$get_page_id = (int) get_option( '_woo_additional_terms_page_id' );
			$get_error = (string) get_option( '_woo_additional_terms_error' );

	    	if ( ! (int) isset( $_POST['_woo_additional_terms'], $get_page_id ) && ! empty( $get_page_id ) ) {
		        wc_add_notice( wp_kses_post( $get_error ), 'error' );
		    } // End If Statement

		}

		/**
		 * Get all the settings for this plugin.
		 *
		 * @access 	public
		 * @return  void
		 */
		public static function get_settings() {

			$settings = array(
				'upsell_title' => array(
					'name' => _x( 'Looking for help customizing this plugin?', 'upsell', 'woo-additional-terms' ),
					'type' => 'title',
					'desc' => sprintf( _x( '%sHire me &#8594;%s', 'upsell', 'woo-additional-terms' ), '<a href="https://www.upwork.com/o/profiles/users/_~016ad17ad3fc5cce94/" class="button-secondary" target="_blank" rel="noopener noreferrer nofollow">', '</a>' )
				),
				'section_end_upsell' => array(
					'type' => 'sectionend'
				),
				'section_title' => array(
					'name' => _x( 'Terms and Conditions', 'settings section name', 'woo-additional-terms' ),
					'type' => 'title',
					'desc' => _x( 'This section controls the display of your additional terms and condition fieldset.', 'settings section description', 'woo-additional-terms' )
				),
				'page_id' => array(
					'name' => _x( 'Terms page', 'settings field name', 'woo-additional-terms' ),
					'desc' => _x( 'If you define a "Terms" page the customer will be asked if they accept additional terms when checking out.', 'settings field description', 'woo-additional-terms' ),
					'type' => 'single_select_page',
					'class' => 'wc-enhanced-select-nostd',
					'css' => 'min-width:300px;',
					'id' => '_woo_additional_terms_page_id',
					'desc_tip' => TRUE,
					'autoload' => FALSE
				),
				'notice' => array(
					'name' => _x( 'Notice content', 'settings field name', 'woo-additional-terms' ),
					'desc' => _x( 'Text for the additional terms checkbox that customers must accept.', 'settings field description', 'woo-additional-terms' ),
					'default' => _x( 'I have read and agree to the website [additional-terms]', 'settings field default value', 'woo-additional-terms' ),
					'type' => 'textarea',
					'css' => 'min-width:300px;',
					'id' => '_woo_additional_terms_notice',
					'desc_tip' => TRUE,
					'autoload' => FALSE
				),
				'error' => array(
					'name' => _x( 'Error content', 'settings field name', 'woo-additional-terms' ),
					'desc' => _x( 'Display friendly notice whenever customer doesn&rsquo;t accept additional terms.', 'settings field description', 'woo-additional-terms' ),
					'default' => _x( 'Please read and accept the additional terms and conditions to proceed with your order. ', 'settings field default value', 'woo-additional-terms' ),
					'type' => 'text',
					'css' => 'min-width:300px;',
					'id' => '_woo_additional_terms_error',
					'desc_tip' => TRUE,
					'autoload' => FALSE
				),
				'section_end' => array(
					'type' => 'sectionend'
				)
        	);

        	return (array) apply_filters( 'woo_additional_terms_settings_args', $settings );

		}

		/**
		 * Display additional links in plugins table page.
		 * Filters the list of action links displayed for a specific plugin in the Plugins list table.
		 *
		 * @access 	public
		 * @param   array 	$links
		 * @return  array 	$links
		 */
		public function additional_links( $links ) {

			$plugin_links = array();
			$plugin_links[] = sprintf( _x( '%sHire Me!%s', 'plugin link', 'woo-additional-terms' ) , sprintf( '<a href="https://www.upwork.com/o/profiles/users/_~016ad17ad3fc5cce94/" class="button-link-delete" target="_blank" rel="noopener noreferrer nofollow" title="%s">', esc_attr_x( 'Looking for help? Hire Me!', 'upsell', 'woo-additional-terms' ) ), '</a>' );
			$plugin_links[] = sprintf( _x( '%sSupport%s', 'plugin link', 'woo-additional-terms' ) , '<a href="https://wordpress.org/support/plugin/woo-additional-terms" target="_blank" rel="noopener noreferrer nofollow">', '</a>' );

			if ( $this->is_woocommerce() ) {
				$settings_url = add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'woo-additional-terms' ), admin_url( 'admin.php' ) );
				$plugin_links[] = sprintf( _x( '%sSettings%s', 'plugin link', 'woo-additional-terms') , sprintf( '<a href="%s" target="_self">', esc_url( $settings_url ) ), '</a>' );
			} // End If Statement

			return array_merge( $plugin_links, $links );

		}

		/**
		 * Query WooCommerce activation
		 *
		 * @access 	private
		 * @return  void
		 */
		private function is_woocommerce() {

			if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				return FALSE;
			} // End If Statement

			return TRUE;

		}

	}
endif;

/**
 * Returns the main instance of Woo_Additional_Terms to prevent the need to use globals.
 *
 * @return  object(class) 	Woo_Additional_Terms::instance
 */
if ( ! function_exists( 'woo_additional_terms_init' ) ) :
	
	function woo_additional_terms_init() {

		return Woo_Additional_Terms::instance();

	}

	woo_additional_terms_init();
endif;