<?php
/**
 * Styles controller
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login;

use Teydea_Studio\Custom_Login\Dependencies\Utils;
use WP_Theme_JSON_Resolver;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Styles" class
 *
 * @phpstan-type Type_Font_Face array{src:string|string[],fontWeight:string,fontStyle?:string,fontFamily:string}
 * @phpstan-type Type_Font_Family array{name?:string,slug:string,fontFamily:string,fontFace:?array<int,Type_Font_Face>}
 * @phpstan-type Type_Preconfigured_Styles array{color_palettes:?array<mixed>,font_families:array<string,array{name:string,slug:string,weights:string[]}>,font_sizes:?array<mixed>,gradient_palettes:?array<mixed>,shadow_presets:?array<mixed>,spacing_presets:?array<mixed>}
 */
class Styles {
	/**
	 * Flex alignment mapping helper
	 *
	 * @var array<string,string>
	 */
	const FLEX_ALIGNMENT = [
		'left'   => 'flex-start',
		'center' => 'center',
		'right'  => 'flex-end',
	];

	/**
	 * Container instance
	 *
	 * @var Utils\Container
	 */
	protected Utils\Container $container;

	/**
	 * Hold resolved preconfigured styles to avoid duplicated
	 * calls to the WP_Theme_JSON_Resolver
	 *
	 * @var ?Type_Preconfigured_Styles
	 */
	protected ?array $preconfigured_styles = null;

	/**
	 * Constructor
	 *
	 * @param Utils\Container $container Container instance.
	 */
	public function __construct( Utils\Container $container ) {
		$this->container = $container;
	}

	/**
	 * Compose background image style
	 *
	 * @param array{media_id:int,focal_point_x:float,focal_point_y:float,size_repeat:string} $data Background image config data.
	 *
	 * @return string Background image styles.
	 */
	public function compose_background_image_style( array $data ): string {
		if ( 0 === $data['media_id'] ) {
			return '';
		}

		$attachment = wp_get_attachment_image_src( $data['media_id'], 'full' );

		if ( false === $attachment ) {
			return '';
		}

		$background_size   = 'cover';
		$background_repeat = 'repeat';

		switch ( $data['size_repeat'] ) {
			case 'size-auto--no-repeat':
				$background_size   = 'auto';
				$background_repeat = 'no-repeat';
				break;
			case 'size-auto--repeat':
				$background_size   = 'auto';
				$background_repeat = 'repeat';
				break;
			case 'size-auto--repeat-round':
				$background_size   = 'auto';
				$background_repeat = 'round';
				break;
			case 'size-auto--repeat-space':
				$background_size   = 'auto';
				$background_repeat = 'space';
				break;
			case 'size-auto--repeat-x':
				$background_size   = 'auto';
				$background_repeat = 'repeat-x';
				break;
			case 'size-auto--repeat-y':
				$background_size   = 'auto';
				$background_repeat = 'repeat-y';
				break;
			case 'size-contain--no-repeat':
				$background_size   = 'contain';
				$background_repeat = 'no-repeat';
				break;
			case 'size-contain--repeat':
				$background_size   = 'contain';
				$background_repeat = 'repeat';
				break;
			case 'size-contain--repeat-x':
				$background_size   = 'contain';
				$background_repeat = 'repeat-x';
				break;
			case 'size-contain--repeat-y':
				$background_size   = 'contain';
				$background_repeat = 'repeat-y';
				break;
			case 'size-cover':
			default:
				// Keep the default values.
				break;
		}

		$style = sprintf(
			'background-image: url(%s); background-position: %s%% %s%%; background-size: %s; background-repeat: %s',
			esc_url( $attachment[0] ),
			$data['focal_point_x'] * 100,
			$data['focal_point_y'] * 100,
			$background_size,
			$background_repeat,
		);

		return $style;
	}

