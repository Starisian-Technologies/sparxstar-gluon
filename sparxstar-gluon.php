<?php

namespace Starisian\Sparxstar;

use function PHPUnit\Framework\assertFalse;
/**
 * Copyright (c) 2026 Starisian Technologies (Max Barrett)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
/**
 * Sparxstar Gluon
 *
 * A WordPress plugin scaffold for strategic value and AI-driven functionality. Use as a template for building robust plugins.
 *
 * @package           Starisian\Sparxstar
 * @author            Starisian Technologies (Max Barrett) <support@starisian.com>
 * @license           MIT License
 * @copyright         Copyright 2025-2026 Starisian Technologies.
 * @version           1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:        SPARXSTAR GLUON
 * Plugin URI:        https://starisian.com/sparxstar/sparxstar-gluon
 * Description:       A WordPress plugin scaffold for strategic value and AI-driven functionality. Use as a template for building robust plugins.
 * Version:           1.0.0
 * Author:            Starisian Technologies
 * Author URI:        https://www.starisian.com/
 * Contributor:       Max Barrett
 * License:           MIT License
 * License URI:       https://github.com/starisian-technologies/sparxstar-gluon/blob/main/LICENSE.md
 * Text Domain:       sparxstar-gluon
 * Requires at least: 6.8
 * Requires PHP:      8.2
 * Tested up to:      6.9
 * Domain Path:       /languages
 * Tags:              strategic, privacy, starter, template, sparxstar, WordPress, plugin, multisite, ai, artificial intelligence, gluon
 * GitHub Plugin URI: https://github.com/starisian-technologies/sparxstar-gluon
 * Requires Plugins:  abilities-api, mcp-adapter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin absolute path constant.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'GLUON_PLUGIN_PATH' ) ) {
	define( 'GLUON_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

/**
 * Plugin URL constant.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'GLUON_PLUGIN_URL' ) ) {
	define( 'GLUON_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Plugin version constant.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'GLUON_PLUGIN_VERSION' ) ) {
	define( 'GLUON_PLUGIN_VERSION', '1.0.0' );
}

/**
 * Plugin namespace for autoloading.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'GLUON_PLUGIN_NAMESPACE' ) ) {
	define( 'GLUON_PLUGIN_NAMESPACE', 'Starisian\\Sparxsatar\\Gluon\\' );
}

/**
 * Plugin display name.
 *
 * @since 1.0.0
 * @var string
 */
if ( ! defined( 'GLUON_PLUGIN_NAME' ) ) {
	define( 'GLUON_PLUGIN_NAME', 'SPARXSTAR-Gluon' );
}

/**
 * Delete plugin data on uninstall.
 *
 * Set to true to remove all plugin options and data when uninstalled.
 *
 * @since 1.0.0
 * @var bool
 */
if ( ! defined( 'GLUON_PLUGIN_DELETE_ON_UNINSTALL' ) ) {
	define( 'GLUON_PLUGIN_DELETE_ON_UNINSTALL', false );
}

use Starisian\src\includes\Autoloader;

