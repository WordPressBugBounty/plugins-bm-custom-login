<?php
/**
 * Implement style and markup adjustments controlled by the "login_form_button_secondary" fields group
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
 * The "Adjuster_Login_Form_Button_Secondary" class
 *
 * @phpstan-type Type_Adjuster_Login_Form_Button_Secondary_Config ?array{background_color:string,background_color_on_focus:string,background_color_on_hover:string,border_bottom_color:string,border_bottom_color_on_focus:string,border_bottom_color_on_hover:string,border_bottom_left_radius:string,border_bottom_right_radius:string,border_bottom_style:string,border_bottom_style_on_focus:string,border_bottom_style_on_hover:string,border_bottom_width:string,border_bottom_width_on_focus:string,border_bottom_width_on_hover:string,border_left_color:string,border_left_color_on_focus:string,border_left_color_on_hover:string,border_left_style:string,border_left_style_on_focus:string,border_left_style_on_hover:string,border_left_width:string,border_left_width_on_focus:string,border_left_width_on_hover:string,border_right_color:string,border_right_color_on_focus:string,border_right_color_on_hover:string,border_right_style:string,border_right_style_on_focus:string,border_right_style_on_hover:string,border_right_width:string,border_right_width_on_focus:string,border_right_width_on_hover:string,border_top_color:string,border_top_color_on_focus:string,border_top_color_on_hover:string,border_top_left_radius:string,border_top_right_radius:string,border_top_style:string,border_top_style_on_focus:string,border_top_style_on_hover:string,border_top_width:string,border_top_width_on_focus:string,border_top_width_on_hover:string,font_family:string,font_size:string,font_weight:string,letter_case:string,line_height:float,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,text_color:string,text_color_on_focus:string,text_color_on_hover:string,label_change:string,label_generate_password:string}
 */