	/**
	 * Compose border style
	 *
	 * @param array{top_width:string,top_style:string,top_color:string,right_width:string,right_style:string,right_color:string,bottom_width:string,bottom_style:string,bottom_color:string,left_width:string,left_style:string,left_color:string} $data Border config data.
	 *
	 * @return string Border style.
	 */
	public function compose_border_style( array $data ): string {
		if ( '0px' === $data['top_width'] && '0px' === $data['right_width'] && '0px' === $data['bottom_width'] && '0px' === $data['left_width'] ) {
			return 'border: none';
		}

		$top_color    = empty( $data['top_color'] ) ? 'transparent' : $data['top_color'];
		$right_color  = empty( $data['right_color'] ) ? 'transparent' : $data['right_color'];
		$bottom_color = empty( $data['bottom_color'] ) ? 'transparent' : $data['bottom_color'];
		$left_color   = empty( $data['left_color'] ) ? 'transparent' : $data['left_color'];

		$border_top    = '0px' === $data['top_width'] ? 'none' : sprintf( '%s %s %s', $data['top_width'], $data['top_style'], $top_color );
		$border_right  = '0px' === $data['right_width'] ? 'none' : sprintf( '%s %s %s', $data['right_width'], $data['right_style'], $right_color );
		$border_bottom = '0px' === $data['bottom_width'] ? 'none' : sprintf( '%s %s %s', $data['bottom_width'], $data['bottom_style'], $bottom_color );
		$border_left   = '0px' === $data['left_width'] ? 'none' : sprintf( '%s %s %s', $data['left_width'], $data['left_style'], $left_color );

		if ( $border_top === $border_right && $border_right === $border_bottom && $border_bottom === $border_left ) {
			return sprintf( 'border: %s', $border_top );
		}

		return sprintf(
			'border-top: %s; border-right: %s; border-bottom: %s; border-left: %s',
			$border_top,
			$border_right,
			$border_bottom,
			$border_left,
		);
	}

	/**
	 * Compose border radius style
	 *
	 * @param string $bottom_left  Bottom left border radius.
	 * @param string $bottom_right Bottom right border radius.
	 * @param string $top_left     Top left border radius.
	 * @param string $top_right    Top right border radius.
	 *
	 * @return string Border radius style.
	 */
	public function compose_border_radius_style( string $bottom_left, string $bottom_right, string $top_left, string $top_right ): string {
		$bottom_left  = '' === $bottom_left ? '0px' : $bottom_left;
		$bottom_right = '' === $bottom_right ? '0px' : $bottom_right;
		$top_left     = '' === $top_left ? '0px' : $top_left;
		$top_right    = '' === $top_right ? '0px' : $top_right;

		if ( '0px' === $top_left && '0px' === $top_right && '0px' === $bottom_right && '0px' === $bottom_left ) {
			return 'border-radius: 0px';
		}

		if ( $top_left === $top_right && $top_right === $bottom_right && $bottom_right === $bottom_left ) {
			return sprintf( 'border-radius: %s', $top_left );
		}

		return sprintf(
			'border-radius: %s %s %s %s',
			$top_left,
			$top_right,
			$bottom_right,
			$bottom_left,
		);
	}

	/**
	 * Compose box shadow style
	 *
	 * @param string $shadow Box shadow style.
	 *
	 * @return string Box shadow style.
	 */
	public function compose_box_shadow_style( string $shadow ): string {
		if ( empty( $shadow ) ) {
			return 'none';
		}

		return $shadow;
	}

	/**
	 * Compose color
	 *
	 * @param string $color    Color value.
	 * @param string $fallback Fallback color value.
	 *
	 * @return string Color value.
	 */
	public function compose_color( string $color, string $fallback = 'inherit' ): string {
		if ( empty( $color ) ) {
			return $fallback;
		}

		return $color;
	}

	/**
	 * Get the font family name by its slug
	 *
	 * @param string $slug Font family slug.
	 *
	 * @return string Font family name, "inherit" if font is unknown.
	 */
	public function get_font_family_name( string $slug ): string {
		if ( 'default' === $slug ) {
			return 'inherit';
		}

		$preconfigured_styles = $this->get_preconfigured_styles();

		if ( empty( $preconfigured_styles['font_families'][ $slug ]['name'] ) ) {
			return 'inherit';
		}

		return sprintf( "'%s'", $preconfigured_styles['font_families'][ $slug ]['name'] );
	}

