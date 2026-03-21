<?php
/**
 * SPARXSTAR Gluon Core
 *
 * Handles the core functionality for the plugin. Serves as the central
 * point for core plugin operations and business logic.
 *
 * @package    Starisian\Sparxstar\Gluon\Core
 * @subpackage Core
 * @since      1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 * @license    MIT License
 * @copyright  Copyright 2025-2026 Starisian Technologies.
 * @version    1.0.0
 */

declare(strict_types=1);

// In: src/core/PluginCore.php

namespace Starisian\Sparxstar\Gluon\core;

use Starisian\Sparxstar\Gluon\integrations\SparxstarGluonRules;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * SPARXSTAR Gluon Core Class
 *
 * Handles the core functionality for the plugin. This class serves as the central
 * point for core plugin operations and business logic. Implements the Singleton pattern
 * to ensure a single instance manages core functionality.
 *
 * This class is responsible for:
 * - Core plugin initialization
 * - Managing plugin rules and integrations
 * - Core business logic execution
 *
 * @package    Starisian\Sparxstar\Gluon\Core
 * @subpackage Core
 * @since      1.0.0
 * @version    1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 * @final
 */
final class SparxstarGluonCore {


	/**
	 * Singleton instance of the core.
	 *
	 * @since 1.0.0
	 * @var SparxstarGluonCore|null
	 */
	private static ?SparxstarGluonCore $instance = null;

	/**
	 * Get singleton instance of the core class.
	 *
	 * Returns the singleton instance, creating it if it doesn't exist.
	 * Implements the Singleton pattern for core functionality.
	 *
	 * @since 1.0.0
	 * @return SparxstarGluonCore The singleton instance.
	 */
	public static function getInstance(): SparxstarGluonCore {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Private constructor to prevent direct instantiation.
	 *
	 * Initializes core plugin functionality and checks for required dependencies.
	 * Private to enforce Singleton pattern.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Register core plugin hooks, rules, or services here.
		if ( \class_exists( SparxstarGluonRules::class ) ) {
			// Rules are instantiated via the Orchestrator
			SparxstarGluonRules::getInstance();
		}
	}
}