if ( dirname( GLUON_PLUGIN_PATH ) && file_exists( GLUON_PLUGIN_PATH . 'src/includes/Autoloader.php' ) ) {
	require_once GLUON_PLUGIN_PATH . 'src/includes/Autoloader.php';
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
 * @package    Starisian\Sparxstar
 * @subpackage Bootstrap
 * @since      1.0.0
 * @final
 */
final class Sparxstar_Gluon {


	/**
	 * Constructor - Initialize plugin checks and hooks.
	 *
	 * Checks WordPress and PHP version compatibility before initializing
	 * the plugin. If compatible, registers necessary hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Check compatibility.
		if ( ! $this->gluonIsPluginCompatible() ) {
			// If not compatible, return.
			return;
		}
		// register the run method on plugins_loaded.
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
		// Register hooks here
		// Run the plugin on plugins_loaded
		add_action( 'plugins_loaded', array( 'Starisian\Sparxstar\Gluon\SparxstarGluonOrchestrator', 'gluonRun' ) );
	}

	/**
	 * Display admin notice for compatibility issues.
	 *
	 * Shows an error notice in the WordPress admin when the plugin
	 * requirements (PHP/WordPress versions) are not met.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function gluonAdminNotice( string $message ): void {
		// Load logger class
		$logger = GLUON_PLUGIN_PATH . 'src/helpers/loggers/SparxstarGluonLogger.php';
		// Include logger if exists
		if ( file_exists( $logger ) ) {
			require_once $logger;
		}
		// Log admin notice
		$logger::gluonAdminNotice( __( $message, 'sparxstar-gluon' ), 'error' );
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
		// Define minimum requirements
		// Adjust as needed
		// PHP minimum version
		$min_php = 8.2;
		// WordPress minimum version
		$min_wp = 6.8;
		// get PHP version
		$php_version = PHP_VERSION;
		// get WordPress version
		$wp_version = get_bloginfo( 'version' );
		// If either version is less than minimum
		if (
			( version_compare( $php_version, $min_php, '<' ) === true )
			|| ( version_compare( $wp_version, $min_wp, '<' ) === true )
		) {
			// Show admin notice
			$this->gluonAdminNotice( __( 'Plugin requires PHP 8.2+ and WordPress 6.8+. Please update your environment.', 'sparxstar-gluon' ) );
			// Return false for incompatible
			return false;
		}
		if ( ! class_exists( 'WP_Ability' ) ) {
			// E.g. add an admin notice about the missing dependency.
			$this->gluonAdminNotice( __( 'Plugin requires the Abilities API plugin to be installed and activated.', 'sparxstar-gluon' ) );
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
		// Return if not activation hook
		if ( current_action() !== 'activate_' . plugin_basename( __FILE__ ) ) {
			return;
		}
		// Return if user lacks permissions
		if ( current_user_can( 'activate_plugins' ) === false ) {
			return;
		}
		global $wpdb;
		// Activation tasks
		// if multi-site, activate for all sites.
		if ( is_multisite() && $network_wide === true ) {
			// Get all site IDs
			$site_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
			// Loop through each site and activate
			foreach ( $site_ids as $site_id ) {
				// Switch to the site
				switch_to_blog( (int) $site_id );
				// Call site-specific activation
				self::gluonActivateSite();
				// Restore to the current site
				restore_current_blog();
			}
			// Single site activation
		} else {
			// Call site-specific activation
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
	public function gluonDeactivate( bool $network_wide ): void {
		// Return if not deactivation hook
		if ( current_action() !== 'deactivate_' . plugin_basename( __FILE__ ) ) {
			return;
		}
		// Return if user lacks permissions
		if ( current_user_can( 'activate_plugins' ) === false ) {
			return;
		}
		global $wpdb;
		// Deactivation tasks
		// if multi-site, deactivate for all sites.
		if ( is_multisite() && $network_wide === true ) {
			// Get all site IDs
			$site_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
			// Loop through each site and deactivate
			foreach ( $site_ids as $site_id ) {
				// Switch to the site
				switch_to_blog( (int) $site_id );
				// Call site-specific deactivation
				self::gluonDeactivateSite();
				// Restore to the current site
				restore_current_blog();
			}
			// Single site deactivation
		} else {
			// Call site-specific deactivation
			self::gluonDeactivateSite();
		}
	}

	/**
	 * Perform activation tasks for a single site.
	 *
	 * Sets up default options and flushes rewrite rules for the site.
	 * Called during both single-site and multisite activation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonActivateSite(): void {
		// Activation tasks per site
		add_option( 'gluon_settings', array() );
		// Add other default settings or database tables as needed
		flush_rewrite_rules();
	}

	/**
	 * Perform deactivation tasks for a single site.
	 *
	 * Flushes rewrite rules. Data is typically retained during deactivation.
	 * Called during both single-site and multisite deactivation.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonDeactivateSite(): void {
		// Deactivation tasks per site
		// Usually nothing removed here
		flush_rewrite_rules();
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
	public function gluonUninstall(): void {
		// Uninstallation tasks
		if ( ( WP_UNINSTALL_PLUGIN !== __FILE__ ) || ( current_user_can( 'delete_plugins' ) === false ) ) {
			// if not called from uninstall.php, return
			return;
		}
		// Uninstall tasks script here or load from uninstall.php
		$uninstall = GLUON_PLUGIN_PATH . 'uninstall.php';
		if ( file_exists( $uninstall ) ) {
			// Include uninstall script to remove all data
			require_once $uninstall;
		}
	}
}
// Hooks and initialization
register_activation_hook( __FILE__, array(__NAMESPACE__ . '\\Sparxstar_Gluon', 'gluonActivate' ));
register_deactivation_hook( __FILE__, array(__NAMESPACE__ . '\\Sparxstar_Gluon', 'gluonDeactivate' ));
register_uninstall_hook( __FILE__, array(__NAMESPACE__ . '\\Sparxstar_Gluon', 'gluonUnintstall' ));

/**
 * Initialize the plugin.
 *
 * Instantiates the main plugin bootstrap class to start the plugin.
 * This function is called immediately to begin plugin execution.
 *
 * @since 1.0.0
 * @return void
 */
function sparxstar_gluon_init(): void {
	// Instantiate the main plugin class
	new Sparxstar_Gluon();
}
// Run the initialization function
sparxstar_gluon_init();
