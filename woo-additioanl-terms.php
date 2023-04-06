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
 * @author MyPreview (Github: @mahdiyazdani, @gooklani, @mypreview)
 *
 * @copyright © 2015 - 2023 MyPreview. All Rights Reserved.
 *
 * @wordpress-plugin
 * Plugin Name:          Woo Additional Terms
 * Plugin URI:           https://mypreview.one/woo-additional-terms
 * Description:          Add additional terms and conditions checkbox to your WooCommerce checkout page.
 * Version:              1.5.2
 * Author:               MyPreview
 * Author URI:           https://mypreview.one/woo-additional-terms
 * Requires at least:    5.0
 * Requires PHP:         7.4
 * License:              GPL-3.0
 * License URI:          http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:          woo-additional-terms
 * Domain Path:          /languages
 * WC requires at least: 4.0
 * WC tested up to:      7.5
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Gets the path to a plugin file or directory.
 *
 * @see https://codex.wordpress.org/Function_Reference/plugin_basename
 * @see http://php.net/manual/en/language.constants.predefined.php
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
define( 'WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WOO_ADDITIONAL_TERMS_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'WOO_ADDITIONAL_TERMS_DIR_PATH', plugin_dir_path( __FILE__ ) );
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
		 * @since 1.0.0
		 *
		 * @var object $instance
		 */
		private static $instance;

		/**
		 * Additional terms page shortcode.
		 *
		 * @since 1.5.2
		 */
		const SHORTCODE = '[additional-terms]';

		/**
		 * Plugin slug.
		 *
		 * @since 1.5.2
		 */
		const SLUG = 'woo-additional-terms';

		/**
		 * Nonce name for Ajax requests verification.
		 *
		 * @since 1.5.2
		 */
		const NONCE = 'woo-additional-terms-dismiss';

		/**
		 * Main `Woo_Additional_Terms` instance.
		 *
		 * Insures that only one instance of Woo_Additional_Terms exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0.0
		 *
		 * @return null|Woo_Additional_Terms The one true Woo_Additional_Terms
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
		 * @since 1.0.0
		 *
		 * @return void
		 */
		private function init() {

			add_action( 'init', array( self::instance(), 'textdomain' ) );
			add_action( 'admin_init', array( self::instance(), 'check_activation_timestamp' ) );
			add_action( 'admin_notices', array( self::instance(), 'admin_notices' ) );
			add_action( 'wp_ajax_woo_additional_terms_dismiss_upsell', array( self::instance(), 'dismiss_upsell' ) );
			add_action( 'wp_ajax_woo_additional_terms_dismiss_rate', array( self::instance(), 'dismiss_rate' ) );
			add_action( 'wp_ajax_woo_additional_terms_dismiss_rated', array( self::instance(), 'dismiss_rated' ) );
			add_action( 'before_woocommerce_init', array( self::instance(), 'add_compatibility' ) );
			add_filter( 'woocommerce_settings_tabs_array', array( self::instance(), 'add_settings_tab' ), 999 );
			add_action( 'woocommerce_settings_tabs_' . self::SLUG, array( self::instance(), 'render_plugin_page' ) );
			add_action( 'woocommerce_update_options_' . self::SLUG, array( self::instance(), 'update_plugin_page' ) );
			add_action( 'woocommerce_after_settings_' . self::SLUG, array( self::instance(), 'upsell_after_settings' ) );
			add_action( 'admin_enqueue_scripts', array( self::instance(), 'admin_enqueue' ) );
			add_action( 'wp_enqueue_scripts', array( self::instance(), 'enqueue' ) );
			add_action( 'woocommerce_blocks_checkout_block_registration', array( self::instance(), 'checkbox_block' ) );
			add_action( 'woocommerce_checkout_after_terms_and_conditions', array( self::instance(), 'print_checkbox' ) );
			add_action( 'woocommerce_checkout_process', array( self::instance(), 'checkbox_error' ), 99 );
			add_action( 'woocommerce_checkout_update_order_meta', array( self::instance(), 'save_terms_acceptance' ) );
			add_action( 'woocommerce_admin_order_data_after_billing_address', array( self::instance(), 'terms_acceptance' ) );
			add_filter( 'admin_footer_text', array( self::instance(), 'ask_to_rate' ) );
			add_filter( 'plugin_action_links_' . WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME, array( self::instance(), 'add_action_links' ) );
			add_filter( 'plugin_row_meta', array( self::instance(), 'add_meta_links' ), 10, 2 );

			register_activation_hook( __FILE__, array( self::instance(), 'activation' ) );
			register_deactivation_hook( __FILE__, array( self::instance(), 'deactivation' ) );
		}

		/**
		 * Cloning instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		protected function __clone() {

			_doing_it_wrong( __FUNCTION__, esc_html_x( 'Cloning instances of this class is forbidden.', 'clone', 'woo-additional-terms' ), esc_html( WOO_ADDITIONAL_TERMS_VERSION ) );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function __wakeup() {

			_doing_it_wrong( __FUNCTION__, esc_html_x( 'Unserializing instances of this class is forbidden.', 'wakeup', 'woo-additional-terms' ), esc_html( WOO_ADDITIONAL_TERMS_VERSION ) );
		}

		/**
		 * Load languages file and text domains.
		 * Define the internationalization functionality.
		 *
		 * @since 1.0.0
		 *
		 * @return void
		 */
		public function textdomain() {

			$domain = 'woo-additional-terms';
			// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . "{$domain}/{$domain}-{$locale}.mo" );
			load_plugin_textdomain( $domain, false, dirname( WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME ) . '/languages/' );
		}

		/**
		 * Check date on admin initiation and add to admin notice if it was more than the time limit.
		 *
		 * @since 1.4.1
		 *
		 * @return void
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
			}
		}

		/**
		 * Prints admin screen notices.
		 *
		 * @since 1.4.0
		 *
		 * @return void
		 */
		public function admin_notices() {

			// Query WooCommerce activation.
			if ( ! $this->is_woocommerce() ) :
				$message = sprintf(
					/* translators: 1: Dashicon, Open anchor tag, 2: Close anchor tag. */
					esc_html_x( '%1$s requires the following plugin: %2$sWooCommerce%3$s', 'admin notice', 'woo-additional-terms' ),
					sprintf(
						'<i class="dashicons dashicons-admin-plugins"></i> <strong>%s</strong>',
						WOO_ADDITIONAL_TERMS_NAME
					),
					'<a href="https://wordpress.org/plugins/woocommerce" target="_blank" rel="noopener noreferrer nofollow"><em>',
					'</em></a>'
				);
				?>
				<div class="notice notice-error notice-alt">
					<p><?php echo wp_kses_post( $message ); ?></p>
				</div>
				<?php
				return;
			endif;

			// Display a friendly admin notice upon plugin activation.
			$welcome_notice_transient = 'woo_additional_terms_welcome_notice';
			$welcome_notice           = get_transient( $welcome_notice_transient );

			if ( $welcome_notice ) :
				?>
				<div class="notice notice-info">
					<p><?php echo wp_kses_post( $welcome_notice ); ?></p>
				</div>
				<?php
				delete_transient( $welcome_notice_transient );
				return;
			endif;

			if ( ! $this->is_pro() && ! get_transient( 'woo_additional_terms_upsell' ) && ( time() - (int) get_site_option( 'woo_additional_terms_activation_timestamp' ) ) > DAY_IN_SECONDS ) :
				?>
				<div id="<?php echo esc_attr( self::SLUG ); ?>-dismiss-upsell" class="notice woocommerce-message notice-alt is-dismissible" style="border-left-color:#00818a">
					<p>
						<i class="dashicons dashicons-saved" style="color:#00818a;box-shadow:inset 0 0 0 2px #c62a88"></i>
						<strong>
							<?php echo esc_html_x( 'Upgrade to Woo Additional Terms PRO', 'admin notice', 'woo-additional-terms' ); ?>
						</strong>
					</p>
					<p>
						<?php echo esc_html_x( 'Unlock the full potential of the plugin. Enjoy unlimited custom terms and conditions checkboxes, flexible display options, advanced conditional logic, and more.', 'admin notice', 'woo-additional-terms' ); ?>
					</p>
					<p>
						<?php echo esc_html_x( 'Upgrade now to access advanced features and improve customer trust!', 'admin notice', 'woo-additional-terms' ); ?>
					</p>
					<p>
						<a href="<?php echo esc_url( WOO_ADDITIONAL_TERMS_URI ); ?>" class="button-primary" target="_blank" rel="noopener noreferrer nofollow" style="margin-top:10px;">
							<?php echo esc_html_x( 'Go PRO for More Options', 'admin notice', 'woo-additional-terms' ); ?> &#8594;
						</a>
					</p>
				</div>
				<?php
				return;
			endif;

			if ( ! get_option( 'woo_additional_terms_rated' ) && ! get_transient( 'woo_additional_terms_rate' ) && ( time() - (int) get_site_option( 'woo_additional_terms_activation_timestamp' ) ) > WEEK_IN_SECONDS ) :
				?>
				<div id="<?php echo esc_attr( self::SLUG ); ?>-dismiss-rate" class="notice notice-alt is-dismissible" style="border-left-color:#00818a">
					<p>
						<i class="dashicons dashicons-star-filled" style="color:#c62a88;vertical-align:sub;"></i>
						<strong>
							<?php
							printf(
								/* translators: 1: Activation duration, 2: Plugin name */
								esc_html_x( '%1$s have passed since you started using %2$s.', 'admin notice', 'woo-additional-terms' ),
								esc_html( human_time_diff( (int) get_site_option( 'woo_additional_terms_activation_timestamp' ), time() ) ),
								esc_html( WOO_ADDITIONAL_TERMS_NAME )
							);
							?>
						</strong>
					</p>
					<p>
						<?php echo esc_html_x( 'Would you kindly consider leaving a review and letting us know how the plugin has helped your business? Your feedback is greatly appreciated!', 'admin notice', 'woo-additional-terms' ); ?>
					</p>
					<p>
						<a href="https://wordpress.org/support/plugin/<?php echo esc_attr( self::SLUG ); ?>/reviews?rate=5#new-post" class="button-primary notice-dismiss-later" target="_blank" rel="noopener noreferrer nofollow" style="margin-top:10px;">
							&#9733;
							<?php echo esc_html_x( 'Give 5 Stars', 'admin notice', 'woo-additional-terms' ); ?> &#8594;
						</a>
						<button class="button-link notice-dismiss-later" style="margin-left:10px;">
							<?php echo esc_html_x( 'Maybe later', 'admin notice', 'woo-additional-terms' ); ?>
						</button>
						<button class="button-link notice-dismiss-rated" style="margin-left:10px;">
							<?php echo esc_html_x( 'I already did!', 'admin notice', 'woo-additional-terms' ); ?>
						</button>
					</p>
				</div>
				<?php
			endif;
		}

		/**
		 * AJAX dismiss up-sell admin notice.
		 *
		 * @since 1.4.0
		 *
		 * @return void
		 */
		public function dismiss_upsell() {

			check_ajax_referer( self::NONCE );

			set_transient( 'woo_additional_terms_upsell', true, MONTH_IN_SECONDS );

			exit();
		}

		/**
		 * AJAX dismiss ask-to-rate admin notice.
		 *
		 * @since 1.4.1
		 *
		 * @return void
		 */
		public function dismiss_rate() {

			check_ajax_referer( self::NONCE );

			set_transient( 'woo_additional_terms_rate', true, 3 * MONTH_IN_SECONDS );

			exit();
		}

		/**
		 * AJAX dismiss ask-to-rate admin notice for users who already rated.
		 *
		 * @since 1.5.2
		 *
		 * @return void
		 */
		public function dismiss_rated() {

			check_ajax_referer( self::NONCE );

			add_option( 'woo_additional_terms_rated', true );

			exit();
		}

		/**
		 * Declaring compatibility with HPOS.
		 *
		 * This plugin has nothing to do with "High-Performance Order Storage".
		 * However, the compatibility flag has been added to avoid WooCommerce declaring the plugin as "uncertain".
		 *
		 * @since 1.5.0
		 *
		 * @return void
		 */
		public function add_compatibility() {

			if ( ! class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				return;
			}

			FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
		}

		/**
		 * Create plugin options tab (page).
		 * Add a new settings tab to the WooCommerce settings tabs array.
		 *
		 * @since 1.3.3
		 *
		 * @param array $settings_tabs Array of WooCommerce setting tabs & their labels.
		 *
		 * @return array
		 */
		public function add_settings_tab( $settings_tabs ) {

			$settings_tabs['woo-additional-terms'] = esc_html_x( 'Additional Terms', 'tab title', 'woo-additional-terms' );

			return $settings_tabs;
		}

		/**
		 * Render and display plugin options page.
		 * Uses the WooCommerce admin fields API to output settings.
		 *
		 * @since 1.3.3
		 *
		 * @return void
		 */
		public function render_plugin_page() {

			woocommerce_admin_fields( self::get_settings() );
		}

		/**
		 * Render and display plugin options page.
		 * Uses the WooCommerce options API to save settings.
		 *
		 * @since 1.3.3
		 *
		 * @return void
		 */
		public function update_plugin_page() {

			woocommerce_update_options( self::get_settings() );
		}

		/**
		 * Promote PRO version adequately!
		 *
		 * @since 1.3.3
		 *
		 * @return void
		 */
		public function upsell_after_settings() {

			// Bail early, in case the PRO version of the plugin is installed.
			if ( $this->is_pro() ) {
				return;
			}

			?>
			<div class="woocommerce-message" style="background:#fff;border:1px solid #dadada;padding:25px 20px;margin-top:20px;position:relative;">
				<h3 style="margin-top:0;">
					<?php echo esc_html_x( 'Upgrade to Woo Additional Terms PRO for More Control Over Your Store’s Policies', 'upsell', 'woo-additional-terms' ); ?>
				</h3>
				<p class="importer-title">
					<?php echo esc_html_x( 'Are you looking for more advanced features to customize your WooCommerce store’s checkout process? Upgrade to Woo Additional Terms PRO and get access to a wealth of additional functionalities that will allow you to take control of your store’s policies and improve customer trust.', 'upsell', 'woo-additional-terms' ); ?>
				</p>
				<p class="importer-title">
					<?php echo esc_html_x( 'With Woo Additional Terms PRO, you’ll enjoy an abundance of features that aren’t available in the free version, including:', 'upsell', 'woo-additional-terms' ); ?>
				</p>
				<ul style="display:grid;gap:5px 10px;grid-template-columns:repeat(auto-fit,minmax(420px,1fr));font-size:14px;margin-block:20px;">
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Unlimited custom terms and conditions checkboxes', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Create complex conditional logic to display checkboxes', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Restrict terms checkboxes by Products', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Restrict terms checkboxes by Tags', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Restrict terms checkboxes by Categories', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Restrict terms checkboxes by Types of products', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Restrict terms checkboxes by Shipping classes', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Restrict terms checkboxes by WooCommerce brands', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Restrict terms checkboxes by Locations', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Track of agreements with detailed acceptance summary', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Display the terms content in a modal', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Display the terms content in a new tab', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Choose to make the terms checkbox required or optional', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Choose whether or not the checkbox is checked by default', 'upsell', 'woo-additional-terms' ); ?>
					</li>
					<li>
						<i class="dashicons dashicons-yes"></i>
						<?php echo esc_html_x( 'Import and export your plugin settings and content', 'upsell', 'woo-additional-terms' ); ?>
					</li>
				</ul>
				<p class="importer-title">
					<?php echo esc_html_x( 'Why settle for the basic version when you can have access to all these advanced features?', 'upsell', 'woo-additional-terms' ); ?>
				</p>
				<p style="margin-bottom:0;">
					<a href="<?php echo esc_url( WOO_ADDITIONAL_TERMS_URI ); ?>" class="button-primary" target="_blank" rel="noopener noreferrer nofollow">
						<?php echo esc_html_x( 'Go PRO for More Options', 'upsell', 'woo-additional-terms' ); ?>
						&#8594;
					</a>
				</p>
			</div>
			<?php
		}

		/**
		 * Enqueue scripts and styles for admin pages.
		 *
		 * @since 1.4.0
		 *
		 * @return void
		 */
		public function admin_enqueue() {

			wp_register_script( self::SLUG, trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/js/' . WOO_ADDITIONAL_TERMS_MIN_DIR . 'admin.js', array( 'jquery' ), WOO_ADDITIONAL_TERMS_VERSION, true );
			wp_localize_script( self::SLUG, 'watVars', array( 'dismiss_nonce' => wp_create_nonce( self::NONCE ) ) );

			if ( ! get_transient( 'woo_additional_terms_rate' ) || ! get_transient( 'woo_additional_terms_upsell' ) ) {
				wp_enqueue_script( self::SLUG );
			}
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @since 1.4.0
		 *
		 * @return void
		 */
		public function enqueue() {

			wp_register_style( self::SLUG, trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/css/' . WOO_ADDITIONAL_TERMS_MIN_DIR . 'style.css', null, WOO_ADDITIONAL_TERMS_VERSION, 'screen' );
			wp_register_script( self::SLUG, trailingslashit( WOO_ADDITIONAL_TERMS_DIR_URL ) . 'assets/js/' . WOO_ADDITIONAL_TERMS_MIN_DIR . 'script.js', array( 'jquery', 'wc-checkout' ), WOO_ADDITIONAL_TERMS_VERSION, true );

			// Make sure the current screen displays plugin’s settings page.
			if ( $this->is_woocommerce() && self::terms_page_content() ) {
				wp_enqueue_style( self::SLUG );
				wp_enqueue_script( self::SLUG );
			}
		}

		/**
		 * Registers block type and registers with WC Blocks Integration Interface.
		 *
		 * @since 1.5.0
		 *
		 * @param object $integration_registry WC Blocks integration registry.
		 *
		 * @return void
		 */
		public function checkbox_block( $integration_registry ) {

			$integration_registry->register( new WAT_Checkout_Block_Integration() );
		}

		/**
		 * Display additional terms and condition checkbox on
		 * the checkout page before to submit (place order) button.
		 *
		 * @since 1.3.3
		 *
		 * @return void
		 */
		public function print_checkbox() {

			$page_id = get_option( '_woo_additional_terms_page_id', null );
			$notice  = get_option( '_woo_additional_terms_notice', '' );

			if (
				! empty( $page_id )
				&& ! empty( $notice )
				&& false !== strpos( $notice, self::SHORTCODE )
				&& get_post( $page_id )
			) {
				$notice = str_replace(
					self::SHORTCODE,
					sprintf(
						'<a href="%s" class="woo-additional-terms__link" target="_blank" rel="noopener noreferrer nofollow">%s</a>',
						esc_url( get_permalink( $page_id ) ),
						wp_kses_post( get_the_title( $page_id ) )
					),
					$notice
				);
			}

			if ( empty( $notice ) ) {
				return;
			}

			?>
			<div class="woocommerce-terms-and-conditions-wrapper woo-additional-terms">
				<?php self::terms_page_content( $page_id, true ); ?>

				<p class="form-row validate-required">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
					<input type="checkbox" class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" name="_woo_additional_terms" id="_woo_additional_terms" value="1" />
						<span class="woocommerce-terms-and-conditions-checkbox-text"><?php echo wp_kses_post( $notice ); ?></span>
						<abbr class="required" title="<?php esc_attr_e( 'required', 'woo-additional-terms' ); ?>">*</abbr>
				</label>
				</p>
			</div>
			<?php
		}

		/**
		 * Show notice if customer does not accept additional terms and conditions.
		 *
		 * @since 1.3.3
		 *
		 * @return void
		 */
		public function checkbox_error() {

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( isset( $_POST['_woo_additional_terms'] ) ) {
				return;
			}

			$notice = get_option( '_woo_additional_terms_notice', '' );

			if ( empty( $notice ) ) {
				return;
			}

			$error = get_option( '_woo_additional_terms_error', '' );

			if ( empty( $error ) ) {
				return;
			}

			wc_add_notice( wp_kses_post( $error ), 'error' );
		}

		/**
		 * Fires after an order saved into the database.
		 * We will update the post meta
		 *
		 * @since 1.3.3
		 *
		 * @param int $order_id Order ID.
		 *
		 * @return void
		 */
		public function save_terms_acceptance( $order_id ) {

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( ! isset( $_POST['_woo_additional_terms'] ) ) {
				return;
			}

			$order = wc_get_order( $order_id );

			if ( ! ( $order instanceof WC_Order ) ) {
				return;
			}

			update_post_meta(
				$order_id,
				'_woo_additional_terms',
				array(
					'id'     => wc_clean( get_option( '_woo_additional_terms_page_id', null ) ),
					'notice' => wc_clean( get_option( '_woo_additional_terms_notice', '' ) ),
				)
			);
		}

		/**
		 * Display the acceptance of terms & conditions on the order edit page.
		 *
		 * @since 1.3.3
		 *
		 * @param object $order The current order object.
		 *
		 * @return void
		 */
		public function terms_acceptance( $order ) {

			$value = $order->get_meta( '_woo_additional_terms' );

			if ( ! boolval( $value ) || empty( $value ) ) {
				return;
			}

			/* incorrect CSS class added here, so it adopts styling we want */
			?>
			<div class="address">
				<p>
					<strong>
						<?php
						if ( ! is_array( $value ) ) {
							esc_html_e( 'Additional terms and conditions:', 'woo-additional-terms' );
						} else {
							echo esc_html( empty( $value['id'] ) ? $value['notice'] : wp_kses_post( get_the_title( $value['id'] ) ) );
						}
						?>
					</strong>
					<?php esc_html_e( 'Accepted', 'woo-additional-terms' ); ?>
				</p>
			</div>
			<?php
		}

		/**
		 * Filters the “Thank you” text displayed in the admin footer.
		 *
		 * @since 1.5.0
		 *
		 * @param string $text The content that will be printed.
		 *
		 * @return string
		 *
		 * @phpcs:disable WordPress.Security.NonceVerification.Recommended
		 */
		public function ask_to_rate( $text ) {

			if ( ! ( isset( $_GET['page'], $_GET['tab'] ) && 'wc-settings' === $_GET['page'] && self::SLUG === $_GET['tab'] ) ) {
				return $text;
			}

			return sprintf(
				/* translators: 1: Open paragraph tag, 2: Plugin name, 3: Five stars, 4: Close paragraph tag. */
				esc_html__( '%1$sIf you are happy with %2$s please leave us a %3$s rating to help us spread the word!%4$s', 'woo-additional-terms' ),
				'<p class="alignleft">',
				sprintf( '<strong>%s</strong>', esc_html( WOO_ADDITIONAL_TERMS_NAME ) ),
				'<a href="https://wordpress.org/support/plugin/' . esc_html( self::SLUG ) . '/reviews?rate=5#new-post" target="_blank" rel="noopener noreferrer nofollow" aria-label="' . esc_attr__( 'five star', 'woo-additional-terms' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>',
				'</p><style>#wpfooter{display:inline !important}.has-woocommerce-navigation #wpfooter{padding-left: 260px;}</style>'
			);
		}

		/**
		 * Display additional links in plugins table page.
		 * Filters the list of action links displayed for a specific plugin in the Plugins list table.
		 *
		 * @since 1.3.5
		 *
		 * @param array $links Plugin table/item action links.
		 *
		 * @return array
		 */
		public function add_action_links( $links ) {

			// Bail early, in case the PRO version of the plugin is installed.
			if ( $this->is_pro() ) {
				return $links;
			}

			$plugin_links = array();
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( esc_html_x( '%1$sGet PRO%2$s', 'plugin link', 'woo-additional-terms' ), sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer nofollow" style="color:green;font-weight:bold;">&#10003; ', esc_url( WOO_ADDITIONAL_TERMS_URI ) ), '</a>' );

			return array_merge( $plugin_links, $links );
		}

		/**
		 * Add additional helpful links to the plugin’s metadata.
		 *
		 * @since 1.3.5
		 *
		 * @param array  $links An array of the plugin’s metadata.
		 * @param string $file  Path to the plugin file relative to the plugins' directory.
		 *
		 * @return array
		 */
		public function add_meta_links( array $links, string $file ): array {

			if ( WOO_ADDITIONAL_TERMS_PLUGIN_BASENAME !== $file ) {
				return $links;
			}

			$plugin_links = array();
			/* translators: 1: Open anchor tag, 2: Close anchor tag. */
			$plugin_links[] = sprintf( esc_html_x( '%1$sCommunity support%2$s', 'plugin link', 'woo-additional-terms' ), sprintf( '<a href="https://wordpress.org/support/plugin/%s" target="_blank" rel="noopener noreferrer nofollow">', esc_html( self::SLUG ) ), '</a>' );

			if ( $this->is_woocommerce() ) {
				$settings_url = add_query_arg(
					array(
						'page' => 'wc-settings',
						'tab'  => self::SLUG,
					),
					admin_url( 'admin.php' )
				);
				/* translators: 1: Open anchor tag, 2: Close anchor tag. */
				$plugin_links[] = sprintf( esc_html_x( '%1$sSettings%2$s', 'plugin settings page', 'woo-additional-terms' ), sprintf( '<a href="%s" style="font-weight:bold;">&#9881; ', esc_url( $settings_url ) ), '</a>' );
			}

			return array_merge( $links, $plugin_links );
		}

		/**
		 * Set the activation hook for a plugin.
		 *
		 * @since 1.3.4
		 *
		 * @return void
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
			$welcome_notice = sprintf( esc_html_x( '%1$s Thanks for installing %2$s plugin! To get started, visit the %3$splugin’s settings page%4$s.', 'admin notice', 'woo-additional-terms' ), '<i class="dashicons dashicons-admin-settings"></i>', sprintf( '<strong>%s</strong>', WOO_ADDITIONAL_TERMS_NAME ), sprintf( '<a href="%s" target="_self">', esc_url( $settings_url ) ), '</a>' );

			set_transient( 'woo_additional_terms_welcome_notice', $welcome_notice, MINUTE_IN_SECONDS );
		}

		/**
		 * Set the deactivation hook for a plugin.
		 *
		 * @since 1.4.0
		 *
		 * @return void
		 */
		public function deactivation() {

			delete_transient( 'woo_additional_terms_rate' );
			delete_transient( 'woo_additional_terms_upsell' );
			delete_transient( 'woo_additional_terms_welcome_notice' );
		}

		/**
		 * Get all the settings for this plugin.
		 *
		 * @since 1.3.3
		 *
		 * @return array
		 */
		private static function get_settings() {

			$settings = array(
				'section_title' => array(
					'name' => esc_html_x( 'Terms and Conditions', 'settings section name', 'woo-additional-terms' ),
					'desc' => esc_html_x( 'This section controls the display of your additional terms and condition fieldset.', 'settings section description', 'woo-additional-terms' ),
					'type' => 'title',
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
					'name'              => esc_html_x( 'Notice content', 'settings field name', 'woo-additional-terms' ),
					'desc'              => esc_html_x( 'Text for the additional terms checkbox that customers must accept.', 'settings field description', 'woo-additional-terms' ),
					/* translators: %s: shortcode. */
					'default'           => sprintf( esc_html_x( 'I have read and agree to the website %s', 'settings field default value', 'woo-additional-terms' ), esc_html( self::SHORTCODE ) ),
					/* translators: %s: shortcode. */
					'placeholder'       => sprintf( esc_html_x( 'I have read and agree to the website %s', 'settings field placeholder', 'woo-additional-terms' ), esc_html( self::SHORTCODE ) ),
					'type'              => 'textarea',
					'css'               => 'min-width:300px;',
					'id'                => '_woo_additional_terms_notice',
					'desc_tip'          => true,
					'autoload'          => false,
					'custom_attributes' => array(
						'rows'     => '4',
						'cols'     => '50',
					),
				),
				'notice_info' => array(
					'type' => 'info',
					'text' => sprintf(
						/* translators: 1: Open paragraph tag, 2: Shortcode, 3: Close paragraph tag. */
						esc_html_x( '%1$sInclude the %2$s shortcode in the Notice, to display the page name linked to the terms page in the checkbox label.%3$s', 'settings field text', 'woo-additional-terms' ),
						'<p class="description">',
						'<code>[additional-terms]</code>',
						'</p>',
					),
				),
				'error' => array(
					'name'              => esc_html_x( 'Error message', 'settings field name', 'woo-additional-terms' ),
					'desc'              => esc_html_x( 'Display friendly notice whenever customer doesn&rsquo;t accept additional terms.', 'settings field description', 'woo-additional-terms' ),
					'default'           => esc_html_x( 'Please read and accept the additional terms and conditions to proceed with your order. ', 'settings field default value', 'woo-additional-terms' ),
					'placeholder'       => esc_html_x( 'You must accept our additional terms.', 'setting field placeholder', 'woo-additional-terms' ),
					'type'              => 'text',
					'css'               => 'min-width:300px;',
					'id'                => '_woo_additional_terms_error',
					'desc_tip'          => true,
					'autoload'          => false,
					'custom_attributes' => array(
						'required' => true,
					),
				),
				'section_end' => array(
					'type' => 'sectionend',
				),
			);

			return apply_filters( 'woo_additional_terms_settings_args', $settings );
		}

		/**
		 * Output additional terms page's content (if set).
		 * The page can be set from the plugin settings page.
		 * “WooCommerce” » “Settings” » “Additional Terms”
		 *
		 * @since 1.3.3
		 *
		 * @param int  $terms_page_id Additional terms page ID.
		 * @param bool $echo          Output additional terms page content on the page.
		 *
		 * @return void|bool
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
		 * Determine whether the pro version is active.
		 *
		 * @since 1.5.2
		 *
		 * @return bool
		 */
		private function is_pro() {

			return class_exists( 'WATPRO', false ) && defined( 'WATPRO_META' );
		}

		/**
		 * Query WooCommerce activation
		 *
		 * @since 1.0.0
		 *
		 * @return bool
		 *
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
	 * @since 1.0.0
	 *
	 * @return null|Woo_Additional_Terms The one true Woo_Additional_Terms Instance.
	 */
	function woo_additional_terms_init() {

		return Woo_Additional_Terms::instance();
	}

	woo_additional_terms_init();
endif;
