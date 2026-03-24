<?php
/**
 * SPARXSTAR Gluon Orchestrator
 *
 * Main orchestrator class that manages plugin initialization and coordinates
 * between all plugin components. Implements the Singleton pattern.
 *
 * @package    Starisian\Sparxstar\Gluon
 * @subpackage Core
 * @since      1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 * @license    MIT License
 * @copyright  Copyright 2025-2026 Starisian Technologies.
 * @version    1.0.0
 */

declare(strict_types=1);

namespace Starisian\Sparxstar\Gluon;

use Starisian\Sparxstar\Gluon\helpers\loggers\SparxstarGluonLogger as Logger;
use Exception;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SPARXSTAR Gluon Orchestrator Class
 *
 * The main orchestrator class that manages plugin initialization, dependency loading,
 * and coordination between different plugin components. Implements the Singleton pattern
 * to ensure a single instance throughout the plugin lifecycle.
 *
 * This class is responsible for:
 * - Loading plugin dependencies (Core, Rules, etc.)
 * - Registering WordPress hooks
 * - Managing plugin assets (CSS/JS)
 * - Loading text domain for internationalization
 * - Coordinating between different plugin modules
 *
 * @package    Starisian\Sparxstar\Gluon
 * @subpackage Core
 * @since      1.0.0
 * @version    1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 * @final
 */
final class SparxstarGluonOrchestrator {


	/**
	 * Plugin version number.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const VERSION = SPARXSTAR_GLUON_VERSION;

	/**
	 * Singleton instance of the orchestrator.
	 *
	 * @since 1.0.0
	 * @var SparxstarGluonOrchestrator|null
	 */
	private static ?SparxstarGluonOrchestrator $instance = null;

	/**
	 * Plugin file path.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $pluginPath;

	/**
	 * Plugin URL.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $pluginUrl;

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $version;

	/**
	 * Plugin display name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $pluginName;

	/**
	 * Array of loaded dependencies.
	 *
	 * @since 1.0.0
	 * @var array<string, mixed>
	 */
	private array $dependencies = array();

	/**
	 * Get singleton instance of the orchestrator.
	 *
	 * Implements the Singleton pattern to ensure only one instance
	 * of the orchestrator exists throughout the plugin lifecycle.
	 *
	 * @since 1.0.0
	 * @return SparxstarGluonOrchestrator The singleton instance.
	 */
	public static function gluonGetInstance(): SparxstarGluonOrchestrator {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 *
	 * Initializes the plugin by setting up paths, loading translations,
	 * loading dependencies, registering hooks, and setting up assets.
	 * Private to enforce Singleton pattern.
	 *
	 * @since 1.0.0
	 * @internal
	 */
	private function __construct() {
		$this->pluginPath = SPARXSTAR_GLUON_PLUGIN_PATH;
		$this->pluginUrl  = SPARXSTAR_GLUON_PLUGIN_URL;
		$this->version    = SPARXSTAR_GLUON_VERSION;
		$this->pluginName = SPARXSTAR_GLUON_NAME;

		$this->gluonLoadTextdomain();
		$this->gluonLoadDependencies();
		$this->gluonRegisterHooks();
		$this->gluonRegisterAssets();
	}

	/**
	 * Register WordPress hooks for the plugin.
	 *
	 * Sets up action and filter hooks that the plugin needs to function.
	 * Called during plugin initialization.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function gluonRegisterHooks(): void {
		\add_action( 'init', array( $this, 'gluonInit' ) );
	}

	/**
	 * Initialize plugin functionality.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonInit(): void {
	}

	/**
	 * Register plugin CSS and JS assets for enqueueing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function gluonRegisterAssets(): void {
	}

	/**
	 * Initializes a single dependency by class name.
	 *
	 * @since 1.0.0
	 * @internal
	 * @param class-string $dependency Fully-qualified class name of the dependency.
	 * @return void
	 */
	private function gluonInitDependency( string $dependency ): void {
		if ( ! class_exists( $dependency ) ) {
			Logger::log( 'SPARXSTAR Gluon: Dependency class ' . $dependency . ' not found.' );
			return;
		}
		try {
			if ( method_exists( $dependency, 'gluonGetInstance' ) ) {
				$this->dependencies[ $dependency ] = $dependency::gluonGetInstance();
			} elseif ( method_exists( $dependency, 'getInstance' ) ) {
				$this->dependencies[ $dependency ] = $dependency::getInstance();
			} elseif ( method_exists( $dependency, 'getInstance' ) ) {
				$this->dependencies[ $dependency ] = $dependency::getInstance();
			} else {
				$this->dependencies[ $dependency ] = new $dependency();
			}
		} catch ( Exception $e ) {
			Logger::log( 'SPARXSTAR Gluon: Failed to instantiate ' . $dependency . ': ' . $e->getMessage() );
		}
	}

	/**
	 * Loads all plugin dependencies.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function gluonLoadDependencies(): void {
		$this->dependencies = array();
		$dependencies       = array(
			\Starisian\Sparxstar\Gluon\core\SparxstarGluonCore::class,
			\Starisian\Sparxstar\Gluon\integrations\SparxstarGluonRules::class,
		);
		foreach ( $dependencies as $dependency ) {
			$this->gluonInitDependency( $dependency );
		}
	}

	/**
	 * Loads the plugin textdomain for translations.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function gluonLoadTextdomain(): void {
		\load_plugin_textdomain(
			'sparxstar-gluon',
			false,
			dirname( \plugin_basename( SPARXSTAR_GLUON_PLUGIN_FILE ) ) . '/languages'
		);
	}

	/**
	 * Runs the plugin by retrieving or creating the singleton instance.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function gluonRun(): void {
		self::gluonGetInstance();
	}

	/**
	 * Prevent cloning of the instance.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function __clone(): void {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is not allowed.', 'sparxstar-gluon' ), self::VERSION );
	}

	/**
	 * Prevent unserializing of the instance.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function __wakeup(): void {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing is not allowed.', 'sparxstar-gluon' ), self::VERSION );
	}

	/**
	 * Prevent serialization of the instance.
	 *
	 * @since 1.0.0
	 * @return array<string, mixed>
	 */
	public function __sleep(): array {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Serialization is not allowed.', 'sparxstar-gluon' ), self::VERSION );
		return array();
	}

	/**
	 * Prevent calling undefined methods.
	 *
	 * @since 1.0.0
	 * @param string       $name      Method name.
	 * @param array<mixed> $arguments Method arguments.
	 * @return void
	 */
	public function __call( string $name, array $arguments ): void {
		_doing_it_wrong( esc_html( $name ), esc_html__( 'Calling undefined methods is not allowed.', 'sparxstar-gluon' ), self::VERSION );
	}
}
