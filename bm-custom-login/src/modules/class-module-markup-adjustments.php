<?php
/**
 * Apply markup adjustments to the login forms
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules;

use DOMDocument;
use DOMXPath;
use Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Module_Markup_Adjustments" class
 */
final class Module_Markup_Adjustments extends Utils\Module {
	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Mark the output buffering start.
		add_action(
			'login_init',
			function (): void {
				ob_start( [ $this, 'apply_markup_adjustments' ] );
			},
			1,
		);

		// Mark the output buffering end.
		add_action(
			'login_footer',
			function (): void {
				ob_end_flush();
			},
			PHP_INT_MAX,
		);
	}

	/**
	 * Apply the markup adjustments on the login form
	 *
	 * @param string $buffer Buffer of the login form markup.
	 *
	 * @return string Updated markup.
	 */
	public function apply_markup_adjustments( string $buffer ): string {
		// Adjust the buffer markup.
		$adjusted_buffer = $buffer . '</body></html>';

		// Create a new DOMDocument object.
		$doc = new DOMDocument();
		libxml_use_internal_errors( true );

		// Load the HTML content.
		$doc->loadHTML( $adjusted_buffer, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );
		$xpath = new DOMXPath( $doc );

		/**
		 * Allow other plugin and modules to apply
		 * their markup adjustments
		 *
		 * @param DOMDocument $doc   The DOMDocument object.
		 * @param DOMXPath    $xpath The DOMXPath object.
		 */
		$doc = apply_filters( 'custom_login__markup_adjustments', $doc, $xpath );

		// Get the modified HTML.
		$html = $doc->saveHTML();

		// In case of failure, return unmodified buffer.
		return false === $html ? $buffer : $html;
	}
}
