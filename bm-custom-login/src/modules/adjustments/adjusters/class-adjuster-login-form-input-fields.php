<?php
/**
 * Implement style and markup adjustments controlled by the "login_form_input_fields" fields group
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
 * The "Adjuster_Login_Form_Input_Fields" class
 *
 * @phpstan-type Type_Adjuster_Login_Form_Input_Fields_Config ?array{background_color:string,background_color_on_focus:string,background_color_on_hover:string,border_bottom_color:string,border_bottom_color_on_focus:string,border_bottom_color_on_hover:string,border_bottom_left_radius:string,border_bottom_right_radius:string,border_bottom_style:string,border_bottom_style_on_focus:string,border_bottom_style_on_hover:string,border_bottom_width:string,border_bottom_width_on_focus:string,border_bottom_width_on_hover:string,border_left_color:string,border_left_color_on_focus:string,border_left_color_on_hover:string,border_left_style:string,border_left_style_on_focus:string,border_left_style_on_hover:string,border_left_width:string,border_left_width_on_focus:string,border_left_width_on_hover:string,border_right_color:string,border_right_color_on_focus:string,border_right_color_on_hover:string,border_right_style:string,border_right_style_on_focus:string,border_right_style_on_hover:string,border_right_width:string,border_right_width_on_focus:string,border_right_width_on_hover:string,border_top_color:string,border_top_color_on_focus:string,border_top_color_on_hover:string,border_top_left_radius:string,border_top_right_radius:string,border_top_style:string,border_top_style_on_focus:string,border_top_style_on_hover:string,border_top_width:string,border_top_width_on_focus:string,border_top_width_on_hover:string,eye_icon_color:string,eye_icon_color_on_focus:string,eye_icon_color_on_hover:string,font_family:string,font_size:string,font_weight:string,line_height:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,placeholder_color:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,text_color:string,text_color_on_focus:string,text_color_on_hover:string,placeholder_email:string,placeholder_password:string,placeholder_username:string,placeholder_username_or_email_address:string}
 */
