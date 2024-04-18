<?php
/**
 * The core plugin class.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms;

/**
 * The plugin class.
 */
class Plugin extends Vendor\Pimple\Container {

	/**
	 * The plugin version.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param string $version The plugin version.
	 * @param string $file    The plugin file.
	 *
	 * @return void
	 */
	public function __construct( $version, $file ) {

		// Set the version.
		$this->version = $version;

		// Pimple Container construct.
		parent::__construct();

		// Register the file service.
		$this['file'] = fn() => new File( $file );

		// Register services early.
		$this->register_services();

		// Hooks.
		$this->hooks();

		// Load the plugin.
		$this->load();
	}

	/**
	 * Register services.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	private function register_services() {

		$provider = new PluginServiceProvider();
		$provider->register( $this );
	}

	/**
	 * Register assets.
	 *
	 * @since 1.6.5
	 *
	 * @return void
	 */
	private function hooks() {

		add_action( 'before_woocommerce_init', array( __NAMESPACE__ . '\\I18n', 'textdomain' ) );
		add_action( 'enqueue_block_editor_assets', array( __NAMESPACE__ . '\\Assets', 'enqueue_editor' ) );
		add_action( 'admin_enqueue_scripts', array( __NAMESPACE__ . '\\Assets', 'enqueue_admin' ) );
		add_action( 'wp_enqueue_scripts', array( __NAMESPACE__ . '\\Assets', 'enqueue_frontend' ) );
	}

	/**
	 * Get a service by given key.
	 *
	 * @since 1.6.0
	 *
	 * @param string $key The service key.
	 *
	 * @return mixed
	 */
	public function service( $key ) {

		return $this[ $key ];
	}

	/**
	 * Get the plugin slug.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_slug() {

		return 'woo-additional-terms';
	}

	/**
	 * Get the plugin version.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function get_version() {

		return $this->version;
	}

	/**
	 * Start loading classes on `woocommerce_loaded`, priority 20.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	private function load() {

		// Iterate through the classes and initialize them.
		foreach ( $this->get_classes() as $class => $args ) {

			// Skip if the condition is not met.
			if ( isset( $args['condition'] ) && ! $args['condition'] ) {
				continue;
			}

			// Initialize the class with parameters.
			if ( isset( $args['params'] ) ) {
				( new $class() )->setup( ...$args['params'] );
				continue;
			}

			// Initialize the class.
			( new $class() )->setup();
		}
	}

	/**
	 * Get the classes to load.
	 *
	 * @since 1.6.0
	 *
	 * @return array
	 */
	private function get_classes() {

		$is_ajax     = wp_doing_ajax();
		$is_admin    = is_admin();
		$is_frontend = ! $is_admin;
		$classes     = array(
			'Admin\\Order'                 => array(
				'condition' => $is_admin,
			),
			'Ajax\\OnBoarding'             => array(
				'condition' => $is_ajax,
			),
			'Ajax\\Rate'                   => array(
				'condition' => $is_ajax,
			),
			'Ajax\\Rated'                  => array(
				'condition' => $is_ajax,
			),
			'Compatibility\\WooCommerce'   => array(
				'condition' => $is_admin && class_exists( 'Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ),
			),
			'Enhancements\\Docs'           => array(
				'condition' => $is_admin,
			),
			'Enhancements\\Meta'           => array(
				'condition' => $is_admin,
			),
			'Enhancements\\Notices'        => array(
				'condition' => $is_admin,
			),
			'Enhancements\\OnBoarding'     => array(
				'condition' => $is_admin,
			),
			'Enhancements\\Rate'           => array(
				'condition' => $is_admin,
			),
			'Enhancements\\Upsell'         => array(
				'condition' => $is_admin,
			),
			'Migration\\Migration160'      => array(
				'condition' => $is_admin,
			),
			'Settings\\Register'           => array(
				'condition' => $is_admin,
			),
			'WooCommerce\\Checkout'        => array(
				'condition' => $is_frontend,
			),
			'WooCommerce\\Block\\Checkout' => array(),
			'WooCommerce\\Block\\Register' => array(),
		);

		return array_combine(
			array_map(
				fn ( $key ) => __NAMESPACE__ . '\\' . $key,
				array_keys( $classes )
			),
			$classes
		);
	}
}
