<?php
/**
 * Post meta registry
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Post;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Post_Meta_Registry" class
 *
 * Stores Post_Meta instances keyed by post type, providing
 * a centralized way for any module to access them. One registry is
 * scoped to each container; obtain it via
 * {@see Container::get_post_meta_registry()} rather than constructing
 * it directly, so every module in a plugin shares the same instance.
 */
final class Post_Meta_Registry {
	/**
	 * Registered Post_Meta instances, keyed by post type
	 *
	 * @var array<string,Post_Meta>
	 */
	private array $instances = [];

	/**
	 * Register a Post_Meta instance for a post type
	 *
	 * @param string    $post_type Post type slug.
	 * @param Post_Meta $post_meta Post_Meta instance.
	 *
	 * @return void
	 */
	public function register( string $post_type, Post_Meta $post_meta ): void {
		$this->instances[ $post_type ] = $post_meta;
	}

	/**
	 * Get Post_Meta instance for a given post
	 *
	 * @param WP_Post $post Post instance.
	 *
	 * @return ?Post_Meta Post_Meta instance, or null if not registered.
	 */
	public function for_post( WP_Post $post ): ?Post_Meta {
		return $this->instances[ $post->post_type ] ?? null;
	}

	/**
	 * Get Post_Meta instance for a given post type
	 *
	 * @param string $post_type Post type slug.
	 *
	 * @return ?Post_Meta Post_Meta instance, or null if not registered.
	 */
	public function for_post_type( string $post_type ): ?Post_Meta {
		return $this->instances[ $post_type ] ?? null;
	}

	/**
	 * Get Post_Meta instance for a given post ID
	 *
	 * Looks up the post's type and returns the corresponding Post_Meta instance.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return ?Post_Meta Post_Meta instance, or null if not registered.
	 */
	public function for_post_id( int $post_id ): ?Post_Meta {
		$post_type = get_post_type( $post_id );

		if ( ! is_string( $post_type ) ) {
			return null;
		}

		return $this->instances[ $post_type ] ?? null;
	}
}