final class Adjuster_Login_Form_Button_Secondary extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'login_form_button_secondary';

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Login_Form_Button_Secondary_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		/**
		 * Markup customizations for "Log In" button of the "language switcher" form
		 */
		$button_change = $xpath->query( '//form[@id="language-switcher"]//input[@type="submit"]' );

		if ( false !== $button_change && ! empty( $button_change[0] ) ) {
			$button_change = $button_change[0];
			$button_change->setAttribute( 'value', esc_html( $config['label_change'] ) );
		}

		/**
		 * Markup customizations for "Generate Password" button of the "reset password" form
		 */
		$button_generate_password = $xpath->query( '//form[@id="resetpassform"]//button[contains(@class,"wp-generate-pw")]' );

		if ( false !== $button_generate_password && ! empty( $button_generate_password[0] ) ) {
			$button_generate_password            = $button_generate_password[0];
			$button_generate_password->nodeValue = esc_html( $config['label_generate_password'] );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Login_Form_Button_Secondary_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{background_color:string,background_color_on_focus:string,background_color_on_hover:string,border_bottom_color:string,border_bottom_color_on_focus:string,border_bottom_color_on_hover:string,border_bottom_left_radius:string,border_bottom_right_radius:string,border_bottom_style:string,border_bottom_style_on_focus:string,border_bottom_style_on_hover:string,border_bottom_width:string,border_bottom_width_on_focus:string,border_bottom_width_on_hover:string,border_left_color:string,border_left_color_on_focus:string,border_left_color_on_hover:string,border_left_style:string,border_left_style_on_focus:string,border_left_style_on_hover:string,border_left_width:string,border_left_width_on_focus:string,border_left_width_on_hover:string,border_right_color:string,border_right_color_on_focus:string,border_right_color_on_hover:string,border_right_style:string,border_right_style_on_focus:string,border_right_style_on_hover:string,border_right_width:string,border_right_width_on_focus:string,border_right_width_on_hover:string,border_top_color:string,border_top_color_on_focus:string,border_top_color_on_hover:string,border_top_left_radius:string,border_top_right_radius:string,border_top_style:string,border_top_style_on_focus:string,border_top_style_on_hover:string,border_top_width:string,border_top_width_on_focus:string,border_top_width_on_hover:string,font_family:string,font_size:string,font_weight:string,letter_case:string,line_height:float,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,text_color:string,text_color_on_focus:string,text_color_on_hover:string} $results */
		$results = $fields_group->get_all_fields_values();

		$results['label_change']            = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'label_change.%s', $this->locale ) ) ?? '' );
		$results['label_generate_password'] = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'label_generate_password.%s', $this->locale ) ) ?? '' );

		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Login_Form_Button_Secondary_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'.login .language-switcher .button, .wp-core-ui .button.wp-generate-pw { background: %s; %s; %s; box-shadow: %s; color: %s; font-family: %s; font-size: %s; font-weight: %s; line-height: %.02fem; margin: 0; min-height: unset; text-transform: %s; padding: %s; }',
				$this->styles->compose_color( $config['background_color'], 'initial' ),
				$this->styles->compose_border_style(
					[
						'top_width'    => $config['border_top_width'],
						'top_style'    => $config['border_top_style'],
						'top_color'    => $config['border_top_color'],
						'right_width'  => $config['border_right_width'],
						'right_style'  => $config['border_right_style'],
						'right_color'  => $config['border_right_color'],
						'bottom_width' => $config['border_bottom_width'],
						'bottom_style' => $config['border_bottom_style'],
						'bottom_color' => $config['border_bottom_color'],
						'left_width'   => $config['border_left_width'],
						'left_style'   => $config['border_left_style'],
						'left_color'   => $config['border_left_color'],
					],
				),
				$this->styles->compose_border_radius_style( $config['border_bottom_left_radius'], $config['border_bottom_right_radius'], $config['border_top_left_radius'], $config['border_top_right_radius'] ),
				$this->styles->compose_box_shadow_style( $config['shadow'] ),
				$this->styles->compose_color( $config['text_color'] ),
				$this->styles->get_font_family_name( $config['font_family'] ),
				$config['font_size'],
				$config['font_weight'],
				$config['line_height'],
				$config['letter_case'],
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
				'.login .language-switcher .button:hover, .wp-core-ui .button.wp-generate-pw:hover { background: %s; %s; box-shadow: %s; color: %s; }',
				$this->styles->compose_color( $config['background_color_on_hover'], 'initial' ),
				$this->styles->compose_border_style(
					[
						'top_width'    => $config['border_top_width_on_hover'],
						'top_style'    => $config['border_top_style_on_hover'],
						'top_color'    => $config['border_top_color_on_hover'],
						'right_width'  => $config['border_right_width_on_hover'],
						'right_style'  => $config['border_right_style_on_hover'],
						'right_color'  => $config['border_right_color_on_hover'],
						'bottom_width' => $config['border_bottom_width_on_hover'],
						'bottom_style' => $config['border_bottom_style_on_hover'],
						'bottom_color' => $config['border_bottom_color_on_hover'],
						'left_width'   => $config['border_left_width_on_hover'],
						'left_style'   => $config['border_left_style_on_hover'],
						'left_color'   => $config['border_left_color_on_hover'],
					],
				),
				$this->styles->compose_box_shadow_style( $config['shadow_on_hover'] ),
				$this->styles->compose_color( $config['text_color_on_hover'] ),
			),
			sprintf(
				'.login .language-switcher .button:focus, .wp-core-ui .button.wp-generate-pw:focus { background: %s; %s; box-shadow: %s; color: %s; }',
				$this->styles->compose_color( $config['background_color_on_focus'], 'initial' ),
				$this->styles->compose_border_style(
					[
						'top_width'    => $config['border_top_width_on_focus'],
						'top_style'    => $config['border_top_style_on_focus'],
						'top_color'    => $config['border_top_color_on_focus'],
						'right_width'  => $config['border_right_width_on_focus'],
						'right_style'  => $config['border_right_style_on_focus'],
						'right_color'  => $config['border_right_color_on_focus'],
						'bottom_width' => $config['border_bottom_width_on_focus'],
						'bottom_style' => $config['border_bottom_style_on_focus'],
						'bottom_color' => $config['border_bottom_color_on_focus'],
						'left_width'   => $config['border_left_width_on_focus'],
						'left_style'   => $config['border_left_style_on_focus'],
						'left_color'   => $config['border_left_color_on_focus'],
					],
				),
				$this->styles->compose_box_shadow_style( $config['shadow_on_focus'] ),
				$this->styles->compose_color( $config['text_color_on_focus'] ),
			),
		];

		return $styles;
	}
}
