<?php
/**
 * SPARXSTAR Gluon Rules and Compliance
 *
 * Manages plugin compliance with privacy regulations and WordPress standards.
 * Integrates with the WordPress Consent API to handle user consent.
 *
 * @package    Starisian\Sparxstar\Gluon\Integrations
 * @subpackage Rules
 * @since      1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 * @license    MIT License
 * @copyright  Copyright 2025-2026 Starisian Technologies.
 * @version    1.0.0
 * @link       https://developer.wordpress.org/apis/consent-api/ WordPress Consent API
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\Gluon\integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SPARXSTAR Gluon Rules and Compliance Class
 *
 * Manages plugin compliance with privacy regulations and WordPress standards.
 * Integrates with the WordPress Consent API to handle user consent for cookies
 * and data tracking in compliance with GDPR and other privacy laws.
 *
 * This class handles:
 * - Cookie registration with WordPress Consent API
 * - Consent type management (opt-in/opt-out)
 * - User consent verification
 * - Consent category customization
 * - Integration with WordPress consent mechanisms
 *
 * @package    Starisian\Sparxstar\Gluon\Integrations
 * @subpackage Rules
 * @since      1.0.0
 * @version    1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 * @link       https://developer.wordpress.org/apis/consent-api/ WordPress Consent API
 */
class SparxstarGluonRules {


	/**
	 * Singleton instance.
	 *
	 * @since 1.0.0
	 * @var SparxstarGluonRules|null
	 */
	private static ?SparxstarGluonRules $instance = null;

	/**
	 * Cookie token identifier for the plugin.
	 *
	 * Uses __Host- prefix for enhanced security (requires HTTPS, Path=/, and no Domain attribute).
	 *
	 * When renaming this scaffold, update this value to match your plugin's cookie prefix.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private const SPARXSTAR_GLUON_COOKIE_TOKEN = '__Host-SparxstarGluon-TOKEN';

	/**
	 * Get singleton instance.
	 *
	 * @since 1.0.0
	 * @return SparxstarGluonRules
	 */
	public static function getInstance(): SparxstarGluonRules {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor — enforces Singleton pattern.
	 *
	 * Sets up plugin compliance rules and registers necessary WordPress hooks
	 * for consent management. Private to prevent direct instantiation;
	 * use {@see getInstance()} to obtain the shared instance.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->gluonComplyWithRules();
		$this->gluonRegisterHooks();
	}

	/**
	 * Register WordPress hooks for rules and consent management.
	 *
	 * Sets up action and filter hooks needed for consent API integration.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function gluonRegisterHooks(): void {
		// Register hooks related to plugin rules here.
		\add_action( 'plugins_loaded', array( $this, 'gluonRegisterCookies' ) );
		// Sets the consent type (optin, optout, default false).
		\add_filter( 'wp_get_consent_type', array( $this, 'gluonSetConsentType' ), 10, 1 );
		// Modify consent categories.
		\add_filter( 'wp_get_consent_categories', array( $this, 'gluonSetConsentCategories' ), 10, 1 );
		// React to consent changes — delete token cookie when functional consent is withdrawn.
		\add_action( 'wp_set_consent', array( $this, 'gluonHandleConsentChange' ), 10, 2 );
	}

	/**
	 * Register plugin with WordPress Consent API.
	 *
	 * Declares that the plugin is registered with and complies with
	 * the WordPress Consent API standards.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonComplyWithRules(): void {
		$plugin = SPARXSTAR_GLUON_NAME;
		\add_filter( "wp_consent_api_registered_{$plugin}", '__return_true' );
	}

	/**
	 * Register cookies with the WordPress Consent API.
	 *
	 * Registers the plugin token cookie so it can be shown to users on the front-end
	 * via wp_get_cookie_info(). This is required for GDPR compliance.
	 *
	 * Cookie is registered with:
	 * - Token: {@see SPARXSTAR_GLUON_COOKIE_TOKEN}
	 * - Purpose: Session management / functional
	 * - Type: functional (required for site operation)
	 *
	 * @since 1.0.0
	 * @see   https://developer.wordpress.org/apis/consent-api/#register-cookies
	 * @return void
	 */
	public function gluonRegisterCookies(): void {
		if ( \function_exists( 'wp_add_cookie_info' ) ) {
			\wp_add_cookie_info(
				self::SPARXSTAR_GLUON_COOKIE_TOKEN,
				'SparxstarGluon',
				'functional',
				__( 'Session', 'sparxstar-gluon' ),
				__( 'Stores a unique session token.', 'sparxstar-gluon' ),
				false,
				false,
				false
			);
		}
	}

