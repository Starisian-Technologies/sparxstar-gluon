<?php
/**
 * AI Manager for SPARXSTAR Gluon
 *
 * This file contains the SparxstarGluonAIManager class which handles
 * integration with WordPress 6.9+ Abilities API and MCP Adapter.
 *
 * @package    Starisian\Sparxstar\Gluon
 * @subpackage Includes
 * @since      1.0.0
 * @author     Starisian Technologies
 * @license    GPL-2.0-or-later
 */

declare(strict_types=1);
namespace Starisian\Sparxstar\Gluon\includes;

use WordPress\AI_Client\AI_Client;
use WP\MCP\Transport\HttpTransport;
use WP\MCP\Infrastructure\ErrorHandling\ErrorLogMcpErrorHandler;
use WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler;
use WP\MCP\Core\McpAdapter;

/**
 * Class SparxstarGluonAIManager
 *
 * Manages AI abilities registration and integration with WordPress Abilities API.
 * Provides MCP server functionality and AI-powered content operations.
 *
 * @package    Starisian\Sparxstar\Gluon\Includes
 * @since      1.0.0
 */
class SparxstarGluonAIManager {


	/**
	 * Constructor - Initializes the AI Manager and registers hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->gluonConfirmMCPAbilities();
		$this->gluonRegisterHooks();
	}

	private function gluonRegisterHooks(): void {
		// Categories must be registered on this specific hook
		\add_action( 'wp_abilities_api_categories_init', array( $this, 'gluonRegisterAbilityCategory' ) );
		// Abilities must be registered on this specific hook
		\add_action( 'wp_abilities_api_init', array( $this, 'gluonRegisterAbilities' ) );
	}

	/**
	 * Registers the ability category for grouping AI-related abilities.
	 *
	 * Creates a custom category 'ai-content-tools' in the WordPress Abilities API
	 * to organize and group related AI functionality.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonRegisterAbilityCategory(): void {
		\wp_register_ability_category(
			'ai-content-tools',
			array(
				'label'       => \__( 'AI Content Tools', 'sparxstar-gluon' ),
				'description' => \__( 'AI-powered tools for content manipulation and analysis.', 'sparxstar-gluon' ),
			)
		);
	}

	/**
	 * Confirms and validates MCP and Abilities API dependencies.
	 *
	 * Checks if the WordPress Abilities API and MCP Adapter are available.
	 * Displays admin notices if required or recommended dependencies are missing.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonConfirmMCPAbilities(): void {
		// B. Abilities API (The Nervous System)
		if ( ! \function_exists( 'wp_register_ability' ) ) {
			\add_action(
				'admin_notices',
				function () {
					echo '<div class="error"><p><strong>SPARXSTAR Gluon Recommendation:</strong> <a href="https://github.com/WordPress/abilities-api" target="_blank">Abilities API</a> required for actions.</p></div>';
				}
			);
			return;
		}

		// C. MCP Adapter (The Connectivity Layer) - Optional but Recommended
		if ( ! \class_exists( '\WP\MCP\Core\McpAdapter' ) ) {
			\add_action(
				'admin_notices',
				function () {
					echo '<div class="notice notice-info"><p><strong>SPARXSTAR Gluon Recommendation:</strong> Install <a href="https://github.com/WordPress/mcp-adapter" target="_blank">MCP Adapter</a> to allow external AI agents to control SkyOS.</p></div>';
				}
			);
		}
	}

	/**
	 * Registers AI abilities with the WordPress Abilities API.
	 *
	 * Registers the 'summarize-content' ability which provides AI-powered
	 * content summarization functionality. The ability is exposed via REST API
	 * and includes input/output schema definitions for AI agents.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function gluonRegisterAbilities(): void {
		\wp_register_ability(
			'sparxstar-gluon/summarize-content',
			array(
				'label'               => \__( 'Summarize Content', 'sparxstar-gluon' ),
				'description'         => \__( 'Takes a post ID and returns a 2-sentence summary of the text.', 'sparxstar-gluon' ),
				'category'            => 'ai-content-tools',
				'execute_callback'    => array( $this, 'gluonAiSummarize' ),
				'permission_callback' => function () {
					return \current_user_can( 'edit_posts' );
				},
				// Metadata for AI agents to understand required inputs
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'post_id' => array(
							'type'        => 'integer',
							'description' => \__( 'The ID of the post to summarize.', 'sparxstar-gluon' ),
						),
					),
					'required'   => array( 'post_id' ),
				),
				// Metadata for the expected output
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'summary' => array( 'type' => 'string' ),
					),
				),
				// Expose this via the REST API at /wp-json/wp-abilities/v1/
				'show_in_rest'        => true,
			)
		);
	}

	/**
	 * Executes AI-powered content summarization.
	 *
	 * This method is the callback for the 'summarize-content' ability.
	 * It retrieves a WordPress post by ID and uses the AI_Client to generate
	 * a concise 2-sentence summary of the post content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input {
	 *     The validated input data based on input_schema.
	 *
	 *     @type int $post_id The ID of the post to summarize.
	 * }
	 *
	 * @return array|\WP_Error {
	 *     Returns an array with the summary on success, or WP_Error on failure.
	 *
	 *     @type string $summary The generated summary text.
	 * }
	 */
	public function gluonAiSummarize( array $input ): array|\WP_Error {
		$post_id = $input['post_id'];
		$post    = \get_post( $post_id );

		if ( ! $post ) {
			return new \WP_Error( 'invalid_post', 'Post not found.' );
		}

		$prompt = "Summarize the following content in 2 sentences:\n\n" . $post->post_content;

		$summary = AI_Client::prompt( $prompt )
			->using_temperature( 0.1 )
			->using_model_preference(
				array( 'anthropic', 'claude-sonnet-4-5' ),
				array( 'google', 'gemini-3-pro-preview' ),
				array( 'openai', 'gpt-5.1' )
			)
			->generate_text();

		return array(
			'summary' => $summary,
		);
	}

	/**
	 * Registers a dedicated MCP (Model Context Protocol) Server for Sky.
	 *
	 * Creates an MCP server endpoint that exposes Sky-specific abilities
	 * and tools for external AI agents to interact with. The server is
	 * accessible at /wp-json/mcp/sky-server.
	 *
	 * @since 1.0.0
	 *
	 * @param McpAdapter $adapter The MCP adapter instance used to create the server.
	 * @return void
	 */
	public function gluonRegisterMcpServer( McpAdapter $adapter ): void {
		$adapter->create_server(
			'sky-server',                      // ID
			'sparxstar-sky',                   // Namespace
			'mcp',                             // Route
			'Sky',                             // Name
			'Direct access to the Sky engine.', // Description
			'1.0.0',                           // Version
			array( HttpTransport::class ), // Transport
			ErrorLogMcpErrorHandler::class,
			NullMcpObservabilityHandler::class,
			// EXPOSE ONLY SKY TOOLS HERE
			// These match the names registered in SparxstarAbilityRegistry
			array(
				'sparxstar-gluon/summarize-content',
			),
			array(
				'description' => 'Resources available to Sky for performing tasks.',
				'type'        => 'array',
				'items'       => array(
					'type' => 'string',
				),
			), // Resources
			array(
				'description' => 'Use these prompts to interact with Sky.',
				'type'        => 'array',
				'items'       => array(
					'type' => 'string',
				),

			)  // Prompts
		);
	}
}
