<?php
/**
 * SPARXSTAR Gluon Logger
 *
 * Provides logging and debugging functionality for the plugin. Outputs log messages
 * to the WordPress debug log when WP_DEBUG is enabled.
 *
 * @package    Starisian\Sparxstar\Gluon\Helpers\Loggers
 * @subpackage Utilities
 * @since      1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 * @license    MIT License
 * @copyright  Copyright 2025-2026 Starisian Technologies.
 * @version    1.0.0
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\Gluon\helpers\loggers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SPARXSTAR Gluon Logger Class
 *
 * Provides logging and debugging functionality for the plugin. Outputs log messages
 * to the WordPress debug log when WP_DEBUG is enabled. Also provides functionality
 * for displaying admin notices in the WordPress dashboard.
 *
 * This is a static utility class - all methods are static and no instantiation is needed.
 *
 * Features:
 * - General logging (log method)
 * - Debug-specific logging (debug method)
 * - Admin notice display (gluonAdminNotice method)
 * - Environment-aware logging (respects WP_DEBUG and production environment)
 *
 * @package    Starisian\Sparxstar\Gluon\Helpers\Loggers
 * @subpackage Utilities
 * @since      1.0.0
 * @version    1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 */
class SparxstarGluonLogger {


	/**
	 * Plugin name for logging context.
	 *
	 * Used as a prefix in all log messages to identify the source.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private static string $pluginName = SPARXSTAR_GLUON_NAME;

	/**
	 * Constructor - Logs initialization message.
	 *
	 * Note: This class is primarily used statically, but can be instantiated
	 * if needed. Logs an initialization message when constructed.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		error_log( '[' . self::$pluginName . '] SparxstarGluonLogger initialized.' );
	}

	/**
	 * Log general messages if WP_DEBUG is enabled.
	 *
	 * Writes messages to the WordPress debug log (typically wp-content/debug.log).
	 * Only logs when WP_DEBUG is enabled or not in production environment.
	 *
	 * @since 1.0.0
	 * @param string $message The message to log.
	 * @return void
	 */
	public static function log( string $message ): void {
		if ( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( \function_exists( 'wp_get_environment_type' ) && \wp_get_environment_type() !== 'production' ) ) {
			error_log( '[' . self::$pluginName . '] ' . $message );
		}
	}

	/**
	 * Log debug messages if WP_DEBUG is enabled.
	 *
	 * Similar to log() but prefixes messages with [DEBUG] for easier filtering.
	 * Only logs when WP_DEBUG constant is defined and set to true.
	 *
	 * @since 1.0.0
	 * @param string $message The debug message to log.
	 * @return void
	 */
	public static function debug( string $message ): void {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( '[' . self::$pluginName . '][DEBUG] ' . $message );
		}
	}

	/**
	 * Display a notification in wp-admin for errors and notices.
	 *
	 * Creates an admin notice that will be displayed at the top of admin pages.
	 * Useful for alerting administrators about important plugin events or errors.
	 *
	 * @since 1.0.0
	 * @param string $message The message to display in the admin notice.
	 * @param string $type    Optional. The type of notice. Accepts 'error' or 'success'. Default 'error'.
	 * @return void
	 */
	public static function gluonAdminNotice( string $message, string $type = 'error' ): void {
		// Display an admin notice in the WordPress dashboard
		$message = '[' . self::$pluginName . '] ' . trim( $message );
		\add_action(
			'admin_notices',
			function () use ( $message, $type ) {
				printf(
					'<div class="%1$s"><p>%2$s</p></div>',
					\esc_attr( 'error' === $type ? 'notice notice-error' : 'notice notice-success' ),
					\esc_html( $message )
				);
			}
		);
	}
}