	/**
	 * Unregister plugin cookies from the Consent API.
	 *
	 * Removes the registered cookie information when needed.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonUnregisterCookies(): void {
		if ( \function_exists( 'wp_remove_cookie_info' ) ) {
			\wp_remove_cookie_info( self::SPARXSTAR_GLUON_COOKIE_TOKEN );
		}
	}

	/**
	 * Set the plugin token cookie, gated on functional consent.
	 *
	 * Only writes the cookie when the user has granted functional consent via the
	 * WordPress Consent API. If consent has not been given the request is silently
	 * declined and the caller must handle the degraded state.
	 *
	 * The cookie uses the __Host- prefix which requires HTTPS, path="/", no Domain
	 * attribute, and Secure flag — all enforced below.
	 *
	 * @since  1.0.0
	 * @param  string $token_value The token value to store in the cookie.
	 * @param  int    $expires     Unix timestamp for cookie expiry. 0 = session cookie.
	 * @return bool   True when the cookie was written, false when consent was denied
	 *                or headers have already been sent.
	 */
	public function gluonSetTokenCookie( string $token_value, int $expires = 0 ): bool {
		if ( ! $this->gluonIsConsentCategory( 'functional' ) ) {
			return false;
		}
		if ( headers_sent() ) {
			return false;
		}
		return setcookie(
			self::SPARXSTAR_GLUON_COOKIE_TOKEN,
			$token_value,
			array(
				'expires'  => $expires,
				'path'     => '/',
				'secure'   => true,
				'httponly' => true,
				'samesite' => 'Strict',
			)
		);
	}

	/**
	 * Delete the plugin token cookie.
	 *
	 * Expires the cookie immediately and removes it from the current request's
	 * $_COOKIE superglobal so callers see the change without a page reload.
	 * Safe to call even when the cookie is not present.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonDeleteTokenCookie(): void {
		if ( ! isset( $_COOKIE[ self::SPARXSTAR_GLUON_COOKIE_TOKEN ] ) ) {
			return;
		}
		if ( ! headers_sent() ) {
			setcookie(
				self::SPARXSTAR_GLUON_COOKIE_TOKEN,
				'',
				array(
					'expires'  => time() - HOUR_IN_SECONDS,
					'path'     => '/',
					'secure'   => true,
					'httponly' => true,
					'samesite' => 'Strict',
				)
			);
		}
		unset( $_COOKIE[ self::SPARXSTAR_GLUON_COOKIE_TOKEN ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- key lookup only, no value used
	}

	/**
	 * React to WordPress Consent API consent changes.
	 *
	 * Fires on the `wp_set_consent` action. When functional consent is denied or
	 * withdrawn the token cookie is immediately deleted so no functional cookie
	 * persists without the user's agreement.
	 *
	 * @since 1.0.0
	 * @param string $category The consent category that changed (e.g. 'functional').
	 * @param string $value    The new consent value ('allow' | 'deny').
	 * @return void
	 */
	public function gluonHandleConsentChange( string $category, string $value ): void {
		if ( 'functional' === $category && 'allow' !== $value ) {
			$this->gluonDeleteTokenCookie();
		}
	}

	/**
	 * Check if user has given consent for a specific category.
	 *
	 * Verifies whether the current user has granted consent for a specific
	 * purpose category (functional, marketing, statistics, etc.).
	 *
	 * @since 1.0.0
	 * @param string $category Optional. The consent category to check. Default 'functional'.
	 *                         Valid values: 'functional', 'preferences', 'statistics',
	 *                         'statistics-anonymous', 'marketing'.
	 * @return bool True if user has consented, false otherwise.
	 */
	public function gluonIsConsentCategory( string $category = 'functional' ): bool {
		// check if user has given marketing consent. Possible consent categories/purposes:
		// functional, preferences', statistics', statistics-anonymous', statistics', marketing',
		$consentCats = array( 'functional', 'preferences', 'statistics', 'statistics-anonymous', 'marketing' );
		if ( ! in_array( $category, $consentCats, true ) ) {
			return false;
		}
		if ( ! \function_exists( 'wp_has_consent' ) ) {
			return false;
		}
		return \wp_has_consent( $category );
	}

	/**
	 * Get the current consent type.
	 *
	 * Returns the site's consent type (opt-in or opt-out).
	 * Falls back to false if function doesn't exist.
	 *
	 * @since 1.0.0
	 * @return string|false The consent type ('optin' or 'optout'), or false if not set.
	 */
	public function gluonGetUserConsent(): string|false {
		if ( function_exists( 'wp_get_consent_type' ) ) {
			return \wp_get_consent_type();
		}
		return false;
	}

	/**
	 * Filter callback to provide a default consent type.
	 *
	 * Only sets the consent type to 'optin' when no site-wide type has been
	 * configured yet. If another plugin or the site owner has already set a
	 * consent type, that value is preserved unchanged — this plugin must not
	 * override the global consent mechanism.
	 *
	 * @since 1.0.0
	 * @param string $type The current consent type set by the site or another plugin.
	 * @return string The existing consent type, or 'optin' when none is set.
	 */
	public function gluonSetConsentType( string $type ): string {
		return $type ?: 'optin';
	}

	/**
	 * Filter callback to modify consent categories.
	 *
	 * Allows customization of which consent categories are available.
	 * Currently removes the 'preferences' category.
	 *
	 * @since 1.0.0
	 * @param array<string, mixed> $consentcategories The available consent categories.
	 * @return array<string, mixed> Modified consent categories.
	 */
	public function gluonSetConsentCategories( array $consentcategories ): array {
		unset( $consentcategories['preferences'] );
		return $consentcategories;
	}
}