	/**
	 * Get known font families
	 *
	 * @param array{theme?:array<int,Type_Font_Family>,custom?:array<int,Type_Font_Family>} $font_families Font families data array.
	 *
	 * @return array<string,array{name:string,slug:string,weights:string[]}> Array of known fonts.
	 */
	protected function get_known_font_families( array $font_families ): array {
		$result = [
			'default' => [
				'name'    => __( 'Default', 'bm-custom-login' ),
				'slug'    => 'default',
				'weights' => [ '400' ],
			],
		];

		$to_process = [
			...( isset( $font_families['theme'] ) ? $font_families['theme'] : [] ),
			...( isset( $font_families['custom'] ) ? $font_families['custom'] : [] ),
		];

		foreach ( $to_process as $font_family ) {
			$slug    = isset( $font_family['name'] ) ? sanitize_title( $font_family['name'] ) : $font_family['slug'];
			$weights = array_map(
				/**
				 * Compose the font weight, considering the font style
				 *
				 * @param Type_Font_Face $font_face Font face to process.
				 *
				 * @return string Font weight.
				 */
				function ( array $font_face ): string {
					$weight = $font_face['fontWeight'];

					if ( isset( $font_face['fontStyle'] ) && 'italic' === $font_face['fontStyle'] ) {
						$weight .= $font_face['fontStyle'];
					}

					return $weight;
				},
				$font_family['fontFace'] ?? [],
			);

			/**
			 * Handle font weight ranges
			 */
			if ( 1 === count( $weights ) && preg_match( '/[0-9]{1}00 [0-9]{1}00/', $weights[0] ) ) {
				$range   = explode( ' ', $weights[0] );
				$weights = [];
				$current = absint( $range[0] );
				$max     = absint( $range[1] );

				while ( $current < $max ) {
					$weights[] = strval( $current );
					$current  += 100;
				}

				$weights[] = strval( $max );
			}

			if ( ! in_array( $slug, $result, true ) ) {
				$result[ $slug ] = [
					'name'    => isset( $font_family['name'] ) ? $font_family['name'] : $slug,
					'slug'    => $slug,
					'weights' => $weights,
				];
			} else {
				$result[ $slug ]['weights'] = array_values(
					array_unique(
						array_merge(
							is_array( $result[ $slug ]['weights'] ) ? $result[ $slug ]['weights'] : [], // @phpstan-ignore function.alreadyNarrowedType
							$weights,
						),
					),
				);
			}

			sort( $result[ $slug ]['weights'] );
		}

		return $result;
	}

	/**
	 * Get the preconfigured styles data array
	 *
	 * @return Type_Preconfigured_Styles Preconfigured styles data array.
	 */
	public function get_preconfigured_styles(): array {
		if ( null === $this->preconfigured_styles ) {
			// Get the theme.json data.
			$tree     = WP_Theme_JSON_Resolver::get_merged_data();
			$settings = $tree->get_settings();

			$this->preconfigured_styles = [
				'color_palettes'    => isset( $settings['color']['palette'] ) ? $settings['color']['palette'] : null,
				'font_families'     => $this->get_known_font_families( isset( $settings['typography']['fontFamilies'] ) ? $settings['typography']['fontFamilies'] : [] ),
				'font_sizes'        => isset( $settings['typography']['fontSizes'] ) ? $settings['typography']['fontSizes'] : null,
				'gradient_palettes' => isset( $settings['color']['gradients'] ) ? $settings['color']['gradients'] : null,
				'shadow_presets'    => isset( $settings['shadow']['presets'] ) ? $settings['shadow']['presets'] : null,
				'spacing_presets'   => isset( $settings['spacing']['spacingSizes'] ) ? $settings['spacing']['spacingSizes'] : null,
			];
		}

		return $this->preconfigured_styles;
	}

	/**
	 * Compose spacing
	 *
	 * @param array{top:string,right:string,bottom:string,left:string} $data Spacing config data.
	 *
	 * @return string Spacing style.
	 */
	public function compose_spacing( array $data ): string {
		$top    = '' === $data['top'] ? '0px' : $data['top'];
		$right  = '' === $data['right'] ? '0px' : $data['right'];
		$bottom = '' === $data['bottom'] ? '0px' : $data['bottom'];
		$left   = '' === $data['left'] ? '0px' : $data['left'];

		if ( '0px' === $top && '0px' === $right && '0px' === $bottom && '0px' === $left ) {
			return '0';
		}

		if ( $top === $right && $right === $bottom && $bottom === $left ) {
			return $top;
		}

		return sprintf(
			'%s %s %s %s',
			$top,
			$right,
			$bottom,
			$left,
		);
	}
}
