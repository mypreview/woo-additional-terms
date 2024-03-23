<?php
/**
 * The plugin file path class.
 *
 * @since 1.0.0
 *
 * @package woo-additional-terms
 */

namespace Woo_Additional_Terms;

/**
 * Class File.
 */
class File {

	/**
	 * The plugin file path.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file The plugin file.
	 *
	 * @return void
	 */
	public function __construct( $file ) {

		$this->file = $file;
	}

	/**
	 * Return the plugin file.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_file() {

		return $this->file;
	}

	/**
	 * Return the plugin base name.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function plugin_basename() {

		return plugin_basename( $this->file );
	}

	/**
	 * Return the plugin path.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path The path to the file.
	 *
	 * @return string
	 */
	public function plugin_path( $path = '' ) {

		return path_join( plugin_dir_path( $this->file ), $path );
	}

	/**
	 * Return the plugin url.
	 *
	 * @since 1.0.0
	 *
	 * @param string $path The path to the file.
	 *
	 * @return string
	 */
	public function plugin_url( $path = '' ) {

		return plugins_url( $path, $this->file );
	}

	/**
	 * Return plugin dirname.
	 *
	 * @since 1.6.0
	 *
	 * @return string
	 */
	public function dirname() {

		return dirname( plugin_basename( $this->file ) );
	}

	/**
	 * Returns a full path for the asset (static resource CSS/JS) file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $file_name Asset file name (filename).
	 *
	 * @return string
	 */
	public function asset_path( $file_name ) {

		$min       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : trailingslashit( 'minified' );
		$directory = pathinfo( $file_name, PATHINFO_EXTENSION );

		return woo_additional_terms()->service( 'file' )->plugin_url( "assets/{$directory}/{$min}{$file_name}" );
	}
}
