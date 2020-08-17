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
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * @link                    https://www.mypreview.one
 * @since                   1.3.3
 * @package                 woo-additional-terms
 *
 * @wordpress-plugin
 * Plugin Name:             Woo Additional Terms
 * Plugin URI:              https://www.mypreview.one
 * Description:             Add additional terms and condition checkbox to the WooCommerce checkout.
 * Version:                 1.3.3
 * Author:                  MyPreview
 * Author URI:              https://mahdiyazdani.com
 * License:                 GPL-3.0
 * License URI:             http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:             woo-additional-terms
 * Domain Path:             /languages
 * WC requires at least:    3.4.0
 * WC tested up to:         4.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	wp_die();
}

/**
 * Gets the path to a plugin file or directory.
 *
 * @see     https://codex.wordpress.org/Function_Reference/plugin_basename
 * @see     http://php.net/manual/en/language.constants.predefined.php
 */
$woo_additional_terms_plugin_data = get_file_data(
	__FILE__,
	array(
		'author_uri' => 'Author URI',
		'version'    => 'Version',
	),
	'plugin'
);
define( 'WOO_ADDITIONAL_TERMS_VERSION', $woo_additional_terms_plugin_data['version'] );
define( 'WOO_ADDITIONAL_TERMS_AUTHOR_URI', $woo_additional_terms_plugin_data['author_uri'] );
define( 'WOO_ADDITIONAL_TERMS_SLUG', 'woo-additional-terms' );
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
		 * @var  object   $instance
		 */
		private static $instance = null;

		/**
		 * Main `Woo_Additional_Terms` instance
		 * Ensures only one instance of `Woo_Additional_Terms` is loaded or can be loaded.
		 *
		 * @return  instance
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Setup class.
		 *
		 * @return  void
		 */
		protected function __construct() {
			add_action( 'init', array( $this, 'textdomain' ) );
			add_action( 'admin_notices', array( $this, 'activation' ) );
			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 999, 1 );
			add_action( 'woocommerce_settings_tabs_woo-additional-terms', array( $this, 'render_plugin_page' ) );
			add_action( 'woocommerce_update_options_woo-additional-terms', array( $this, 'update_plugin_page' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
			add_action( 'woocommerce_checkout_after_terms_and_conditions', array( $this, 'print_checkbox' ) );
			add_action( 'woocommerce_checkout_process', array( $this, 'checkbox_error' ), 99 );
			add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_terms_acceptance' ) );
			add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'terms_acceptance' ) );
			add_filter( sprintf( 'plugin_action_links_%s', WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME ), array( $this, 'action_links' ) );
		}

		/**
		 * Cloning instances of this class is forbidden.
		 *
		 * @return  void
		 */
		protected function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html_x( 'Cloning instances of this class is forbidden.', 'clone', 'woo-additional-terms' ), esc_html( WOO_ADDITIONAL_TERMS_VERSION ) );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @return  void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html_x( 'Unserializing instances of this class is forbidden.', 'wakeup', 'woo-additional-terms' ), esc_html( WOO_ADDITIONAL_TERMS_VERSION ) );
		}

		/**
		 * Load languages file and text domains.
		 * Define the internationalization functionality.
		 *
		 * @return  void
		 */
		public function textdomain() {
			load_plugin_textdomain( 'woo-additional-terms', false, dirname( dirname( WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME ) ) . '/languages/' );
		}

		/**
		 * Query WooCommerce activation.
		 *
		 * @return  void
		 */
		public function activation() {
			// Query WooCommerce activation.
			if ( ! $this->_is_woocommerce() ) {
				/* translators: 1: Dashicon, Open anchor tag, 2: Close anchor tag. */
				$message = sprintf( esc_html_x( '%1$s requires the following plugin: %2$sWooCommerce%3$s', 'admin notice', 'woo-additional-terms' ), sprintf( '<i class="dashicons dashicons-admin-plugins" style="vertical-align:sub"></i> <strong>%s</strong>', WOO_ADDITIONAL_TERMS_NAME ), '<a href="https://wordpress.org/plugins/woocommerce" target="_blank" rel="noopener noreferrer nofollow"><em>', '</em></a>' );
				printf( '<div class="notice notice-error notice-alt"><p>%s</p></div>', wp_kses_post( $message ) );
				return;
			}
		}

		/**
		 * Create plugin options tab (page).
		 * Add a new settings tab to the WooCommerce settings tabs array.
		 *
		 * @param   array $settings_tabs   Array of WooCommerce setting tabs & their labels.
		 * @return  array
		 */
		public function add_settings_tab( $settings_tabs ) {
			$settings_tabs['woo-additional-terms'] = _x( 'Additional Terms', 'tab title', 'woo-additional-terms' );
			return $settings_tabs;
		}

		/**
		 * Render and display plugin options page.
		 * Uses the WooCommerce admin fields API to output settings.
		 *
		 * @return  void
		 */
		public function render_plugin_page() {
			woocommerce_admin_fields( self::get_settings() );
		}

		/**
		 * Render and display plugin options page.
		 * Uses the WooCommerce options API to save settings.
		 *
		 * @return  void
		 */
		public function update_plugin_page() {
			woocommerce_update_options( self::get_settings() );
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @return  void
		 */
		public function enqueue() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			// Enqueue a stylesheet.
			wp_register_style( sprintf( '%s-style', WOO_ADDITIONAL_TERMS_SLUG ), sprintf( '%sassets/css/style%s.css', WOO_ADDITIONAL_TERMS_DIR_URL, $min ), null, WOO_ADDITIONAL_TERMS_VERSION, 'screen' );
			// Enqueue a script.
			wp_register_script( sprintf( '%s-script', WOO_ADDITIONAL_TERMS_SLUG ), sprintf( '%sassets/js/script%s.js', WOO_ADDITIONAL_TERMS_DIR_URL, $min ), array( 'jquery', 'wc-checkout' ), WOO_ADDITIONAL_TERMS_VERSION, true );

			// Make sure the current screen displays plugin’s settings page.
			if ( $this->_is_woocommerce() && $this->_terms_page_content() ) {
				wp_enqueue_style( sprintf( '%s-style', WOO_ADDITIONAL_TERMS_SLUG ) );
				wp_enqueue_script( sprintf( '%s-script', WOO_ADDITIONAL_TERMS_SLUG ) );
			}
		}

		/**
		 * Display additional terms and condition checkbox on
		 * the checkout page before the submit (place order) button.
		 *
		 * @return  void
		 */
		public function print_checkbox() {
			$page_id = (int) get_option( '_woo_additional_terms_page_id', null );
			$notice  = (string) get_option( '_woo_additional_terms_notice' );

			// Bail out, if the page ID is not defined yet!
			if ( ! isset( $page_id ) || empty( $page_id ) ) {
				return;
			}

			if ( false !== strpos( $notice, '[additional-terms]' ) ) {
    			$notice = str_replace( '[additional-terms]', sprintf( '<a href="%s" class="woo-additional-terms__link" target="_blank" rel="noopener noreferrer nofollow">%s</a>', esc_url( get_permalink( $page_id ) ), esc_html( get_the_title( $page_id ) ) ), $notice ); // @codingStandardsIgnoreLine
			}

			?>
			<div class="woocommerce-terms-and-conditions-wrapper woo-additional-terms">
				<?php $this->_terms_page_content( $page_id, true ); ?>
				<p class="form-row validate-required">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="_woo_additional_terms" id="_woo_additional_terms" value="1" />
						<span class="woocommerce-terms-and-conditions-checkbox-text"><?php echo wp_kses_post( $notice ); ?></span>&nbsp;<span class="required">*</span>
					</label>
				</p>
			</div>
			<?php
		}

		/**
		 * Show notice if customer does not accept additional terms and conditions.
		 *
		 * @return  void
		 */
		public function checkbox_error() {
			$page_id = (int) get_option( '_woo_additional_terms_page_id' );
			$error   = (string) get_option( '_woo_additional_terms_error' );
			$data    = wp_unslash( $_POST ); //phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected, WordPress.CSRF.NonceVerification.NoNonceVerification

			if ( ! (int) isset( $data['_woo_additional_terms'], $page_id ) && ! empty( $page_id ) ) {
				wc_add_notice( wp_kses_post( $error ), 'error' );
			}
		}

		/**
		 * Fires after an order saved into the databse.
		 * We will update the post meta
		 *
		 * @param    int $order_id    Order ID.
		 * @return   void
		 */
		public function save_terms_acceptance( $order_id ) {
			$data       = wp_unslash( $_POST ); //phpcs:ignore WordPress.VIP.SuperGlobalInputUsage.AccessDetected, WordPress.CSRF.NonceVerification.NoNonceVerification
			$acceptance = isset( $data['_woo_additional_terms'] ) ? wc_string_to_bool( $data['_woo_additional_terms'] ) : null;
			$order      = wc_get_order( $order_id );

			if ( $acceptance && ( $order instanceof WC_Order ) ) {
				update_post_meta( $order_id, '_woo_additional_terms', $acceptance );
			}
		}

		/**
		 * Display the acceptance of terms & conditions on the order edit page.
		 *
		 * @param    object $order  The current order object.
		 * @return   void
		 */
		public function terms_acceptance( $order ) {
			$page_id = (int) get_option( '_woo_additional_terms_page_id', null );

			// Bail out, if the page ID is not defined yet!
			if ( ! isset( $page_id ) || empty( $page_id ) ) {
				return;
			}

			/* incorrect CSS class added here so it adopts styling we want */
			?>
			<div class="address">
			<?php
				$value = $order->get_meta( '_woo_additional_terms' ) ? esc_html__( 'Accepted', 'woo-additional-terms' ) : esc_html__( 'N/A', 'woo-additional-terms' );
				printf( '<p><strong>%s:</strong>%s</p>', wp_kses_post( get_the_title( $page_id ) ), esc_html( $value ) );
			?>
			</div>
			<?php
		}

		/**
		 * Display additional links in plugins table page.
		 * Filters the list of action links displayed for a specific plugin in the Plugins list table.
		 *
		 * @param   array $links Plugin table/item action links.
		 * @return  array
		 */
		public function action_links( $links ) {
			$plugin_links = array();
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( _x( '%1$sHire Me!%2$s', 'plugin link', 'woo-additional-terms' ), sprintf( '<a href="%s" class="button-link-delete" target="_blank" rel="noopener noreferrer nofollow" title="%s">', esc_url( WOO_ADDITIONAL_TERMS_AUTHOR_URI ), esc_attr_x( 'Looking for help? Hire Me!', 'upsell', 'woo-additional-terms' ) ), '</a>' );
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( _x( '%1$sSupport%2$s', 'plugin link', 'woo-additional-terms' ), '<a href="https://wordpress.org/support/plugin/woo-additional-terms" target="_blank" rel="noopener noreferrer nofollow">', '</a>' );

			if ( $this->_is_woocommerce() ) {
				$settings_url = add_query_arg(
					array(
						'page' => 'wc-settings',
						'tab'  => 'woo-additional-terms',
					),
					admin_url( 'admin.php' )
				);
				/* translators: 1: Open anchor tag, 2: Close anchor tag. */
				$plugin_links[] = sprintf( _x( '%1$sSettings%2$s', 'plugin settings page', 'woo-additional-terms' ), sprintf( '<a href="%s" target="_self">', esc_url( $settings_url ) ), '</a>' );
			}

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Get all the settings for this plugin.
		 *
		 * @return  array
		 */
		public static function get_settings() {
			$settings = array(
				'upsell_title'       => array(
					'name' => _x( 'Looking for help customizing this plugin?', 'upsell', 'woo-additional-terms' ),
					'type' => 'title',
					/* translators: 1: Open anchor tag, 2: Close anchor tag. */
					'desc' => sprintf( _x( '%1$sHire me &#8594;%2$s', 'upsell', 'woo-additional-terms' ), sprintf( '<a href="%s" class="button-secondary" target="_blank" rel="noopener noreferrer nofollow" title="%s">', esc_url( WOO_ADDITIONAL_TERMS_AUTHOR_URI ), esc_attr_x( 'Looking for help? Hire Me!', 'upsell', 'woo-additional-terms' ) ), '</a>' ),
				),
				'section_end_upsell' => array(
					'type' => 'sectionend',
				),
				'section_title'      => array(
					'name' => _x( 'Terms and Conditions', 'settings section name', 'woo-additional-terms' ),
					'type' => 'title',
					'desc' => _x( 'This section controls the display of your additional terms and condition fieldset.', 'settings section description', 'woo-additional-terms' ),
				),
				'page_id'            => array(
					'name'     => _x( 'Terms page', 'settings field name', 'woo-additional-terms' ),
					'desc'     => _x( 'If you define a "Terms" page the customer will be asked if they accept additional terms when checking out.', 'settings field description', 'woo-additional-terms' ),
					'type'     => 'single_select_page',
					'class'    => 'wc-enhanced-select-nostd',
					'css'      => 'min-width:300px;',
					'id'       => '_woo_additional_terms_page_id',
					'desc_tip' => true,
					'autoload' => false,
				),
				'notice'             => array(
					'name'        => _x( 'Notice content', 'settings field name', 'woo-additional-terms' ),
					'desc'        => _x( 'Text for the additional terms checkbox that customers must accept.', 'settings field description', 'woo-additional-terms' ),
					'default'     => _x( 'I have read and agree to the website [additional-terms]', 'settings field default value', 'woo-additional-terms' ),
					'placeholder' => _x( 'I have read and agree to the website [additional-terms]', 'settings field placeholder', 'woo-additional-terms' ),
					'type'        => 'textarea',
					'css'         => 'min-width:300px;',
					'id'          => '_woo_additional_terms_notice',
					'desc_tip'    => true,
					'autoload'    => false,
				),
				'error'              => array(
					'name'        => _x( 'Error message', 'settings field name', 'woo-additional-terms' ),
					'desc'        => _x( 'Display friendly notice whenever customer doesn&rsquo;t accept additional terms.', 'settings field description', 'woo-additional-terms' ),
					'default'     => _x( 'Please read and accept the additional terms and conditions to proceed with your order. ', 'settings field default value', 'woo-additional-terms' ),
					'placeholder' => _x( 'You must accept our additional terms.', 'setting field placeholder', 'woo-additional-terms' ),
					'type'        => 'text',
					'css'         => 'min-width:300px;',
					'id'          => '_woo_additional_terms_error',
					'desc_tip'    => true,
					'autoload'    => false,
				),
				'section_end'        => array(
					'type' => 'sectionend',
				),
			);

			return (array) apply_filters( 'woo_additional_terms_settings_args', $settings );
		}

		/**
		 * Output additional terms page's content (if set).
		 * The page can be set from the plugin settings page.
		 * “WooCommerce” » “Settings” » “Additional Terms”
		 *
		 * @param   int  $terms_page_id  Additional terms page ID.
		 * @param   bool $echo           Output additional terms page content on the page.
		 * @return  void
		 * @phpcs:disable PSR2.Methods.MethodDeclaration.Underscore
		 */
		private function _terms_page_content( $terms_page_id = null, $echo = false ) {
			$terms_page_id = $terms_page_id ? intval( $terms_page_id ) : get_option( '_woo_additional_terms_page_id', null );

			// Bail early, in case the page ID is not available or not a number.
			if ( ! $terms_page_id || ! is_numeric( $terms_page_id ) ) {
				return;
			}

			$page = get_post( $terms_page_id );

			if ( $page && 'publish' === $page->post_status && $page->post_content && ! has_shortcode( $page->post_content, 'woocommerce_checkout' ) ) {
				// Print on the page, only if needed.
				if ( $echo ) {
					printf( '<div class="woo-additional-terms__content">%s</div>', wp_kses_post( wc_format_content( $page->post_content ) ) );
				}
				return true;
			}

			return false;
		}

		/**
		 * Query WooCommerce activation
		 *
		 * @return  bool
		 * @phpcs:disable PSR2.Methods.MethodDeclaration.Underscore, WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		 */
		private function _is_woocommerce() {
			// This statement prevents from producing fatal errors,
			// in case the WooCommerce plugin is not activated on the site.
			$woocommerce_plugin     = apply_filters( 'woo_additional_terms_woocommerce_path', 'woocommerce/woocommerce.php' );
			$subsite_active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			$network_active_plugins = apply_filters( 'active_plugins', get_site_option( 'active_sitewide_plugins' ) );

			// Bail early in case the plugin is not activated on the website.
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ( empty( $subsite_active_plugins ) || ! in_array( $woocommerce_plugin, $subsite_active_plugins ) ) && ( empty( $network_active_plugins ) || ! array_key_exists( $woocommerce_plugin, $network_active_plugins ) ) ) {
				return false;
			}

			return true;
		}

	}
endif;

if ( ! function_exists( 'woo_additional_terms_init' ) ) :

	/**
	 * Returns the main instance of Woo_Additional_Terms to prevent the need to use globals.
	 *
	 * @return  object(class)   Woo_Additional_Terms::instance
	 */
	function woo_additional_terms_init() {
		return Woo_Additional_Terms::instance();
	}

	woo_additional_terms_init();
endif;