final class Adjuster_Login_Form_Input_Fields extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'login_form_input_fields';

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Login_Form_Input_Fields_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		/**
		 * Markup customizations for "username or email address" field of the "login" form
		 */
		$input_username_or_email_address = $xpath->query( '//form[@id="loginform"]//input[@id="user_login"]' );

		if ( false !== $input_username_or_email_address && ! empty( $input_username_or_email_address[0] ) ) {
			$input_username_or_email_address = $input_username_or_email_address[0];
			$input_username_or_email_address->setAttribute( 'placeholder', esc_html( $config['placeholder_username_or_email_address'] ) );
		}

		/**
		 * Markup customizations for "password" label of the "login" form
		 */
		$input_password = $xpath->query( '//form[@id="loginform"]//input[@id="user_pass"]' );

		if ( false !== $input_password && ! empty( $input_password[0] ) ) {
			$input_password = $input_password[0];
			$input_password->setAttribute( 'placeholder', esc_html( $config['placeholder_password'] ) );
		}

		/**
		 * Markup customizations for "username" field of the "register" form
		 */
		$input_username = $xpath->query( '//form[@id="registerform"]//input[@id="user_login"]' );

		if ( false !== $input_username && ! empty( $input_username[0] ) ) {
			$input_username = $input_username[0];
			$input_username->setAttribute( 'placeholder', esc_html( $config['placeholder_username'] ) );
		}

		/**
		 * Markup customizations for "email" field of the "register" form
		 */
		$input_email = $xpath->query( '//form[@id="registerform"]//input[@id="user_email"]' );

		if ( false !== $input_email && ! empty( $input_email[0] ) ) {
			$input_email = $input_email[0];
			$input_email->setAttribute( 'placeholder', esc_html( $config['placeholder_email'] ) );
		}

		/**
		 * Markup customizations for "username or email address" field of the "lost password" form
		 */
		$input_username_or_email_address = $xpath->query( '//form[@id="lostpasswordform"]//input[@id="user_login"]' );

		if ( false !== $input_username_or_email_address && ! empty( $input_username_or_email_address[0] ) ) {
			$input_username_or_email_address = $input_username_or_email_address[0];
			$input_username_or_email_address->setAttribute( 'placeholder', esc_html( $config['placeholder_username_or_email_address'] ) );
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Login_Form_Input_Fields_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{background_color:string,background_color_on_focus:string,background_color_on_hover:string,border_bottom_color:string,border_bottom_color_on_focus:string,border_bottom_color_on_hover:string,border_bottom_left_radius:string,border_bottom_right_radius:string,border_bottom_style:string,border_bottom_style_on_focus:string,border_bottom_style_on_hover:string,border_bottom_width:string,border_bottom_width_on_focus:string,border_bottom_width_on_hover:string,border_left_color:string,border_left_color_on_focus:string,border_left_color_on_hover:string,border_left_style:string,border_left_style_on_focus:string,border_left_style_on_hover:string,border_left_width:string,border_left_width_on_focus:string,border_left_width_on_hover:string,border_right_color:string,border_right_color_on_focus:string,border_right_color_on_hover:string,border_right_style:string,border_right_style_on_focus:string,border_right_style_on_hover:string,border_right_width:string,border_right_width_on_focus:string,border_right_width_on_hover:string,border_top_color:string,border_top_color_on_focus:string,border_top_color_on_hover:string,border_top_left_radius:string,border_top_right_radius:string,border_top_style:string,border_top_style_on_focus:string,border_top_style_on_hover:string,border_top_width:string,border_top_width_on_focus:string,border_top_width_on_hover:string,eye_icon_color:string,eye_icon_color_on_focus:string,eye_icon_color_on_hover:string,font_family:string,font_size:string,font_weight:string,line_height:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,placeholder_color:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,text_color:string,text_color_on_focus:string,text_color_on_hover:string,placeholder_email:string,placeholder_password:string,placeholder_username:string,placeholder_username_or_email_address:string} $results */
		$results = $fields_group->get_all_fields_values();

		$results['placeholder_email']                     = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'placeholder_email.%s', $this->locale ) ) ?? '' );
		$results['placeholder_password']                  = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'placeholder_password.%s', $this->locale ) ) ?? '' );
		$results['placeholder_username']                  = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'placeholder_username.%s', $this->locale ) ) ?? '' );
		$results['placeholder_username_or_email_address'] = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'placeholder_username_or_email_address.%s', $this->locale ) ) ?? '' );

		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Login_Form_Input_Fields_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'.login form .input, .login input[type=password], .login input[type=email], .login input[type=text] { background: %s; %s; %s; box-shadow: %s; color: %s; font-family: %s; font-size: %s; font-weight: %s; line-height: %.02fem; margin: %s; padding: %s; }',
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
				'.login form .input:hover, .login input[type=password]:hover, .login input[type=email]:hover, .login input[type=text]:hover { background: %s; %s; box-shadow: %s; color: %s; }',
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
				'.login form .input:focus, .login input[type=password]:focus, .login input[type=email]:focus, .login input[type=text]:focus { background: %s; %s; box-shadow: %s; color: %s; }',
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
			sprintf(
				'.login .wp-pwd { margin-bottom: %s; }',
				empty( $config['margin_bottom'] ) ? '0px' : $config['margin_bottom'],
			),
			'.login .wp-pwd input[type=password] { margin-bottom: 0; }',
			'.login .button.wp-hide-pw { top: 0; bottom: 0; height: auto; display: flex; align-items: center; }',
			'.login .button.wp-hide-pw .dashicons { top: auto; }',
			sprintf(
				'.login .button.wp-hide-pw { color: %s; }',
				$this->styles->compose_color( $config['eye_icon_color'] ),
			),
			sprintf(
				'.login .button.wp-hide-pw:hover { color: %s; }',
				$this->styles->compose_color( $config['eye_icon_color_on_hover'] ),
			),
			sprintf(
				'.login .button.wp-hide-pw:focus { color: %s; }',
				$this->styles->compose_color( $config['eye_icon_color_on_focus'] ),
			),
		];

		if ( ! empty( $config['placeholder_color'] ) ) {
			$styles[] = sprintf(
				'.login form input::placeholder { color: %s; }',
				$this->styles->compose_color( $config['placeholder_color'] ),
			);
		}

		return $styles;
	}
}
