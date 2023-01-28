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
 * @since                   1.0.0
 * @package                 woo-additional-terms
 * @author                  MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 * @copyright               © 2015 - 2022 MyPreview. All Rights Reserved.
 *
 * @wordpress-plugin
 * Plugin Name:             Woo Additional Terms
 * Plugin URI:              https://mypreview.one/woo-additional-terms
 * Description:             Add additional terms and condition checkbox to the WooCommerce checkout.
 * Version:                 1.5.0
 * Author:                  MyPreview
 * Author URI:              https://mypreview.one/woo-additional-terms
 * Requires at least:       5.0
 * Requires PHP:            7.4
 * License:                 GPL-3.0
 * License URI:             http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:             woo-additional-terms
 * Domain Path:             /languages
 * WC requires at least:    4.0
 * WC tested up to:         7.4
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

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
		'plugin_uri' => 'Plugin URI',
		'version'    => 'Version',
	),
	'plugin'
);
define( 'WOO_ADDITIONAL_TERMS_NAME', $woo_additional_terms_plugin_data['name'] );
define( 'WOO_ADDITIONAL_TERMS_VERSION', $woo_additional_terms_plugin_data['version'] );
define( 'WOO_ADDITIONAL_TERMS_URI', $woo_additional_terms_plugin_data['plugin_uri'] );
define( 'WOO_ADDITIONAL_TERMS_SLUG', 'woo-additional-terms' );
define( 'WOO_ADDITIONAL_TERMS_FILE', __FILE__ );
define( 'WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME', plugin_basename( WOO_ADDITIONAL_TERMS_FILE ) );
define( 'WOO_ADDITIONAL_TERMS_DIR_URL', plugin_dir_url( WOO_ADDITIONAL_TERMS_FILE ) );
define( 'WOO_ADDITIONAL_TERMS_DIR_PATH', plugin_dir_path( WOO_ADDITIONAL_TERMS_FILE ) );
define( 'WOO_ADDITIONAL_TERMS_MIN_DIR', defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : trailingslashit( 'minified' ) );

/**
 * Loads the autoloader implementation.
 */
require trailingslashit( WOO_ADDITIONAL_TERMS_DIR_PATH ) . 'vendor/autoload.php';

