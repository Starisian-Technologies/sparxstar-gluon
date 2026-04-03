<?php
/**
 * SPARXSTAR Gluon
 *
 * A WordPress plugin scaffold for strategic value and AI-driven functionality.
 * Use as a template for building robust, production-grade plugins.
 *
 * When using this scaffold, replace all occurrences of "Gluon", "gluon",
 * "GLUON", and "sparxstar-gluon" with your plugin's name. See README.md for
 * a full renaming checklist.
 *
 * @package           Starisian\Sparxstar\Gluon
 * @author            Starisian Technologies (Max Barrett) <support@starisian.com>
 * @license           MIT License
 * @copyright         Copyright 2025-2026 Starisian Technologies.
 * @version           1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       SPARXSTAR GLUON
 * Plugin URI:        https://starisian.com/sparxstar/sparxstar-gluon
 * Description:       A WordPress plugin scaffold for strategic value and AI-driven functionality. Use as a template for building robust plugins.
 * Version:           1.0.0
 * Author:            Starisian Technologies
 * Author URI:        https://www.starisian.com/
 * Contributor:       Max Barrett
 * License:           MIT License
 * License URI:       https://github.com/Starisian-Technologies/sparxstar-gluon/blob/main/LICENSE.md
 * Text Domain:       sparxstar-gluon
 * Requires at least: 6.8
 * Requires PHP:      8.2
 * Tested up to:      6.9
 * Domain Path:       /languages
 * Tags:              strategic, privacy, starter, template, sparxstar, WordPress, plugin, multisite, ai, artificial intelligence, gluon
 * GitHub Plugin URI: https://github.com/Starisian-Technologies/sparxstar-gluon
 * Requires Plugins:  abilities-api, mcp-adapter
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\Gluon;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin absolute path constant.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'SPARXSTAR_GLUON_PLUGIN_PATH' ) ) {
	define( 'SPARXSTAR_GLUON_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * Plugin URL constant.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'SPARXSTAR_GLUON_PLUGIN_URL' ) ) {
	define( 'SPARXSTAR_GLUON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Plugin version constant.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'SPARXSTAR_GLUON_VERSION' ) ) {
	define( 'SPARXSTAR_GLUON_VERSION', '1.0.0' );
}

/**
 * Plugin namespace for autoloading.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'SPARXSTAR_GLUON_NAMESPACE' ) ) {
	define( 'SPARXSTAR_GLUON_NAMESPACE', 'Starisian\\Sparxstar\\Gluon\\' );
}

/**
 * Plugin display name.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'SPARXSTAR_GLUON_NAME' ) ) {
	define( 'SPARXSTAR_GLUON_NAME', 'SPARXSTAR-Gluon' );
}

/**
 * Plugin main file path constant.
 *
 * Used for textdomain loading and other file-relative operations.
 * Update when renaming this scaffold.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'SPARXSTAR_GLUON_PLUGIN_FILE' ) ) {
	define( 'SPARXSTAR_GLUON_PLUGIN_FILE', __FILE__ );
}

/**
 * Delete plugin data on uninstall.
 *
 * Set to true to remove all plugin options and data when uninstalled.
 *
 * @since 1.0.0
 * @var bool
 */
if ( ! defined( 'SPARXSTAR_GLUON_DELETE_ON_UNINSTALL' ) ) {
	define( 'SPARXSTAR_GLUON_DELETE_ON_UNINSTALL', false );
}

use Starisian\Sparxstar\Gluon\includes\Autoloader;

if ( file_exists( SPARXSTAR_GLUON_PLUGIN_PATH . 'src/includes/Autoloader.php' ) ) {
	require_once SPARXSTAR_GLUON_PLUGIN_PATH . 'src/includes/Autoloader.php';
	Autoloader::register();
} else {
	add_action(
		'admin_notices',
		function (): void {
			echo '<div class="error"><p>' . esc_html__( 'Critical file Autoloader.php is missing.', 'sparxstar-gluon' ) . '</p></div>';
		}
	);
	return;
}

/**
 * Main Plugin Bootstrap Class.
 *
 * Handles plugin initialization, compatibility checks, and activation/deactivation hooks.
 * This is the entry point for the plugin and manages the plugin lifecycle.
 *
 * @package    Starisian\Sparxstar\Gluon
 * @subpackage Bootstrap
 * @since      1.0.0
 * @final
 */
final class SparxstarGluon {


