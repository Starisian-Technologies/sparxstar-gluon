<?php
/**
 * PSR-4 Compatible Autoloader
 *
 * Provides automatic class loading following the PSR-4 autoloading standard.
 * Enables object-oriented plugin development without requiring Composer in production.
 *
 * @package    Starisian\Sparxstar\Gluon\Includes
 * @subpackage Core
 * @since      1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 * @license    MIT License
 * @copyright  Copyright 2025-2026 Starisian Technologies.
 * @version    1.0.0
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\Gluon\includes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PSR-4 Compatible Autoloader Class
 *
 * Provides automatic class loading following the PSR-4 autoloading standard.
 * This autoloader enables object-oriented plugin development without requiring Composer.
 *
 * The autoloader expects:
 * - Classes to be within the namespace defined by SPARXSTAR_GLUON_NAMESPACE constant
 * - Class files to be located in the /src/ directory
 * - File structure to match the namespace structure
 * - One class per file, with the filename matching the class name
 *
 * Example:
 * - Namespace: Starisian\Sparxstar\Gluon\core
 * - Class: SparxstarGluonCore
 * - File: /src/core/SparxstarGluonCore.php
 *
 * @package    Starisian\Sparxstar\Gluon\Includes
 * @subpackage Core
 * @since      1.0.0
 * @version    1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 */
class Autoloader {


	/**
	 * Register this autoloader with SPL.
	 *
	 * Registers the loadClass method as an autoloader with PHP's SPL autoload stack.
	 * Call this method once during plugin initialization to enable automatic class loading.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function register(): void {
		spl_autoload_register( array( __CLASS__, 'loadClass' ) );
	}

	/**
	 * Unregister this autoloader.
	 *
	 * Removes the autoloader from the SPL autoload stack.
	 * Useful for cleanup or testing purposes.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function unregister(): void {
		spl_autoload_unregister( array( __CLASS__, 'loadClass' ) );
	}

	/**
	 * PSR-4 compliant autoload implementation.
	 *
	 * Automatically loads class files based on the PSR-4 standard.
	 * Converts namespace to directory path and attempts to load the file.
	 *
	 * Process:
	 * 1. Verifies required constants are defined
	 * 2. Checks if class belongs to plugin namespace
	 * 3. Converts namespace to file path
	 * 4. Attempts to load the file
	 * 5. Logs errors in debug mode
	 *
	 * @since 1.0.0
	 * @param string $className Fully qualified class name to load.
	 * @return void
	 */
	public static function loadClass( string $className ): void {
		// Ensure required constants are defined
		if ( ! defined( 'SPARXSTAR_GLUON_NAMESPACE' ) || ! defined( 'SPARXSTAR_GLUON_PLUGIN_PATH' ) ) {
			if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || \wp_get_environment_type() !== 'production' ) {
				error_log( 'Autoloader error: SPARXSTAR_GLUON_NAMESPACE or SPARXSTAR_GLUON_PLUGIN_PATH is not defined.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
			return;
		}

		$baseNamespace = SPARXSTAR_GLUON_NAMESPACE;
		$baseDir       = SPARXSTAR_GLUON_PLUGIN_PATH . 'src/';

		$len = strlen( $baseNamespace );
		if ( strncmp( $className, $baseNamespace, $len ) !== 0 ) {
			return;
		}

		$relativeClass = substr( $className, $len );
		$file          = $baseDir . str_replace( '\\', '/', $relativeClass ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		} elseif ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || \wp_get_environment_type() !== 'production' ) {
			error_log( "Autoloader: Class file not found for {$className} at {$file}" ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		}
	}
}