if ( ! class_exists( 'Woo_Additional_Terms' ) ) :

	/**
	 * The Woo Additional Terms - Class
	 */
	final class Woo_Additional_Terms {

		/**
		 * Instance of the class.
		 *
		 * @since    1.0.0
		 * @var      object    $instance
		 */
		private static $instance = null;

		/**
		 * Main `Woo_Additional_Terms` instance.
		 *
		 * Insures that only one instance of Woo_Additional_Terms exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since     1.0.0
		 * @return    null|Woo_Additional_Terms    The one true Woo_Additional_Terms
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Woo_Additional_Terms ) ) {
				self::$instance = new Woo_Additional_Terms();
				self::$instance->init();
			}

			return self::$instance;
		}

		/**
		 * Load actions.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		private function init() {
			add_action( 'init', array( self::instance(), 'textdomain' ) );
			add_action( 'admin_init', array( self::instance(), 'check_activation_timestamp' ) );
			add_action( 'admin_notices', array( self::instance(), 'admin_notices' ) );
			add_action( 'wp_ajax_woo_additional_terms_dismiss_upsell', array( self::instance(), 'dismiss_upsell' ) );
			add_action( 'wp_ajax_woo_additional_terms_dismiss_rate', array( self::instance(), 'dismiss_rate' ) );
			add_action( 'before_woocommerce_init', array( self::instance(), 'add_compatibility' ) );
			add_filter( 'woocommerce_settings_tabs_array', array( self::instance(), 'add_settings_tab' ), 999, 1 );
			add_action( 'woocommerce_settings_tabs_' . WOO_ADDITIONAL_TERMS_SLUG, array( self::instance(), 'render_plugin_page' ) );
			add_action( 'woocommerce_update_options_' . WOO_ADDITIONAL_TERMS_SLUG, array( self::instance(), 'update_plugin_page' ) );
			add_action( 'woocommerce_after_settings_' . WOO_ADDITIONAL_TERMS_SLUG, array( self::instance(), 'upsell_after_settings' ) );
			add_action( 'admin_enqueue_scripts', array( self::instance(), 'admin_enqueue' ) );
			add_action( 'wp_enqueue_scripts', array( self::instance(), 'enqueue' ) );
			add_action( 'woocommerce_blocks_checkout_block_registration', array( self::instance(), 'checkbox_block' ) );
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
			_doing_it_wrong( __FUNCTION__, esc_html_x( 'Cloning instances of this class is forbidden.', 'clone', 'woo-additional-terms' ), esc_html( WOO_ADDITIONAL_TERMS_VERSION ) );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html_x( 'Unserializing instances of this class is forbidden.', 'wakeup', 'woo-additional-terms' ), esc_html( WOO_ADDITIONAL_TERMS_VERSION ) );
		}

		/**
		 * Load languages file and text domains.
		 * Define the internationalization functionality.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function textdomain() {
			$domain = 'woo-additional-terms';
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . "{$domain}/{$domain}-{$locale}.mo" );
			load_plugin_textdomain( $domain, false, dirname( WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME ) . '/languages/' );
		}

		/**
		 * Check date on admin initiation and add to admin notice if it was more than the time limit.
		 *
		 * @since     1.4.1
		 * @return    void
		 */
		public function check_activation_timestamp() {
			if ( get_transient( 'woo_additional_terms_rate' ) ) {
				return;
			}

			// If not installation date set, then add it.
			$option_name          = 'woo_additional_terms_activation_timestamp';
			$activation_timestamp = get_site_option( $option_name );

			if ( ! $activation_timestamp ) {
				add_site_option( $option_name, time() );
				$activation_timestamp = get_site_option( $option_name );
			}
		}

		/**
		 * Prints admin screen notices.
		 *
		 * @since     1.4.0
		 * @return    void
		 */
		public function admin_notices() {
			// Query WooCommerce activation.
			if ( ! $this->is_woocommerce() ) {
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
				} else {
					if ( ! get_transient( 'woo_additional_terms_upsell' ) && ( time() - (int) get_site_option( 'woo_additional_terms_activation_timestamp' ) ) > DAY_IN_SECONDS ) {
						/* translators: 1: HTML Symbol, 2: HTML symbol, 3: Open anchor tag, 4: Close anchor tag. */
						$message = sprintf( esc_html_x( '%1$s Add unlimited “I Agree with terms & conditions” checkboxes to the WooCommerce checkout without any manual effort needed. %2$s %3$sUpgrade to PRO%4$s', 'admin notice', 'woo-additional-terms' ), '&#9745;', '&#8594;', sprintf( '<a href="%s" class="button-primary" target="_blank" rel="noopener noreferrer nofollow">', esc_url( WOO_ADDITIONAL_TERMS_URI ) ), '</a>' );
						printf( '<div id="%s-dismiss-upsell" class="notice notice-info woocommerce-message notice-alt is-dismissible"><p>%s</p></div>', esc_attr( WOO_ADDITIONAL_TERMS_SLUG ), wp_kses_post( $message ) );
					} elseif ( ! get_transient( 'woo_additional_terms_rate' ) && ( time() - (int) get_site_option( 'woo_additional_terms_activation_timestamp' ) ) > WEEK_IN_SECONDS ) {
						/* translators: 1: HTML symbol, 2: Plugin name, 3: Activation duration, 4: HTML symbol, 5: Open anchor tag, 6: Close anchor tag. */
						$message = sprintf( esc_html_x( '%1$s You have been using the %2$s plugin for %3$s now, do you like it as much as we like you? %4$s %5$sRate 5-Stars%6$s', 'admin notice', 'woo-additional-terms' ), '&#9733;', esc_html( WOO_ADDITIONAL_TERMS_NAME ), human_time_diff( (int) get_site_option( 'woo_additional_terms_activation_timestamp' ), time() ), '&#8594;', sprintf( '<a href="https://wordpress.org/support/plugin/%s/reviews?rate=5#new-post" class="button-primary" target="_blank" rel="noopener noreferrer nofollow">&#9733; ', esc_attr( WOO_ADDITIONAL_TERMS_SLUG ) ), '</a>' );
						printf( '<div id="%s-dismiss-rate" class="notice notice-info is-dismissible"><p>%s</p></div>', esc_attr( WOO_ADDITIONAL_TERMS_SLUG ), wp_kses_post( $message ) );
					}
				}
			}
		}

		/**
		 * AJAX dismiss up-sell admin notice.
		 *
		 * @since     1.4.0
		 * @return    void
		 */
		public function dismiss_upsell() {
			check_ajax_referer( WOO_ADDITIONAL_TERMS_SLUG . '-dismiss' );
			set_transient( 'woo_additional_terms_upsell', true, MONTH_IN_SECONDS );
			wp_die();
		}

		/**
		 * AJAX dismiss ask-to-rate admin notice.
		 *
		 * @since     1.4.1
		 * @return    void
		 */
		public function dismiss_rate() {
			check_ajax_referer( WOO_ADDITIONAL_TERMS_SLUG . '-dismiss' );
			set_transient( 'woo_additional_terms_rate', true, 3 * MONTH_IN_SECONDS );
			wp_die();
		}

		/**
		 * Declaring compatibility with HPOS.
		 *
		 * This plugin has nothing to do with "High-Performance Order Storage".
		 * However, the compatibility flag has been added to avoid WooCommerce declaring the plugin as "uncertain".
		 *
		 * @since     1.5.0
		 * @return    void
		 */
		public function add_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', WOO_ADDITIONAL_TERMS_FILE, true );
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
			$settings_tabs['woo-additional-terms'] = esc_html_x( 'Additional Terms', 'tab title', 'woo-additional-terms' );
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
		 * Promote PRO version adequately!
		 *
		 * @since     1.3.3
		 * @return    void
		 */
		public function upsell_after_settings() {
			/* translators: 1: Open H2 tag, 2: Close H2 tag. */
			printf( esc_html_x( '%1$sLooking to add more terms & condition checkboxes?%2$s', 'upsell', 'woo-additional-terms' ), '<h2>', '</h2>' );
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			printf( esc_html_x( '%1$sBuy PRO &#8594;%2$s', 'upsell', 'woo-additional-terms' ), sprintf( '<div class="woocommerce-message"><a href="%s" class="button-primary" target="_blank" rel="noopener noreferrer nofollow" title="%s"><span class="dashicons dashicons-cart" style="vertical-align:middle;font-size:16px;"></span> ', esc_url( WOO_ADDITIONAL_TERMS_URI ), esc_attr_x( 'Upgrade to premium version to unlock more features!', 'upsell', 'woo-additional-terms' ) ), '</a></div>' );
		}

		/**
		 * Enqueue scripts and styles for admin pages.
		 *
		 * @since     1.4.0
		 * @return    void
		 */
		public function admin_enqueue() {
			wp_register_script( WOO_ADDITIONAL_TERMS_SLUG, trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/js/' . WOO_ADDITIONAL_TERMS_MIN_DIR . 'admin.js', array( 'jquery' ), WOO_ADDITIONAL_TERMS_VERSION, true );
			wp_localize_script( WOO_ADDITIONAL_TERMS_SLUG, 'watVars', array( 'dismiss_nonce' => wp_create_nonce( WOO_ADDITIONAL_TERMS_SLUG . '-dismiss' ) ) );

			if ( ! get_transient( 'woo_additional_terms_rate' ) || ! get_transient( 'woo_additional_terms_upsell' ) ) {
				wp_enqueue_script( WOO_ADDITIONAL_TERMS_SLUG );
			}
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since     1.4.0
		 * @return    void
		 */
		public function enqueue() {
			wp_register_style( WOO_ADDITIONAL_TERMS_SLUG, trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/css/' . WOO_ADDITIONAL_TERMS_MIN_DIR . 'style.css', null, WOO_ADDITIONAL_TERMS_VERSION, 'screen' );
			wp_register_script( WOO_ADDITIONAL_TERMS_SLUG, trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/js/' . WOO_ADDITIONAL_TERMS_MIN_DIR . 'script.js', array( 'jquery', 'wc-checkout' ), WOO_ADDITIONAL_TERMS_VERSION, true );

			// Make sure the current screen displays plugin’s settings page.
			if ( $this->is_woocommerce() && self::terms_page_content() ) {
				wp_enqueue_style( WOO_ADDITIONAL_TERMS_SLUG );
				wp_enqueue_script( WOO_ADDITIONAL_TERMS_SLUG );
			}
		}

		/**
		 * Registers block type and registers with WC Blocks Integration Interface.
		 *
		 * @since     1.5.0
		 * @param     object $integration_registry    WC Blocks integration registry.
		 * @return    void
		 */
		public function checkbox_block( $integration_registry ) {
			$integration_registry->register( new WAT_Checkout_Block_Integration() );
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
				$notice = str_replace( '[additional-terms]', sprintf( '<a href="%s" class="woo-additional-terms__link" target="_blank" rel="noopener noreferrer nofollow">%s</a>', esc_url( get_permalink( $page_id ) ), esc_html( get_the_title( $page_id ) ) ), $notice );
			}

			?>
			<div class="woocommerce-terms-and-conditions-wrapper woo-additional-terms">
			<?php self::terms_page_content( $page_id, true ); ?>
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

			if ( $this->is_woocommerce() ) {
				$settings_url = add_query_arg(
					array(
						'page' => 'wc-settings',
						'tab'  => 'woo-additional-terms',
					),
					admin_url( 'admin.php' )
				);
				/* translators: 1: Open anchor tag, 2: Close anchor tag. */
				$plugin_links[] = sprintf( esc_html_x( '%1$sSettings%2$s', 'plugin settings page', 'woo-additional-terms' ), sprintf( '<a href="%s" target="_self">', esc_url( $settings_url ) ), '</a>' );
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
			$plugin_links[] = sprintf( esc_html_x( '%1$sCommunity support%2$s', 'plugin link', 'woo-additional-terms' ), sprintf( '<a href="https://wordpress.org/support/plugin/%s" target="_blank" rel="noopener noreferrer nofollow">', esc_html( WOO_ADDITIONAL_TERMS_SLUG ) ), '</a>' );
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( esc_html_x( '%1$sDonate%2$s', 'plugin link', 'woo-additional-terms' ), sprintf( '<a href="https://www.buymeacoffee.com/mahdiyazdani" target="_blank" rel="noopener noreferrer nofollow" title="%s">☕ ', esc_attr__( 'Donate to support this plugin', 'woo-additional-terms' ) ), '</a>' );
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( esc_html_x( '%1$sUpgrade to PRO%2$s', 'plugin link', 'woo-additional-terms' ), sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer nofollow" class="button-link-delete"><span class="dashicons dashicons-cart" style="font-size:16px;vertical-align:middle;"></span> ', esc_url( WOO_ADDITIONAL_TERMS_URI ) ), '</a>' );

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
		 * @since     1.4.0
		 * @return    void
		 */
		public function deactivation() {
			delete_transient( 'woo_additional_terms_rate' );
			delete_transient( 'woo_additional_terms_upsell' );
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
				'section_title' => array(
					'name' => esc_html_x( 'Terms and Conditions', 'settings section name', 'woo-additional-terms' ),
					'type' => 'title',
					'desc' => esc_html_x( 'This section controls the display of your additional terms and condition fieldset.', 'settings section description', 'woo-additional-terms' ),
				),
				'page_id' => array(
					'name'     => esc_html_x( 'Terms page', 'settings field name', 'woo-additional-terms' ),
					'desc'     => esc_html_x( 'If you define a "Terms" page the customer will be asked if they accept additional terms when checking out.', 'settings field description', 'woo-additional-terms' ),
					'type'     => 'single_select_page',
					'class'    => 'wc-enhanced-select-nostd',
					'css'      => 'min-width:300px;',
					'id'       => '_woo_additional_terms_page_id',
					'desc_tip' => true,
					'autoload' => false,
				),
				'notice' => array(
					'name'        => esc_html_x( 'Notice content', 'settings field name', 'woo-additional-terms' ),
					'desc'        => esc_html_x( 'Text for the additional terms checkbox that customers must accept.', 'settings field description', 'woo-additional-terms' ),
					'default'     => esc_html_x( 'I have read and agree to the website [additional-terms]', 'settings field default value', 'woo-additional-terms' ),
					'placeholder' => esc_html_x( 'I have read and agree to the website [additional-terms]', 'settings field placeholder', 'woo-additional-terms' ),
					'type'        => 'textarea',
					'css'         => 'min-width:300px;',
					'id'          => '_woo_additional_terms_notice',
					'desc_tip'    => true,
					'autoload'    => false,
				),
				'error' => array(
					'name'        => esc_html_x( 'Error message', 'settings field name', 'woo-additional-terms' ),
					'desc'        => esc_html_x( 'Display friendly notice whenever customer doesn&rsquo;t accept additional terms.', 'settings field description', 'woo-additional-terms' ),
					'default'     => esc_html_x( 'Please read and accept the additional terms and conditions to proceed with your order. ', 'settings field default value', 'woo-additional-terms' ),
					'placeholder' => esc_html_x( 'You must accept our additional terms.', 'setting field placeholder', 'woo-additional-terms' ),
					'type'        => 'text',
					'css'         => 'min-width:300px;',
					'id'          => '_woo_additional_terms_error',
					'desc_tip'    => true,
					'autoload'    => false,
				),
				'section_end' => array(
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
		 */
		public static function terms_page_content( $terms_page_id = null, $echo = false ) {
			$terms_page_id = $terms_page_id ? intval( $terms_page_id ) : get_option( '_woo_additional_terms_page_id', null );

			// Bail early, in case the page ID is not available or not a number.
			if ( ! $terms_page_id || ! is_numeric( $terms_page_id ) ) {
				return;
			}

			$page = get_post( $terms_page_id );

			if ( $page && 'publish' === $page->post_status && $page->post_content && ! has_shortcode( $page->post_content, 'woocommerce_checkout' ) ) {
				$return = sprintf( '<div class="woo-additional-terms__content">%s</div>', wp_kses_post( wc_format_content( $page->post_content ) ) );

				// Print on the page, only if needed.
				if ( $echo ) {
					echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}

				return $return;
			}

			return false;
		}

		/**
		 * Query WooCommerce activation
		 *
		 * @since     1.0.0
		 * @return    bool
		 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		 */
		private function is_woocommerce() {
			// This statement prevents from producing fatal errors,
			// in case the WooCommerce plugin is not activated on the site.
			$woocommerce_plugin     = apply_filters( 'woo_additional_terms_woocommerce_path', 'woocommerce/woocommerce.php' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.HookCommentWrongStyle
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
	 * @return    null|Woo_Additional_Terms    The one true Woo_Additional_Terms Instance.
	 */
	function woo_additional_terms_init() {
		return Woo_Additional_Terms::instance();
	}

	woo_additional_terms_init();
endif;