	/**
	 * Number of sites to process per batch during network-wide activation/deactivation.
	 *
	 * Pagination batch size used to iterate all sites on large Multisite networks
	 * without exhausting PHP memory.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private const MULTISITE_BATCH_SIZE = 100;

	/**
	 * Constructor - Initialize plugin checks and hooks.
	 *
	 * Checks WordPress and PHP version compatibility before initializing
	 * the plugin. If compatible, registers necessary hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		if ( ! $this->gluonIsPluginCompatible() ) {
			return;
		}
		$this->gluonRegisterHooks();
	}

	/**
	 * Register WordPress action and filter hooks.
	 *
	 * Sets up the main plugin initialization hook that will run
	 * on WordPress 'plugins_loaded' action.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonRegisterHooks(): void {
		add_action( 'plugins_loaded', array( SparxstarGluonOrchestrator::class, 'gluonRun' ) );
	}

	/**
	 * Display admin notice for compatibility issues.
	 *
	 * Shows an error notice in the WordPress admin when the plugin
	 * requirements (PHP/WordPress versions) are not met.
	 *
	 * @since 1.0.0
	 * @param string $message The message to display.
	 * @return void
	 */
	private function gluonAdminNotice( string $message ): void {
		$logger_file = SPARXSTAR_GLUON_PLUGIN_PATH . 'src/helpers/loggers/SparxstarGluonLogger.php';
		if ( file_exists( $logger_file ) ) {
			require_once $logger_file;
			\Starisian\Sparxstar\Gluon\helpers\loggers\SparxstarGluonLogger::gluonAdminNotice( $message, 'error' );
			return;
		}

		if ( function_exists( 'add_action' ) ) {
			add_action(
				'admin_notices',
				static function () use ( $message ): void {
					echo '<div class="notice notice-error"><p>' . esc_html( $message ) . '</p></div>';
				}
			);
		}

		if ( function_exists( 'error_log' ) ) {
			\error_log( '[SPARXSTAR GLUON] ' . $message ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log -- Last-resort logging when SparxstarGluonLogger is unavailable.
		}
	}

	/**
	 * Check if the plugin environment meets minimum requirements.
	 *
	 * Validates that the server is running the minimum required PHP version
	 * and that WordPress is at the minimum required version.
	 *
	 * @since 1.0.0
	 * @return bool True if compatible, false otherwise.
	 */
	private function gluonIsPluginCompatible(): bool {
		$min_php = '8.2';
		$min_wp  = '6.8';

		if ( version_compare( PHP_VERSION, $min_php, '<' ) ) {
			$this->gluonAdminNotice(
				__(
					'SPARXSTAR Gluon requires PHP 8.2+ and WordPress 6.8+. Please update your environment.',
					'sparxstar-gluon'
				)
			);
			return false;
		}

		if ( version_compare( get_bloginfo( 'version' ), $min_wp, '<' ) ) {
			$this->gluonAdminNotice(
				__(
					'SPARXSTAR Gluon requires PHP 8.2+ and WordPress 6.8+. Please update your environment.',
					'sparxstar-gluon'
				)
			);
			return false;
		}

		if ( ! class_exists( 'WP_Ability' ) ) {
			$this->gluonAdminNotice(
				__(
					'SPARXSTAR Gluon requires the Abilities API plugin to be installed and activated.',
					'sparxstar-gluon'
				)
			);
			return false;
		}

		return true;
	}

	/**
	 * Plugin activation callback.
	 *
	 * Handles plugin activation for both single and multisite installations.
	 * For multisite, activates the plugin on all sites if network-wide activation.
	 *
	 * @since 1.0.0
	 * @param bool $network_wide Whether the plugin is being network-activated.
	 * @return void
	 */
	public static function gluonActivate( bool $network_wide ): void {
		if ( current_action() !== 'activate_' . plugin_basename( __FILE__ ) ) {
			return;
		}
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( is_multisite() && $network_wide ) {
			$batch_size = self::MULTISITE_BATCH_SIZE;
			$offset     = 0;
			do {
				$sites = get_sites(
					array(
						'number' => $batch_size,
						'offset' => $offset,
						'fields' => 'ids',
					)
				);
				foreach ( $sites as $site_id ) {
					switch_to_blog( (int) $site_id );
					self::gluonActivateSite();
					restore_current_blog();
				}
				$offset += $batch_size;
			} while ( count( $sites ) === $batch_size );
		} else {
			self::gluonActivateSite();
		}
	}

	/**
	 * Plugin deactivation callback.
	 *
	 * Handles plugin deactivation for both single and multisite installations.
	 * For multisite, deactivates the plugin on all sites if network-wide deactivation.
	 *
	 * @since 1.0.0
	 * @param bool $network_wide Whether the plugin is being network-deactivated.
	 * @return void
	 */
	public static function gluonDeactivate( bool $network_wide ): void {
		if ( current_action() !== 'deactivate_' . plugin_basename( __FILE__ ) ) {
			return;
		}
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( is_multisite() && $network_wide ) {
			$batch_size = self::MULTISITE_BATCH_SIZE;
			$offset     = 0;
			do {
				$sites = get_sites(
					array(
						'number' => $batch_size,
						'offset' => $offset,
						'fields' => 'ids',
					)
				);
				foreach ( $sites as $site_id ) {
					switch_to_blog( (int) $site_id );
					self::gluonDeactivateSite();
					restore_current_blog();
				}
				$offset += $batch_size;
			} while ( count( $sites ) === $batch_size );
		} else {
			self::gluonDeactivateSite();
		}
	}

	/**
	 * Perform activation tasks for a single site.
	 *
	 * Sets up default options for the site.
	 * Called during both single-site and multisite activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function gluonActivateSite(): void {
		add_option( 'sparxstar_gluon_settings', array() );
	}

	/**
	 * Perform deactivation tasks for a single site.
	 *
	 * Data is retained during deactivation.
	 * Called during both single-site and multisite deactivation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function gluonDeactivateSite(): void {
		// No cleanup required on deactivation; data is intentionally retained.
	}

	/**
	 * Plugin uninstall callback.
	 *
	 * Performs cleanup tasks when the plugin is uninstalled.
	 * Only executes if called from uninstall.php and user has proper permissions.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function gluonUninstall(): void {
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) || ! current_user_can( 'delete_plugins' ) ) {
			return;
		}
		$uninstall = SPARXSTAR_GLUON_PLUGIN_PATH . 'uninstall.php';
		if ( file_exists( $uninstall ) ) {
			require_once $uninstall;
		}
	}
}

register_activation_hook( __FILE__, array( SparxstarGluon::class, 'gluonActivate' ) );
register_deactivation_hook( __FILE__, array( SparxstarGluon::class, 'gluonDeactivate' ) );
register_uninstall_hook( __FILE__, array( SparxstarGluon::class, 'gluonUninstall' ) );

/**
 * Initialize the plugin.
 *
 * Instantiates the main plugin bootstrap class to start the plugin.
 *
 * @since 1.0.0
 * @return void
 */
function sparxstar_gluon_init(): void {
	new SparxstarGluon();
}

sparxstar_gluon_init();
