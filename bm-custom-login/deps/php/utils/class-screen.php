<?php
/**
 * Screen class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Screen;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Screen" class
 */
final class Screen {
	/**
	 * Container instance
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Current screen
	 *
	 * @var ?WP_Screen
	 */
	protected ?WP_Screen $current_screen = null;

	/**
	 * Constructor
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container ) {
		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . 'wp-admin/includes/screen.php';
		}

		$this->container      = $container;
		$this->current_screen = get_current_screen();
	}

	/**
	 * Check if a given screen is currently active
	 *
	 * @param string $screen_key Screen key to check.
	 * @param string $type       Type of the screen.
	 *
	 * @return bool Whether the screen with a given key is active or not.
	 */
	public function is( string $screen_key, string $type = '' ): bool {
		if ( ! $this->current_screen instanceof WP_Screen ) {
			return false;
		}

		$valid_id  = ! empty( $type ) ? sprintf( '%1$s_%2$s', $type, $screen_key ) : $screen_key;
		$valid_ids = [ $valid_id ];

		if ( $this->container->is_network_enabled() ) {
			$valid_ids[] = sprintf( '%s-network', $valid_id );
		}

		return in_array( $this->current_screen->id, $valid_ids, true );
	}

	/**
	 * Check if the current screen is a block editor
	 * for a specific post type
	 *
	 * @param string $post_type Expected post type.
	 *
	 * @return bool Whether the current screen is a block editor for a specific post type, or not.
	 */
	public function is_block_editor_and_post_type( string $post_type ): bool {
		if ( ! $this->current_screen instanceof WP_Screen ) {
			return false;
		}

		return $this->current_screen->is_block_editor && $post_type === $this->current_screen->post_type;
	}
}
