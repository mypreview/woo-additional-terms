<?php
/**
 * Abstract Migration class.
 *
 * @since 1.6.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms\Migration;

/**
 * Migration class.
 */
abstract class Migration {

	/**
	 * The version.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	private $version;

	/**
	 * The option name.
	 *
	 * @since 1.6.0
	 *
	 * @var string
	 */
	private $option_name;

	/**
	 * Constructor.
	 *
	 * @since 1.6.0
	 *
	 * @param string $version The version to do the migration.
	 *
	 * @return void
	 */
	public function __construct( $version ) {

		// Set the migration properties.
		$this->set_version( $version );
		$this->set_option_name();

		// Initialize the migration.
		$this->setup();
	}

	/**
	 * Setup hooks and filters.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	public function setup() {

		add_action( 'woocommerce_init', array( $this, 'migrate_callback' ) );
	}

	/**
	 * Migrate callback.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	abstract public function migrate_callback();

	/**
	 * Check if the migration is eligible.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	protected function is_eligible() {

		// Bail early if the migration is done.
		if ( $this->is_completed() ) {
			return false;
		}

		// Bail early if the version is not set.
		if ( empty( $this->version ) ) {
			return false;
		}

		// Bail early if the version requirement is not met.
		if ( version_compare( $this->version, woo_additional_terms()->get_version(), '<>' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Mark the migration as done.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	protected function complete() {

		update_option( $this->option_name, true );
	}

	/**
	 * Check if the migration is completed.
	 *
	 * @since 1.6.0
	 *
	 * @return bool
	 */
	private function is_completed() {

		return (bool) get_option( $this->option_name, false );
	}

	/**
	 * Set the version.
	 *
	 * @since 1.6.0
	 *
	 * @param string $version The version.
	 *
	 * @return void
	 */
	private function set_version( $version ) {

		$this->version = $version;
	}

	/**
	 * Set the option name.
	 *
	 * @since 1.6.0
	 *
	 * @return void
	 */
	private function set_option_name() {

		$this->option_name = "_woo_additional_terms_migration_{$this->version}";
	}
}
