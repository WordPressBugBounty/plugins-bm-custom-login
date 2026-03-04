<?php
/**
 * Implement style and markup adjustments controlled by the "under_form_links" fields group
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Adjustments;

use DOMDocument;
use DOMXPath;
use Teydea_Studio\Custom_Login\Adjuster;
use Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Adjuster_Under_Form_Links" class
 *
 * @phpstan-type Type_Adjuster_Under_Form_Links_Config ?array{alignment:string,disable_back_link:bool,font_family:string,font_size:string,font_weight:string,letter_case:string,line_height:float,link_color:string,link_color_on_focus:string,link_color_on_hover:string,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,separator:string,separator_color:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,text_decoration:string,links_list:array<int,array{link:string,open_in_new_tab:bool,text:string}>}
 */
final class Adjuster_Under_Form_Links extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'under_form_links';

	/**
	 * Register hooks
	 *
	 * @return void
	 */
	public function register(): void {
		// Filter the login link separator.
		add_filter( 'login_link_separator', [ $this, 'filter_login_link_separator' ] );
	}

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Under_Form_Links_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		/**
		 * Disable the "Go to..." link
		 */
		if ( true === $config['disable_back_link'] ) {
			$link_tag = $xpath->query( '//p[@id="backtoblog"]' );

			if ( false !== $link_tag && ! empty( $link_tag[0] ) ) {
				$link_tag = $link_tag[0];

				// Remove the link from the DOM.
				$link_tag->parentNode->removeChild( $link_tag );
			}
		}

		/**
		 * Render additional footer links
		 */
		if ( ! empty( $config['links_list'] ) ) {
			$link_container = $xpath->query( '//p[@id="nav"]' );

			if ( false !== $link_container && ! empty( $link_container[0] ) ) {
				$link_container = $link_container[0];

				foreach ( $config['links_list'] as $link_data ) {
					// Create and append the separator element.
					$separator = $doc->createTextNode( sprintf( ' %s ', $config['separator'] ) );
					$link_container->appendChild( $separator );

					// Create a new link element.
					$link_element = $doc->createElement( 'a', esc_html( $link_data['text'] ) );
					$link_element->setAttribute( 'href', esc_url( $link_data['link'] ) );

					if ( true === $link_data['open_in_new_tab'] ) {
						$link_element->setAttribute( 'target', '_blank' );
						$link_element->setAttribute( 'rel', 'noopener noreferrer' );
					}

					// Append the link to the container.
					$link_container->appendChild( $link_element );
				}
			}
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return Type_Adjuster_Under_Form_Links_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{alignment:string,disable_back_link:bool,font_family:string,font_size:string,font_weight:string,letter_case:string,line_height:float,link_color:string,link_color_on_focus:string,link_color_on_hover:string,margin_bottom:string,margin_left:string,margin_right:string,margin_top:string,padding_bottom:string,padding_left:string,padding_right:string,padding_top:string,separator:string,separator_color:string,shadow:string,shadow_on_focus:string,shadow_on_hover:string,text_decoration:string} $results */
		$results = $fields_group->get_all_fields_values();
		unset( $fields_group );

		/**
		 * Get the additional footer links
		 */
		$results['links_list'] = [];

		$fields_group = $this->settings->get_fields_group( sprintf( '%s_list', $this->key ) );
		$links        = null !== $fields_group ? $fields_group->get_fields() : [];

		/** @var Validatable_Fields\Fields_Group $link */
		foreach ( $links as $link ) {
			/** @var array{link:string,open_in_new_tab:bool,text:string} $link_data */
			$link_data = [
				'link'            => $link->get_field_value( 'link' ),
				'open_in_new_tab' => $link->get_field_value( 'open_in_new_tab' ),
				'text'            => $link->get_field_value( 'text' ),
			];

			if ( empty( $link_data['text'] ) || empty( $link_data['link'] ) ) {
				continue;
			}

			$results['links_list'][] = $link_data;
		}

		return $results;
	}

	/**
	 * Filter the login link separator
	 *
	 * @param string $separator The separator used between login form navigation links.
	 *
	 * @return string The modified separator.
	 */
	public function filter_login_link_separator( string $separator ): string {
		/** @var Type_Adjuster_Under_Form_Links_Config $config */
		$config = $this->get_config();

		if ( null !== $config && ! empty( $config['separator'] ) ) {
			$separator = sprintf( ' %s ', $config['separator'] );
		}

		return $separator;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Under_Form_Links_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'.login #nav, .login #backtoblog { color: %s; font-family: %s; font-size: %s; font-weight: %s; line-height: %.02fem; text-align: %s; text-transform: %s; margin: %s; padding: %s; }',
				$this->styles->compose_color( $config['separator_color'] ),
				$this->styles->get_font_family_name( $config['font_family'] ),
				$config['font_size'],
				$config['font_weight'],
				$config['line_height'],
				$config['alignment'],
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
			sprintf(
				'.login #nav a, .login #backtoblog a { box-shadow: %s; color: %s; text-decoration: %s; }',
				$this->styles->compose_box_shadow_style( $config['shadow'] ),
				$this->styles->compose_color( $config['link_color'] ),
				$config['text_decoration'],
			),
			sprintf(
				'.login #nav a:hover, .login #backtoblog a:hover { box-shadow: %s; color: %s; }',
				$this->styles->compose_box_shadow_style( $config['shadow_on_hover'] ),
				$this->styles->compose_color( $config['link_color_on_hover'] ),
			),
			sprintf(
				'.login #nav a:focus, .login #backtoblog a:focus { box-shadow: %s; color: %s; }',
				$this->styles->compose_box_shadow_style( $config['shadow_on_focus'] ),
				$this->styles->compose_color( $config['link_color_on_focus'] ),
			),
		];

		return $styles;
	}
}
