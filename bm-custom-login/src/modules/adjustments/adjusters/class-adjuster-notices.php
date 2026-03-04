<?php
/**
 * Implement style and markup adjustments controlled by the "notices" fields group
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
 * The "Adjuster_Notices" class
 *
 * @phpstan-type Type_Adjuster_Notices_Config ?array{border_bottom_left_radius:string,border_bottom_right_radius:string,border_top_left_radius:string,border_top_right_radius:string,custom_notice_type:string,error_background_color:string,error_border_bottom_color:string,error_border_bottom_style:string,error_border_bottom_width:string,error_border_left_color:string,error_border_left_style:string,error_border_left_width:string,error_border_right_color:string,error_border_right_style:string,error_border_right_width:string,error_border_top_color:string,error_border_top_style:string,error_border_top_width:string,error_shadow:string,error_text_color:string,font_family:string,font_size:string,font_weight:string,line_height:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,notice_background_color:string,notice_border_bottom_color:string,notice_border_bottom_style:string,notice_border_bottom_width:string,notice_border_left_color:string,notice_border_left_style:string,notice_border_left_width:string,notice_border_right_color:string,notice_border_right_style:string,notice_border_right_width:string,notice_border_top_color:string,notice_border_top_style:string,notice_border_top_width:string,notice_shadow:string,notice_text_color:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,show_custom_notice:bool,success_background_color:string,success_border_bottom_color:string,success_border_bottom_style:string,success_border_bottom_width:string,success_border_left_color:string,success_border_left_style:string,success_border_left_width:string,success_border_right_color:string,success_border_right_style:string,success_border_right_width:string,success_border_top_color:string,success_border_top_style:string,success_border_top_width:string,success_shadow:string,success_text_color:string,notice_custom:string,notice_password_reset:string,notice_register:string}
 */
