<?php
/**
 * Implement style and markup adjustments controlled by the "language_switcher" fields group
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Adjustments;

use DOMDocument;
use DOMXPath;
use Teydea_Studio\Custom_Login\Adjuster;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Adjuster_Language_Switcher" class
 *
 * @phpstan-type Type_Adjuster_Language_Switcher_Config ?array{icon_color:string,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,show:bool}
 */
final class Adjuster_Language_Switcher extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'language_switcher';

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Language_Switcher_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		if ( false === $config['show'] ) {
			$language_switcher = $xpath->query( '//div[contains(@class,"language-switcher")]' );

			if ( false !== $language_switcher && ! empty( $language_switcher[0] ) ) {
				$language_switcher = $language_switcher[0];

				// Remove the language switcher container from the DOM.
				$language_switcher->parentNode->removeChild( $language_switcher );
			}
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Language_Switcher_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{icon_color:string,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,show:bool} $results */
		$results = $fields_group->get_all_fields_values();

		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Language_Switcher_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [];

		if ( true === $config['show'] ) {
			$styles = [
				sprintf(
					'.language-switcher { padding: %s; }',
					$this->styles->compose_spacing(
						[
							'top'    => $config['padding_top'],
							'right'  => $config['padding_right'],
							'bottom' => $config['padding_bottom'],
							'left'   => $config['padding_left'],
						],
					),
				),
				sprintf(
					'form#language-switcher { margin: %s; }',
					$this->styles->compose_spacing(
						[
							'top'    => $config['margin_top'],
							'right'  => $config['margin_right'],
							'bottom' => $config['margin_bottom'],
							'left'   => $config['margin_left'],
						],
					),
				),
				sprintf(
					'label[for="language-switcher-locales"] span.dashicons { color: %s; }',
					$this->styles->compose_color( $config['icon_color'] ),
				),

				/**
				 * Reset core styles applied on smaller screens only
				 * to ensure consistent appearance
				 */
				'@media screen and (max-width: 782px) { #language-switcher label, #language-switcher select { margin-right: .25em; } .wp-admin .form-table select, .wp-core-ui select { min-height: unset; font-size: 14px; line-height: 2; padding: 0 24px 0 8px; } }',
			];
		}

		return $styles;
	}
}
