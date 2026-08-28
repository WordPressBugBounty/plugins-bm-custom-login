<?php
/**
 * Apply markup adjustments to the login forms
 *
 * @package Teydea_Studio\Login_Customizer
 */

namespace Teydea_Studio\Login_Customizer\Modules\Markup_Adjustments;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Teydea_Studio\Login_Customizer\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
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

		// Level the login markup across the supported WordPress versions.
		$this->normalize_logo_heading( $xpath );

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

	/**
	 * Give the login logo heading the class that WordPress 6.7 added to it
	 *
	 * Every logo-aware adjuster and every logo style rule keys off
	 * "h1.wp-login-logo", which core only started emitting in 6.7. On 6.6 —
	 * still a supported version — that heading carries no class at all, so
	 * without this the whole "logo" fields group silently does nothing there.
	 *
	 * @param DOMXPath $xpath XPath object.
	 *
	 * @return void
	 */
	private function normalize_logo_heading( DOMXPath $xpath ): void {
		$heading = $xpath->query( '//div[@id="login"]/h1[not(contains(@class, "wp-login-logo"))]' );

		if ( false === $heading || ! ( $heading[0] instanceof DOMElement ) ) {
			return;
		}

		$heading[0]->setAttribute( 'class', trim( $heading[0]->getAttribute( 'class' ) . ' wp-login-logo' ) );
	}
}
