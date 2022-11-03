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
 * @link                    https://www.mypreview.one
 * @since                   1.3.5
 * @package                 woo-additional-terms
 * @author                  MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @copyright               © 2015 - 2022 MyPreview. All Rights Reserved.
 *
 * @wordpress-plugin
 * Plugin Name:             Woo Additional Terms
 * Plugin URI:              https://www.mypreview.one
 * Description:             Add additional terms and condition checkbox to the WooCommerce checkout.
 * Version:                 1.3.6
 * Author:                  Mahdi Yazdani
 * Author URI:              https://www.mahdiyazdani.com
 * Requires at least:       5.0
 * Requires PHP:            7.2
 * License:                 GPL-3.0
 * License URI:             http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:             woo-additional-terms
 * Domain Path:             /languages
 * WC requires at least:    3.4
 * WC tested up to:         7.0
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
		'name'       => 'Plugin Name',
		'author_uri' => 'Author URI',
		'version'    => 'Version',
	),
	'plugin'
);
define( 'WOO_ADDITIONAL_TERMS_NAME', $woo_additional_terms_plugin_data['name'] );
define( 'WOO_ADDITIONAL_TERMS_VERSION', $woo_additional_terms_plugin_data['version'] );
define( 'WOO_ADDITIONAL_TERMS_AUTHOR_URI', $woo_additional_terms_plugin_data['author_uri'] );
define( 'WOO_ADDITIONAL_TERMS_SLUG', 'woo-additional-terms' );
define( 'WOO_ADDITIONAL_TERMS_FILE', __FILE__ );
define( 'WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME', plugin_basename( WOO_ADDITIONAL_TERMS_FILE ) );
define( 'WOO_ADDITIONAL_TERMS_DIR_URL', plugin_dir_url( WOO_ADDITIONAL_TERMS_FILE ) );

