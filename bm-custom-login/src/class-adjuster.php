<?php
/**
 * Abstraction with utility functions for adjusters
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login;

use DOMDocument;
use DOMXPath;
use Teydea_Studio\Custom_Login\Dependencies\Utils;
use Teydea_Studio\Custom_Login\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Adjuster" class
 */
abstract class Adjuster {
	/**
	 * Hold the adjuster-specific config during the class lifetime
	 *
	 * @var ?array<string,mixed> Config array, null if couldn't read.
	 */
	protected ?array $config = null;

	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key;

	/**
	 * Hold the Container instance
	 *
	 * @var Utils\Container
	 */
	protected object $container;

	/**
	 * Hold the current locale
	 *
	 * @var string
	 */
	protected string $locale;

	/**
	 * Hold the Settings instance
	 *
	 * @var Settings
	 */
	protected object $settings;

	/**
	 * Hold the Styles instance
	 *
	 * @var Styles
	 */
	protected object $styles;

	/**
	 * Constructor
	 *
	 * @param Utils\Container $container Container instance.
	 * @param Settings        $settings  Settings instance.
	 * @param Styles          $styles    Styles instance.
	 * @param string          $locale    Current locale.
	 */
	public function __construct( object $container, object $settings, object $styles, string $locale ) {
		$this->container = $container;
		$this->settings  = $settings;
		$this->styles    = $styles;
		$this->locale    = $locale;
	}

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return ?array<string,mixed> Config array, null if couldn't read.
	 */
	protected function collect_config(): ?array {
		return null;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		return [];
	}

	/**
	 * Get the adjuster-specific config
	 *
	 * @return ?array<string,mixed> Config array, null if couldn't read.
	 */
	protected function get_config(): ?array {
		if ( null === $this->config ) {
			$this->config = $this->collect_config();
		}

		return $this->config;
	}

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {}
}
