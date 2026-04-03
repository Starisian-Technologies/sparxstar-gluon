<?php
/**
 * SPARXSTAR Gluon Template Shortcode Loader
 *
 * Provides functionality to register WordPress shortcodes that render PHP templates.
 * Allows creation of reusable template components embeddable anywhere shortcodes work.
 *
 * @package    Starisian\Sparxstar\Gluon\Frontend
 * @subpackage Shortcodes
 * @since      1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 * @license    MIT License
 * @copyright  Copyright 2025-2026 Starisian Technologies.
 * @version    1.0.0
 */

declare(strict_types=1);
namespace Starisian\Sparxstar\Gluon\frontend;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Template Shortcode Loader Class
 *
 * Provides functionality to register WordPress shortcodes that render PHP templates.
 * This allows you to create reusable template components that can be embedded
 * anywhere shortcodes are supported (posts, pages, widgets, etc.).
 *
 * Features:
 * - Register custom shortcodes tied to template files
 * - Pass attributes to templates
 * - Output buffering for clean shortcode output
 * - Template file validation
 *
 * Example Usage:
 * ```php
 * $shortcode = new SparxstarGluonLoader(
 *     'my_template',
 *     SPARXSTAR_GLUON_PLUGIN_PATH . 'src/templates/my-template.php'
 * );
 * // Use in content: [my_template title="My Title"]
 * ```
 *
 * @package    Starisian\Sparxstar\Gluon\frontend
 * @subpackage Shortcodes
 * @since      1.0.0
 * @version    1.0.0
 * @author     Starisian Technologies (Max Barrett) <support@starisian.com>
 */
class SparxstarGluonLoader {

	/**
	 * The shortcode tag name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $tag;

	/**
	 * Path to the template file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private string $template_path;

	/**
	 * Constructor - Initialize and register the shortcode.
	 *
	 * Creates a new shortcode that renders a specified template file.
	 * The shortcode is automatically registered with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $tag           The name of the shortcode (e.g., 'my_custom_view').
	 * @param string $template_path The full path to the .php template file.
	 */
	public function __construct( string $tag, string $template_path ) {
		$this->tag           = $tag;
		$this->template_path = $template_path;
		// Register the shortcode with WordPress
		$this->gluonRegisterHooks();
	}

	/**
	 * Register the shortcode with WordPress.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function gluonRegisterHooks(): void {
		// Register the shortcode with WordPress
		\add_shortcode( $this->tag, array( $this, 'gluonRenderShortcode' ) );
	}

	/**
	 * Shortcode callback that renders the template.
	 *
	 * This method is called by WordPress when the shortcode is encountered.
	 * It validates the template exists, extracts shortcode attributes,
	 * includes the template file, and returns the output.
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes passed by the user.
	 * @return string The rendered template output or error message.
	 */
	public function gluonRenderShortcode( array $atts ): string {
		// Extract attributes so they are available inside the template as variables
		// Usage in template: echo $title;
		$args = shortcode_atts(
			array(
				'title' => 'Default Title',
			),
			$atts
		);

		// Check if the file exists to avoid errors
		if ( ! dirname( $this->template_path ) || ! file_exists( $this->template_path ) ) {
			return 'Template not found at: ' . esc_html( $this->template_path );
		}

		// Start output buffering
		ob_start();

		// Pass the attributes into the template scope
		// This makes $args available inside the included file
		include $this->template_path;

		// Return the buffer contents and turn off buffering
		return ob_get_clean();
	}
}