if ( ! class_exists( 'Woo_Additional_Terms' ) ) :

	/**
	 * The Woo Additional Terms - Class
	 */
	final class Woo_Additional_Terms {

		/**
		 * Instance of the class.
		 *
		 * @since    1.0.0
		 * @var      object    $_instance
		 */
		private static $_instance = null;

		/**
		 * Main `Woo_Additional_Terms` instance.
		 *
		 * Insures that only one instance of Woo_Additional_Terms exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since     1.0.0
		 * @return    object|Woo_Additional_Terms    The one true Woo_Additional_Terms
		 */
		public static function instance() {
			if ( ! isset( self::$_instance ) && ! ( self::$_instance instanceof Woo_Additional_Terms ) ) {
				self::$_instance = new Woo_Additional_Terms();
				self::$_instance->init();
			}

			return self::$_instance;
		}

		/**
		 * Load actions.
		 *
		 * @since     1.3.5
		 * @return    void
		 */
		private function init() {
			add_action( 'init', array( self::instance(), 'textdomain' ) );
			add_action( 'admin_notices', array( self::instance(), 'admin_notices' ) );
			add_filter( 'woocommerce_settings_tabs_array', array( self::instance(), 'add_settings_tab' ), 999, 1 );
			add_action( 'woocommerce_settings_tabs_' . WOO_ADDITIONAL_TERMS_SLUG, array( self::instance(), 'render_plugin_page' ) );
			add_action( 'woocommerce_update_options_' . WOO_ADDITIONAL_TERMS_SLUG, array( self::instance(), 'update_plugin_page' ) );
			add_action( 'wp_enqueue_scripts', array( self::instance(), 'enqueue' ) );
			add_action( 'woocommerce_checkout_after_terms_and_conditions', array( self::instance(), 'print_checkbox' ) );
			add_action( 'woocommerce_checkout_process', array( self::instance(), 'checkbox_error' ), 99 );
			add_action( 'woocommerce_checkout_update_order_meta', array( self::instance(), 'save_terms_acceptance' ) );
			add_action( 'woocommerce_admin_order_data_after_billing_address', array( self::instance(), 'terms_acceptance' ) );
			add_filter( 'plugin_action_links_' . WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME, array( self::instance(), 'add_action_links' ) );
			add_filter( 'plugin_row_meta', array( self::instance(), 'add_meta_links' ), 10, 2 );
			register_activation_hook( WOO_ADDITIONAL_TERMS_FILE, array( self::instance(), 'activation' ) );
			register_deactivation_hook( WOO_ADDITIONAL_TERMS_FILE, array( self::instance(), 'deactivation' ) );
		}

		/**
		 * Cloning instances of this class is forbidden.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function __clone() {
			_doing_it_wrong( __CLASS__, esc_html_x( 'Cloning instances of this class is forbidden.', 'clone', 'woo-additional-terms' ), esc_html( WSVPRO_VERSION ) );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function __wakeup() {
			_doing_it_wrong( __CLASS__, esc_html_x( 'Unserializing instances of this class is forbidden.', 'wakeup', 'woo-additional-terms' ), esc_html( WSVPRO_VERSION ) );
		}

		/**
		 * Load languages file and text domains.
		 * Define the internationalization functionality.
		 *
		 * @since     1.3.3
		 * @return    void
		 */
		public function textdomain() {
			load_plugin_textdomain( 'woo-additional-terms', false, dirname( dirname( WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME ) ) . '/languages/' );
		}

		/**
		 * Prints admin screen notices.
		 *
		 * @since     1.3.4
		 * @return    void
		 */
		public function admin_notices() {
			// Query WooCommerce activation.
			if ( ! $this->_is_woocommerce() ) {
				/* translators: 1: Dashicon, Open anchor tag, 2: Close anchor tag. */
				$message = sprintf( esc_html_x( '%1$s requires the following plugin: %2$sWooCommerce%3$s', 'admin notice', 'woo-additional-terms' ), sprintf( '<i class="dashicons dashicons-admin-plugins" style="vertical-align:sub"></i> <strong>%s</strong>', WOO_ADDITIONAL_TERMS_NAME ), '<a href="https://wordpress.org/plugins/woocommerce" target="_blank" rel="noopener noreferrer nofollow"><em>', '</em></a>' );
				printf( '<div class="notice notice-error notice-alt"><p>%s</p></div>', wp_kses_post( $message ) );
			} else {
				// Display a friendly admin notice upon plugin activation.
				$welcome_notice_transient = 'woo_additional_terms_welcome_notice';
				$welcome_notice           = get_transient( $welcome_notice_transient );
				if ( $welcome_notice ) {
					printf( '<div class="notice notice-info"><p>%s</p></div>', wp_kses_post( $welcome_notice ) );
					delete_transient( $welcome_notice_transient );
				}
			}
		}

		/**
		 * Create plugin options tab (page).
		 * Add a new settings tab to the WooCommerce settings tabs array.
		 *
		 * @since     1.3.3
		 * @param     array $settings_tabs    Array of WooCommerce setting tabs & their labels.
		 * @return    array
		 */
		public function add_settings_tab( $settings_tabs ) {
			$settings_tabs['woo-additional-terms'] = _x( 'Additional Terms', 'tab title', 'woo-additional-terms' );
			return $settings_tabs;
		}

		/**
		 * Render and display plugin options page.
		 * Uses the WooCommerce admin fields API to output settings.
		 *
		 * @since     1.3.3
		 * @return    void
		 */
		public function render_plugin_page() {
			woocommerce_admin_fields( self::get_settings() );
		}

		/**
		 * Render and display plugin options page.
		 * Uses the WooCommerce options API to save settings.
		 *
		 * @since     1.3.3
		 * @return    void
		 */
		public function update_plugin_page() {
			woocommerce_update_options( self::get_settings() );
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since     1.3.5
		 * @return    void
		 */
		public function enqueue() {
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			// Register the plugin stylesheet.
			wp_register_style( WOO_ADDITIONAL_TERMS_SLUG . '-style', trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/css/style' . $min . '.css', null, WOO_ADDITIONAL_TERMS_VERSION, 'screen' );
			// Register the plugin script.
			wp_register_script( WOO_ADDITIONAL_TERMS_SLUG . '-script', trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/js/script' . $min . '.js', array( 'jquery', 'wc-checkout' ), WOO_ADDITIONAL_TERMS_VERSION, true );

			// Make sure the current screen displays plugin’s settings page.
			if ( $this->_is_woocommerce() && $this->_terms_page_content() ) {
				wp_enqueue_style( WOO_ADDITIONAL_TERMS_SLUG . '-style' );
				wp_enqueue_script( WOO_ADDITIONAL_TERMS_SLUG . '-script' );
			}
		}

		/**
		 * Display additional terms and condition checkbox on
		 * the checkout page before the submit (place order) button.
		 *
		 * @since     1.3.3
		 * @return    void
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
						<span class="woocommerce-terms-and-conditions-checkbox-text"><?php echo wp_kses_post( $notice ); ?></span>&nbsp;<abbr class="required" title="<?php esc_attr_e( 'required', 'woo-additional-terms' ); ?>">*</abbr>
					</label>
				</p>
			</div>
			<?php
		}

		/**
		 * Show notice if customer does not accept additional terms and conditions.
		 *
		 * @since     1.3.3
		 * @return    void
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
		 * @since     1.3.3
		 * @param     int $order_id    Order ID.
		 * @return    void
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
		 * @since     1.3.3
		 * @param     object $order    The current order object.
		 * @return    void
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
		 * @since     1.3.5
		 * @param     array $links    Plugin table/item action links.
		 * @return    array
		 */
		public function add_action_links( $links ) {
			$plugin_links = array();
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( _x( '%1$sHire Me!%2$s', 'plugin link', 'woo-additional-terms' ), sprintf( '<a href="%s" class="button-link-delete" target="_blank" rel="noopener noreferrer nofollow" title="%s">', esc_url( WOO_ADDITIONAL_TERMS_AUTHOR_URI ), esc_attr_x( 'Looking for help? Hire Me!', 'upsell', 'woo-additional-terms' ) ), '</a>' );

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
		 * Add additional helpful links to the plugin’s metadata.
		 *
		 * @since     1.3.5
		 * @param     array  $links    An array of the plugin’s metadata.
		 * @param     string $file     Path to the plugin file relative to the plugins directory.
		 * @return    array
		 */
		public function add_meta_links( array $links, string $file ): array {
			if ( WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME !== $file ) {
				return $links;
			}

			$plugin_links = array();
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( _x( '%1$sCommunity support%2$s', 'plugin link', 'woo-additional-terms' ), sprintf( '<a href="https://wordpress.org/support/plugin/%s" target="_blank" rel="noopener noreferrer nofollow">', esc_html( WOO_ADDITIONAL_TERMS_SLUG ) ), '</a>' );
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( _x( '%1$sDonate%2$s', 'plugin link', 'woo-additional-terms' ), sprintf( '<a href="https://www.buymeacoffee.com/mahdiyazdani" class="button-link-delete" target="_blank" rel="noopener noreferrer nofollow" title="%s">☕ ', esc_attr__( 'Donate to support this plugin', 'woo-additional-terms' ) ), '</a>' );

			return array_merge( $links, $plugin_links );
		}

		/**
		 * Set the activation hook for a plugin.
		 *
		 * @since     1.3.4
		 * @return    void
		 */
		public function activation() {
			// Set up the admin notice to be displayed on activation.
			$settings_url = add_query_arg(
				array(
					'page' => 'wc-settings',
					'tab'  => 'woo-additional-terms',
				),
				admin_url( 'admin.php' )
			);
			/* translators: 1: Dashicon, 2: Plugin name, 3: Open anchor tag, 4: Close anchor tag. */
			$welcome_notice = sprintf( esc_html_x( '%1$s Thanks for installing %2$s plugin! To get started, visit the %3$splugin’s settings page%4$s.', 'admin notice', 'woo-additional-terms' ), '<i class="dashicons dashicons-admin-settings" style="vertical-align:sub"></i>', sprintf( '<strong>%s</strong>', WOO_ADDITIONAL_TERMS_NAME ), sprintf( '<a href="%s" target="_self">', esc_url( $settings_url ) ), '</a>' );
			set_transient( 'woo_additional_terms_welcome_notice', $welcome_notice, MINUTE_IN_SECONDS );
		}

		/**
		 * Set the deactivation hook for a plugin.
		 *
		 * @since     1.3.4
		 * @return    void
		 */
		public function deactivation() {
			delete_transient( 'woo_additional_terms_welcome_notice' );
		}

		/**
		 * Get all the settings for this plugin.
		 *
		 * @since     1.3.3
		 * @return    array
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
		 * @since     1.3.3
		 * @param     int  $terms_page_id    Additional terms page ID.
		 * @param     bool $echo             Output additional terms page content on the page.
		 * @return    void|bool
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
		 * @since     1.3.5
		 * @return    bool
		 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		 */
		private function _is_woocommerce() {
			// This statement prevents from producing fatal errors,
			// in case the WooCommerce plugin is not activated on the site.
			$woocommerce_plugin     = apply_filters( 'woo_additional_terms_woocommerce_path', 'woocommerce/woocommerce.php' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.HookCommentWrongStyle
			$subsite_active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			$network_active_plugins = apply_filters( 'active_plugins', get_site_option( 'active_sitewide_plugins' ) );

			// Bail early in case the plugin is not activated on the website.
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ( empty( $subsite_active_plugins ) || ! in_array( $woocommerce_plugin, $subsite_active_plugins ) ) && ( empty( $network_active_plugins ) || ! array_key_exists( $woocommerce_plugin, $network_active_plugins ) ) ) {
				return false;
			} // End If Statement

			return true;
		}

	}
endif;

if ( ! function_exists( 'woo_additional_terms_init' ) ) :
	/**
	 * Begins execution of the plugin.
	 * The main function responsible for returning the one true Woo_Additional_Terms
	 * Instance to functions everywhere.
	 *
	 * This function is meant to be used like any other global variable,
	 * except without needing to declare the global.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since     1.0.0
	 * @return    object|Woo_Additional_Terms    The one true Woo_Additional_Terms Instance.
	 */
	function woo_additional_terms_init() {
		return Woo_Additional_Terms::instance();
	}

	woo_additional_terms_init();
endif;
