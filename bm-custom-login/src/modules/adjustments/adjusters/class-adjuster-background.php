<?php
/**
 * Implement style and markup adjustments controlled by the "background" fields group
 *
 * @package Teydea_Studio\Custom_Login
 */

namespace Teydea_Studio\Custom_Login\Modules\Adjustments;

use Teydea_Studio\Custom_Login\Adjuster;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Adjuster_Background" class
 *
 * @phpstan-type Type_Adjuster_Background_Config ?array{color:string,focal_point_x:float,focal_point_y:float,media_id:int,size_repeat:string}
 */
final class Adjuster_Background extends Adjuster {
	/**
	 * The adjuster key
	 *
	 * @var string
	 */
	protected string $key = 'background';

	/**
	 * Collect adjuster-specific config
	 *
	 * @return ?Type_Adjuster_Background_Config Config array.
	 */
	protected function collect_config(): ?array {
		// Get the fields group.
		$fields_group = $this->settings->get_fields_group( $this->key );

		if ( null === $fields_group ) {
			return null;
		}

		/** @var array{color:string,focal_point_x:float,focal_point_y:float,media_id:int,size_repeat:string} $results */
		$results = $fields_group->get_all_fields_values();
		return $results;
	}

	/**
	 * Generate styles
	 *
	 * @return string[] Array of CSS styles.
	 */
	public function generate_styles(): array {
		/** @var Type_Adjuster_Background_Config $config */
		$config = $this->get_config();

		if ( null === $config ) {
			return [];
		}

		$styles = [
			sprintf(
				'body { background-color: %s; %s; }',
				$this->styles->compose_color( $config['color'], 'initial' ),
				$this->styles->compose_background_image_style(
					[
						'media_id'      => $config['media_id'],
						'focal_point_x' => $config['focal_point_x'],
						'focal_point_y' => $config['focal_point_y'],
						'size_repeat'   => $config['size_repeat'],
					],
				),
			),
		];

		return $styles;
	}
}
