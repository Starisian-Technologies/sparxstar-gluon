<?php
/**
 * SPARXSTAR Gluon Uninstall Script
 *
 * This file is executed when the plugin is deleted through the WordPress admin.
 * Handles cleanup of plugin data based on the SPARXSTAR_GLUON_DELETE_ON_UNINSTALL setting.
 * Supports both single-site and multisite WordPress installations.
 *
 * Security: Only runs when called by WordPress uninstall process.
 *
 * @package   Starisian\Sparxstar\Gluon
 * @author    Starisian Technologies (Max Barrett) <support@starisian.com>
 * @license   MIT
 * @copyright Copyright (c) 2026 Starisian Technologies.
 * @since     1.0.0
 * @version   1.0.0
 */

// Security: Exit if not called by WordPress uninstall process
defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Define delete on uninstall setting if not already defined.
 *
 * @since 1.0.0
 */
if ( ! defined( 'SPARXSTAR_GLUON_DELETE_ON_UNINSTALL' ) ) {
	define( 'SPARXSTAR_GLUON_DELETE_ON_UNINSTALL', false );
}

// Access WordPress database
global $wpdb;

// Nothing to clean up when delete-on-uninstall is disabled.
if ( ! SPARXSTAR_GLUON_DELETE_ON_UNINSTALL ) {
	return;
}

/**
 * Handle Multisite Uninstall
 *
 * For multisite installations, loop through all sites and perform
 * cleanup on each one individually.
 */
if ( is_multisite() ) {

	/**
	 * For multisite, iterate all sites using pagination to avoid memory exhaustion
	 * on very large networks. Batch size of 100 is a safe default.
	 */
	$batch_size = 100;
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
			delete_option( 'sparxstar_gluon_settings' );
			restore_current_blog();
		}

		$offset += $batch_size;
	} while ( count( $sites ) === $batch_size );
} else {

	/**
	 * Handle Single Site Uninstall
	 *
	 * For single-site installations, perform cleanup directly.
	 */
	delete_option( 'sparxstar_gluon_settings' );
}
