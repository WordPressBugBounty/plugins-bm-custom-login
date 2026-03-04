<?php
/**
 * Implement style and markup adjustments controlled by the "login_form_remember_me_checkbox" fields group
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Adjustments;

use DOMDocument;
use DOMXPath;
use Teydea_Studio\Custom_Login\Adjuster;
use Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Adjuster_Login_Form_Remember_Me_Checkbox" class
 *
 * @phpstan-type Type_Adjuster_Login_Form_Remember_Me_Checkbox_Config ?array{margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,visibility:string,label_remember_me:string}
 */
final class Adjuster_Login_Form_Remember_Me_Checkbox extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'login_form_remember_me_checkbox';

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Login_Form_Remember_Me_Checkbox_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		/**
		 * Markup customizations for the checkbox field value
		 */
		if ( 'visible' !== $config['visibility'] ) {
			$wrapper = $xpath->query( '//input[@id="rememberme"]' );

			if ( false !== $wrapper && ! empty( $wrapper[0] ) ) {
				$wrapper = $wrapper[0];
				$wrapper->setAttribute( 'disabled', 'disabled' );

				if ( 'hidden-checked' === $config['visibility'] ) {
					$wrapper->setAttribute( 'checked', 'checked' );
				}
			}

			return $doc;
		}

		/**
		 * Markup customizations for the checkbox label
		 */
		$label = $xpath->query( '//label[@for="rememberme"]' );

		if ( false !== $label && ! empty( $label[0] ) ) {
			$label            = $label[0];
			$label->nodeValue = esc_html( $config['label_remember_me'] );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Login_Form_Remember_Me_Checkbox_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,visibility:string} $results */
		$results                      = $fields_group->get_all_fields_values();
		$results['label_remember_me'] = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'label_remember_me.%s', $this->locale ) ) ?? '' );

		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Login_Form_Remember_Me_Checkbox_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'#login form p.forgetmenot { margin: %1$s; }',
				$this->styles->compose_spacing(
					[
						'top'    => $config['margin_top'],
						'right'  => $config['margin_right'],
						'bottom' => $config['margin_bottom'],
						'left'   => $config['margin_left'],
					],
				),
			),
		];

		if ( 'visible' !== $config['visibility'] ) {
			$styles[] = '#login form p.forgetmenot { display: none; }';
		}

		return $styles;
	}
}