final class Adjuster_Notices extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'notices';

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Notices_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		/**
		 * Markup customizations for "password reset" notice
		 */
		$notice_password_reset = $xpath->query( '//body[contains(@class,"login-action-lostpassword")]//div[@id="login"]//div[contains(@class,"message")]/p' );

		if ( false !== $notice_password_reset && ! empty( $notice_password_reset[0] ) ) {
			$notice_password_reset            = $notice_password_reset[0];
			$notice_password_reset->nodeValue = esc_html( $config['notice_password_reset'] );
		}

		/**
		 * Markup customizations for "register" notice
		 */
		$notice_register = $xpath->query( '//body[contains(@class,"login-action-register")]//div[@id="login"]//div[contains(@class,"register")]/p' );

		if ( false !== $notice_register && ! empty( $notice_register[0] ) ) {
			$notice_register            = $notice_register[0];
			$notice_register->nodeValue = esc_html( $config['notice_register'] );
		}

		/**
		 * Display custom notice above the login form
		 */
		if ( true === $config['show_custom_notice'] && ! empty( $config['notice_custom'] ) ) {
			$login_form = $xpath->query( '//form[@id="loginform"]' );

			if ( false !== $login_form && ! empty( $login_form[0] ) ) {
				$login_form = $login_form[0];

				// Class names for the notice container.
				$class_names = [ 'notice' ];

				switch ( $config['custom_notice_type'] ) {
					case 'error':
					case 'notice':
						$class_names[] = sprintf( 'notice-%s', $config['custom_notice_type'] );
						break;
					case 'success':
						$class_names[] = 'success';
						break;
				}

				// Create a new div element for the custom notice.
				$custom_notice = $doc->createElement( 'div', esc_html( $config['notice_custom'] ) );
				$custom_notice->setAttribute( 'class', implode( ' ', $class_names ) );

				// Insert the custom notice before the login form.
				$login_form->parentNode->insertBefore( $custom_notice, $login_form );
			}
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return ?Type_Adjuster_Notices_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{border_bottom_left_radius:string,border_bottom_right_radius:string,border_top_left_radius:string,border_top_right_radius:string,custom_notice_type:string,error_background_color:string,error_border_bottom_color:string,error_border_bottom_style:string,error_border_bottom_width:string,error_border_left_color:string,error_border_left_style:string,error_border_left_width:string,error_border_right_color:string,error_border_right_style:string,error_border_right_width:string,error_border_top_color:string,error_border_top_style:string,error_border_top_width:string,error_shadow:string,error_text_color:string,font_family:string,font_size:string,font_weight:string,line_height:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,notice_background_color:string,notice_border_bottom_color:string,notice_border_bottom_style:string,notice_border_bottom_width:string,notice_border_left_color:string,notice_border_left_style:string,notice_border_left_width:string,notice_border_right_color:string,notice_border_right_style:string,notice_border_right_width:string,notice_border_top_color:string,notice_border_top_style:string,notice_border_top_width:string,notice_shadow:string,notice_text_color:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,show_custom_notice:bool,success_background_color:string,success_border_bottom_color:string,success_border_bottom_style:string,success_border_bottom_width:string,success_border_left_color:string,success_border_left_style:string,success_border_left_width:string,success_border_right_color:string,success_border_right_style:string,success_border_right_width:string,success_border_top_color:string,success_border_top_style:string,success_border_top_width:string,success_shadow:string,success_text_color:string} $results */
		$results = $fields_group->get_all_fields_values();

		$results['notice_custom']         = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'notice_custom.%s', $this->locale ) ) ?? '' );
		$results['notice_password_reset'] = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'notice_password_reset.%s', $this->locale ) ) ?? '' );
		$results['notice_register']       = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'notice_register.%s', $this->locale ) ) ?? '' );

		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Notices_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'.login .message p, .login .message ul, .login .message li, .login .notice p, .login .notice ul, .login .notice li { color: inherit; font-family: %s; font-size: %s; font-weight: %s; }',
				$this->styles->get_font_family_name( $config['font_family'] ),
				$config['font_size'],
				$config['font_weight'],
			),
			sprintf(
				'.login .notice { %s; margin: %s; padding: %s; }',
				$this->styles->compose_border_radius_style( $config['border_bottom_left_radius'], $config['border_bottom_right_radius'], $config['border_top_left_radius'], $config['border_top_right_radius'] ),
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
				'.login .notice.notice-error { background: %s; %s; box-shadow: %s; color: %s; }',
				$this->styles->compose_color( $config['error_background_color'], 'initial' ),
				$this->styles->compose_border_style(
					[
						'top_width'    => $config['error_border_top_width'],
						'top_style'    => $config['error_border_top_style'],
						'top_color'    => $config['error_border_top_color'],
						'right_width'  => $config['error_border_right_width'],
						'right_style'  => $config['error_border_right_style'],
						'right_color'  => $config['error_border_right_color'],
						'bottom_width' => $config['error_border_bottom_width'],
						'bottom_style' => $config['error_border_bottom_style'],
						'bottom_color' => $config['error_border_bottom_color'],
						'left_width'   => $config['error_border_left_width'],
						'left_style'   => $config['error_border_left_style'],
						'left_color'   => $config['error_border_left_color'],
					],
				),
				$this->styles->compose_box_shadow_style( $config['error_shadow'] ),
				$this->styles->compose_color( $config['error_text_color'] ),
			),
			sprintf(
				'.login .notice.notice-info { background: %s; %s; box-shadow: %s; color: %s; }',
				$this->styles->compose_color( $config['notice_background_color'], 'initial' ),
				$this->styles->compose_border_style(
					[
						'top_width'    => $config['notice_border_top_width'],
						'top_style'    => $config['notice_border_top_style'],
						'top_color'    => $config['notice_border_top_color'],
						'right_width'  => $config['notice_border_right_width'],
						'right_style'  => $config['notice_border_right_style'],
						'right_color'  => $config['notice_border_right_color'],
						'bottom_width' => $config['notice_border_bottom_width'],
						'bottom_style' => $config['notice_border_bottom_style'],
						'bottom_color' => $config['notice_border_bottom_color'],
						'left_width'   => $config['notice_border_left_width'],
						'left_style'   => $config['notice_border_left_style'],
						'left_color'   => $config['notice_border_left_color'],
					],
				),
				$this->styles->compose_box_shadow_style( $config['notice_shadow'] ),
				$this->styles->compose_color( $config['notice_text_color'] ),
			),
			sprintf(
				'.login .notice.success { background: %s; %s; box-shadow: %s; color: %s; }',
				$this->styles->compose_color( $config['success_background_color'], 'initial' ),
				$this->styles->compose_border_style(
					[
						'top_width'    => $config['success_border_top_width'],
						'top_style'    => $config['success_border_top_style'],
						'top_color'    => $config['success_border_top_color'],
						'right_width'  => $config['success_border_right_width'],
						'right_style'  => $config['success_border_right_style'],
						'right_color'  => $config['success_border_right_color'],
						'bottom_width' => $config['success_border_bottom_width'],
						'bottom_style' => $config['success_border_bottom_style'],
						'bottom_color' => $config['success_border_bottom_color'],
						'left_width'   => $config['success_border_left_width'],
						'left_style'   => $config['success_border_left_style'],
						'left_color'   => $config['success_border_left_color'],
					],
				),
				$this->styles->compose_box_shadow_style( $config['success_shadow'] ),
				$this->styles->compose_color( $config['success_text_color'] ),
			),
		];

		return $styles;
	}
}
