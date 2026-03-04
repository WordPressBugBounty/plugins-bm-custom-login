<?php
/**
 * Implement style and markup adjustments controlled by the "login_form_labels" fields group
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Adjustments;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Teydea_Studio\Custom_Login\Adjuster;
use Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Adjuster_Login_Form_Labels" class
 *
 * @phpstan-type Type_Adjuster_Login_Form_Labels_Config ?array{font_family:string,font_size:string,font_weight:string,letter_case:string,line_height:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,show:bool,text_color:string,label_email:string,label_password:string,label_username:string,label_username_or_email_address:string}
 */
final class Adjuster_Login_Form_Labels extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'login_form_labels';

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Login_Form_Labels_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		/**
		 * Markup customizations for "username or email address" label of the "login" form
		 */
		$label_username_or_email_address = $xpath->query( '//form[@id="loginform"]//label[@for="user_login"]' );

		if ( false !== $label_username_or_email_address && ! empty( $label_username_or_email_address[0] ) ) {
			$label_username_or_email_address            = $label_username_or_email_address[0];
			$label_username_or_email_address->nodeValue = esc_html( $config['label_username_or_email_address'] );
		}

		/**
		 * Markup customizations for "password" label of the "login" form
		 */
		$label_password = $xpath->query( '//form[@id="loginform"]//label[@for="user_pass"]' );

		if ( false !== $label_password && ! empty( $label_password[0] ) ) {
			$label_password            = $label_password[0];
			$label_password->nodeValue = esc_html( $config['label_password'] );
		}

		/**
		 * Markup customizations for "username" label of the "register" form
		 */
		$label_username = $xpath->query( '//form[@id="registerform"]//label[@for="user_login"]' );

		if ( false !== $label_username && ! empty( $label_username[0] ) ) {
			$label_username            = $label_username[0];
			$label_username->nodeValue = esc_html( $config['label_username'] );
		}

		/**
		 * Markup customizations for "email" label of the "register" form
		 */
		$label_email = $xpath->query( '//form[@id="registerform"]//label[@for="user_email"]' );

		if ( false !== $label_email && ! empty( $label_email[0] ) ) {
			$label_email            = $label_email[0];
			$label_email->nodeValue = esc_html( $config['label_email'] );
		}

		/**
		 * Markup customizations for "username or email address" label of the "lost password" form
		 */
		$label_username_or_email_address = $xpath->query( '//form[@id="lostpasswordform"]//label[@for="user_login"]' );

		if ( false !== $label_username_or_email_address && ! empty( $label_username_or_email_address[0] ) ) {
			$label_username_or_email_address            = $label_username_or_email_address[0];
			$label_username_or_email_address->nodeValue = esc_html( $config['label_username_or_email_address'] );
		}

		/**
		 * Remove labels if "show" is set to "false"
		 */
		if ( false === $config['show'] ) {
			$labels_to_remove = $xpath->query(
				'//form[@id="loginform"]//label[@for="user_login"] | '
				. '//form[@id="loginform"]//label[@for="user_pass"] | '
				. '//form[@id="registerform"]//label[@for="user_login"] | '
				. '//form[@id="registerform"]//label[@for="user_email"] | '
				. '//form[@id="lostpasswordform"]//label[@for="user_login"]'
			);

			if ( false !== $labels_to_remove ) {
				/** @var DOMElement $label */
				foreach ( $labels_to_remove as $label ) {
					$for = $label->getAttribute( 'for' );

					if ( '' !== $for ) {
						$input_nodes = $xpath->query( sprintf( '//input[@id="%s"]', $for ) );

						if ( false !== $input_nodes && ! empty( $input_nodes[0] ) ) {
							$input_nodes[0]->setAttribute( 'aria-label', esc_attr( $label->nodeValue ?? '' ) );
						}
					}

					if ( null !== $label->parentNode ) {
						$label->parentNode->removeChild( $label ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					}
				}
			}

			return $doc;
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Login_Form_Labels_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{font_family:string,font_size:string,font_weight:string,letter_case:string,line_height:float,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,show:bool,text_color:string,label_email:string,label_password:string,label_username:string,label_username_or_email_address:string} $results */
		$results = $fields_group->get_all_fields_values();

		$results['label_email']                     = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'label_email.%s', $this->locale ) ) ?? '' );
		$results['label_password']                  = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'label_password.%s', $this->locale ) ) ?? '' );
		$results['label_username']                  = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'label_username.%s', $this->locale ) ) ?? '' );
		$results['label_username_or_email_address'] = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'label_username_or_email_address.%s', $this->locale ) ) ?? '' );

		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Login_Form_Labels_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'.login label:not([for="language-switcher-locales"]) { color: %s; font-family: %s; font-size: %s; font-weight: %s; line-height: %.02fem; text-transform: %s; margin: %s; padding: %s; }',
				$this->styles->compose_color( $config['text_color'] ),
				$this->styles->get_font_family_name( $config['font_family'] ),
				$config['font_size'],
				$config['font_weight'],
				$config['line_height'],
				$config['letter_case'],
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
		];

		return $styles;
	}
}
