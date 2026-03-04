<?php
/**
 * Implement style and markup adjustments controlled by the "logo" fields group
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
 * The "Adjuster_Logo" class
 *
 * @phpstan-type Type_Adjuster_Logo_Config ?array{alignment:string,as_link:bool,link:string,link_title:string,media_id:int,open_in_new_tab:bool,show:bool,strict_width:int,logo_source:string}
 */
final class Adjuster_Logo extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'logo';

	/**
	 * Apply the markup adjustments
	 *
	 * @param DOMDocument $doc   DOMDocument object.
	 * @param DOMXPath    $xpath XPath object.
	 *
	 * @return DOMDocument DOMDocument object.
	 */
	public function apply_markup_adjustments( DOMDocument $doc, DOMXPath $xpath ): DOMDocument {
		/** @var Type_Adjuster_Logo_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return $doc;
		}

		// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- DOMDocument properties are in snake case.

		/**
		 * Markup customizations for the logo element
		 */
		$logo_tag = $xpath->query( '//h1[contains(@class,"wp-login-logo")]' );

		if ( false !== $logo_tag && ! empty( $logo_tag[0] ) ) {
			$logo_tag = $logo_tag[0];

			if ( true === $config['show'] ) {
				// Find the logo link element.
				$logo_link_tag = $xpath->query( 'a', $logo_tag );

				if ( false !== $logo_link_tag && ! empty( $logo_link_tag[0] ) ) {
					$logo_link_tag = $logo_link_tag[0];

					// Remove the default element as it relies on CSS "background-image".
					$logo_link_tag->parentNode->removeChild( $logo_link_tag );
				}

				// Create logo image element.
				$logo_image = false;

				if ( 'core' === $config['logo_source'] ) {
					// Use default WordPress logo.
					$logo_image = $doc->createElement( 'img' );

					if ( false !== $logo_image ) {
						$logo_image->setAttribute( 'src', esc_url( admin_url( '/images/wordpress-logo.svg?ver=20131107' ) ) );
						$logo_image->setAttribute( 'width', '84px' );
					}
				} elseif ( 'site_icon' === $config['logo_source'] || 'custom' === $config['logo_source'] ) {
					// Use site icon or custom logo.
					$attachment_id = Utils\Type::ensure_int(
						'site_icon' === $config['logo_source']
							? get_option( 'site_icon', 0 )
							: $config['media_id']
					);

					if ( 0 !== $attachment_id ) {
						$attachment = wp_get_attachment_image_src( $attachment_id );

						if ( false !== $attachment ) {
							$logo_image = $doc->createElement( 'img' );

							if ( false !== $logo_image ) {
								$logo_image->setAttribute( 'src', esc_url( $attachment[0] ) );

								if ( 0 === $config['strict_width'] ) {
									// Use original image dimensions.
									$logo_image->setAttribute( 'width', esc_attr( Utils\Type::ensure_string( $attachment[1] ) ) );
									$logo_image->setAttribute( 'height', esc_attr( Utils\Type::ensure_string( $attachment[2] ) ) );
								} else {
									// Use strict width.
									$logo_image->setAttribute( 'width', esc_attr( Utils\Type::ensure_string( $config['strict_width'] ) ) );
								}
							}
						}
					}
				}

				// Only continue if logo image element is built.
				if ( false !== $logo_image ) {
					// Should we wrap a logo image with a link?
					if ( true === $config['as_link'] ) {
						$logo_link = $doc->createElement( 'a' );

						if ( false !== $logo_link ) {
							$logo_link->setAttribute( 'href', esc_url( $config['link'] ) );
							$logo_link->setAttribute( 'title', esc_attr( $config['link_title'] ) );

							// Should open in new tab?
							if ( true === $config['open_in_new_tab'] ) {
								$logo_link->setAttribute( 'target', '_blank' );
								$logo_link->setAttribute( 'rel', 'noopener' );
							}

							$logo_link->appendChild( $logo_image );
							$logo_tag->appendChild( $logo_link );
						}
					} else {
						$logo_tag->appendChild( $logo_image );
					}
				}
			} else {
				$logo_tag->parentNode->removeChild( $logo_tag );
			}
		}

		// phpcs:enable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

		return $doc;
	}

	/**
	 * Collect adjuster-specific config
	 *
	 * @return ?Type_Adjuster_Logo_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{alignment:string,as_link:bool,link:string,link_title:string,media_id:int,open_in_new_tab:bool,show:bool,strict_width:int,logo_source:string} $results */
		$results               = $fields_group->get_all_fields_values();
		$results['link_title'] = Utils\Type::ensure_string( $fields_group->get_field_value( sprintf( 'link_title.%s', $this->locale ) ) ?? '' );

		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Logo_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [];

		if ( true === $config['show'] ) {
			$styles[] = sprintf( '.login h1.wp-login-logo { display: flex; justify-content: %s; margin-bottom: 24px; }', $this->styles::FLEX_ALIGNMENT[ $config['alignment'] ] );
			$styles[] = '.login h1.wp-login-logo a { all: unset; cursor: pointer; font-size: 0; margin: initial; }';
		}

		return $styles;
	}
}
