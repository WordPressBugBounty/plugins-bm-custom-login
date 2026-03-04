<?php
/**
 * Implement style and markup adjustments controlled by the "privacy_policy_link" fields group
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
 * The "Adjuster_Privacy_Policy_Link" class
 *
 * @phpstan-type Type_Adjuster_Privacy_Policy_Link_Config ?array{alignment:string,font_family:string,font_size:string,font_weight:string,hide:bool,letter_case:string,line_height:float,link_color:string,link_color_on_focus:string,link_color_on_hover:string,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,text_decoration:string}
 */
final class Adjuster_Privacy_Policy_Link extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'privacy_policy_link';

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Privacy_Policy_Link_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		/**
		 * Hide the privacy policy link
		 */
		if ( true === $config['hide'] ) {
			$link_wrapper = $xpath->query( '//div[contains(@class, "privacy-policy-page-link")]' );

			if ( false !== $link_wrapper && ! empty( $link_wrapper[0] ) ) {
				$link_wrapper = $link_wrapper[0];

				// Remove the link from the DOM.
				$link_wrapper->parentNode->removeChild( $link_wrapper );
			}
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Privacy_Policy_Link_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{alignment:string,font_family:string,font_size:string,font_weight:string,hide:bool,letter_case:string,line_height:float,link_color:string,link_color_on_focus:string,link_color_on_hover:string,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,text_decoration:string} $results */
		$results = $fields_group->get_all_fields_values();
		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Privacy_Policy_Link_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'.login .privacy-policy-page-link { text-align: %s; margin: %s; padding: %s; }',
				$config['alignment'],
				$this->styles->compose_spacing(
					[
						'top'    => $config['margin_top'],
						'right'  => $config['margin_right'],
						'bottom' => $config['margin_bottom'],
						'left'   => $config['margin_left'],
					],
				),
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
				'.login .privacy-policy-page-link a.privacy-policy-link { box-shadow: %s; color: %s; font-family: %s; font-size: %s; font-weight: %s; line-height: %.02fem; text-decoration: %s; text-transform: %s; }',
				$this->styles->compose_box_shadow_style( $config['shadow'] ),
				$this->styles->compose_color( $config['link_color'] ),
				$this->styles->get_font_family_name( $config['font_family'] ),
				$config['font_size'],
				$config['font_weight'],
				$config['line_height'],
				$config['text_decoration'],
				$config['letter_case'],
			),
			sprintf(
				'.login .privacy-policy-page-link a.privacy-policy-link:hover { box-shadow: %s; color: %s; }',
				$this->styles->compose_box_shadow_style( $config['shadow_on_hover'] ),
				$this->styles->compose_color( $config['link_color_on_hover'] ),
			),
			sprintf(
				'.login .privacy-policy-page-link a.privacy-policy-link:focus { box-shadow: %s; color: %s; }',
				$this->styles->compose_box_shadow_style( $config['shadow_on_focus'] ),
				$this->styles->compose_color( $config['link_color_on_focus'] ),
			),
		];

		return $styles;
	}
}
