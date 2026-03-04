<?php
/**
 * Implement style and markup adjustments controlled by the "login_form_checkbox_fields" fields group
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Adjustments;

use Teydea_Studio\Custom_Login\Adjuster;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Adjuster_Login_Form_Checkbox_Fields" class
 *
 * @phpstan-type Type_Adjuster_Login_Form_Checkbox_Fields_Config ?array{background_color:string,background_color_checked:string,background_color_on_focus:string,background_color_on_focus_checked:string,background_color_on_hover:string,background_color_on_hover_checked:string,border_bottom_color:string,border_bottom_color_on_focus:string,border_bottom_color_on_hover:string,border_bottom_left_radius:string,border_bottom_right_radius:string,border_bottom_style:string,border_bottom_style_on_focus:string,border_bottom_style_on_hover:string,border_bottom_width:string,border_bottom_width_on_focus:string,border_bottom_width_on_hover:string,border_left_color:string,border_left_color_on_focus:string,border_left_color_on_hover:string,border_left_style:string,border_left_style_on_focus:string,border_left_style_on_hover:string,border_left_width:string,border_left_width_on_focus:string,border_left_width_on_hover:string,border_right_color:string,border_right_color_on_focus:string,border_right_color_on_hover:string,border_right_style:string,border_right_style_on_focus:string,border_right_style_on_hover:string,border_right_width:string,border_right_width_on_focus:string,border_right_width_on_hover:string,border_top_color:string,border_top_color_on_focus:string,border_top_color_on_hover:string,border_top_left_radius:string,border_top_right_radius:string,border_top_style:string,border_top_style_on_focus:string,border_top_style_on_hover:string,border_top_width:string,border_top_width_on_focus:string,border_top_width_on_hover:string,field_size:int,field_margin_bottom:string,field_margin_left:string,field_margin_right:string,field_margin_top:string,icon:string,icon_color:string,icon_color_on_focus:string,icon_color_on_hover:string,icon_size:int,icon_margin_bottom:string,icon_margin_left:string,icon_margin_right:string,icon_margin_top:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string}
 */
final class Adjuster_Login_Form_Checkbox_Fields extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'login_form_checkbox_fields';

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Login_Form_Checkbox_Fields_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{background_color:string,background_color_checked:string,background_color_on_focus:string,background_color_on_focus_checked:string,background_color_on_hover:string,background_color_on_hover_checked:string,border_bottom_color:string,border_bottom_color_on_focus:string,border_bottom_color_on_hover:string,border_bottom_left_radius:string,border_bottom_right_radius:string,border_bottom_style:string,border_bottom_style_on_focus:string,border_bottom_style_on_hover:string,border_bottom_width:string,border_bottom_width_on_focus:string,border_bottom_width_on_hover:string,border_left_color:string,border_left_color_on_focus:string,border_left_color_on_hover:string,border_left_style:string,border_left_style_on_focus:string,border_left_style_on_hover:string,border_left_width:string,border_left_width_on_focus:string,border_left_width_on_hover:string,border_right_color:string,border_right_color_on_focus:string,border_right_color_on_hover:string,border_right_style:string,border_right_style_on_focus:string,border_right_style_on_hover:string,border_right_width:string,border_right_width_on_focus:string,border_right_width_on_hover:string,border_top_color:string,border_top_color_on_focus:string,border_top_color_on_hover:string,border_top_left_radius:string,border_top_right_radius:string,border_top_style:string,border_top_style_on_focus:string,border_top_style_on_hover:string,border_top_width:string,border_top_width_on_focus:string,border_top_width_on_hover:string,field_size:int,field_margin_bottom:string,field_margin_left:string,field_margin_right:string,field_margin_top:string,icon:string,icon_color:string,icon_color_on_focus:string,icon_color_on_hover:string,icon_size:int,icon_margin_bottom:string,icon_margin_left:string,icon_margin_right:string,icon_margin_top:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string} $results */
		$results = $fields_group->get_all_fields_values();
		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Login_Form_Checkbox_Fields_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'.login form input[type=checkbox] { background: %1$s; %2$s; %3$s; box-shadow: %4$s; height: %5$s; margin: %6$s; min-width: %5$s; width: %5$s; }',
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
				sprintf( '%dpx', $config['field_size'] ),
				$this->styles->compose_spacing(
					[
						'top'    => $config['field_margin_top'],
						'right'  => $config['field_margin_right'],
						'bottom' => $config['field_margin_bottom'],
						'left'   => $config['field_margin_left'],
					],
				),
			),
			sprintf(
				'.login form input[type=checkbox]:checked { background: %s; }',
				$this->styles->compose_color( $config['background_color_checked'], 'initial' ),
			),
			sprintf(
				'.login form input[type=checkbox]:hover:checked { background: %s; }',
				$this->styles->compose_color( $config['background_color_on_hover_checked'], 'initial' ),
			),
			sprintf(
				'.login form input[type=checkbox]:hover { background: %s; %s; box-shadow: %s; }',
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
			),
			sprintf(
				'.login form input[type=checkbox]:focus:checked { background: %s; }',
				$this->styles->compose_color( $config['background_color_on_focus_checked'], 'initial' ),
			),
			sprintf(
				'.login form input[type=checkbox]:focus { background: %s; %s; box-shadow: %s; }',
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
			),
		];

		switch ( $config['icon'] ) {
			case 'fontawesome-check':
				/**
				 * Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com
				 *
				 * License - https://fontawesome.com/license/free
				 * Copyright 2025 Fonticons, Inc.
				 *
				 * @see https://fontawesome.com/icons/check?f=classic&s=solid
				 */
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor"><path d="M438.6 105.4c12.5 12.5 12.5 32.8 0 45.3l-256 256c-12.5 12.5-32.8 12.5-45.3 0l-128-128c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0L160 338.7 393.4 105.4c12.5-12.5 32.8-12.5 45.3 0z" /></svg>';
				break;
			case 'heroicons-check':
				/**
				 * License - https://github.com/tailwindlabs/heroicons/blob/master/LICENSE
				 * Copyright (c) Tailwind Labs, Inc.
				 *
				 * @see https://heroicons.com/
				 */
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" /></svg>';
				break;
			case 'default':
			default:
				$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M14.83 4.89l1.34.94-5.81 8.38H9.02L5.78 9.67l1.34-1.25 2.57 2.4z" fill="currentColor" /></svg>';
				break;
		}

		$styles[] = sprintf(
			'.login form input[type=checkbox]:checked::before { content: url("data:image/svg+xml;base64,%1$s"); height: %2$s; width: %2$s; margin: %3$s; }',
			base64_encode( str_replace( 'currentColor', $config['icon_color'], $svg ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
			sprintf( '%dpx', $config['icon_size'] ),
			$this->styles->compose_spacing(
				[
					'top'    => $config['icon_margin_top'],
					'right'  => $config['icon_margin_right'],
					'bottom' => $config['icon_margin_bottom'],
					'left'   => $config['icon_margin_left'],
				],
			),
		);

		$styles[] = sprintf(
			'.login form input[type=checkbox]:checked:hover::before { content: url("data:image/svg+xml;base64,%s"); }',
			base64_encode( str_replace( 'currentColor', $config['icon_color_on_hover'], $svg ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		);

		$styles[] = sprintf(
			'.login form input[type=checkbox]:checked:focus::before { content: url("data:image/svg+xml;base64,%s"); }',
			base64_encode( str_replace( 'currentColor', $config['icon_color_on_focus'], $svg ) ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		);

		return $styles;
	}
}
