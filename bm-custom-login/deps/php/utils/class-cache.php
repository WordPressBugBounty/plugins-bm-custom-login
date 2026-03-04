<?php
/**
 * Cache class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Post;
use WP_Site;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Cache" class
 */
final class Cache {
	/**
	 * Container instance
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Resource to handle cache for
	 *
	 * @var ?string
	 */
	protected ?string $resource = null;

	/**
	 * Group
	 *
	 * @var string
	 */
	protected string $group = '';

	/**
	 * One-of key
	 *
	 * @var ?string
	 */
	protected ?string $key = null;

	/**
	 * Post object
	 *
	 * @var ?WP_Post
	 */
	protected ?WP_Post $post = null;

	/**
	 * User object
	 *
	 * @var ?WP_User
	 */
	protected ?WP_User $user = null;

	/**
	 * File path
	 *
	 * @var ?string
	 */
	protected ?string $file = null;

	/**
	 * Constructor
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Set the cache group
	 *
	 * @param string $group Cache group.
	 *
	 * @return void
	 */
	public function set_group( string $group ): void {
		$this->group = $group;
	}

	/**
	 * Set the one-of key
	 *
	 * @param string $key One-of key.
	 *
	 * @return void
	 */
	public function set_key( string $key ): void {
		if ( null === $this->resource ) {
			$this->resource = 'key';
			$this->key      = $key;
		}
	}

	/**
	 * Set the post object
	 *
	 * @param WP_Post $post The post object.
	 *
	 * @return void
	 */
	public function set_post( WP_Post $post ): void {
		if ( null === $this->resource ) {
			$this->resource = 'post';
			$this->post     = $post;
		}
	}

	/**
	 * Set the user object
	 *
	 * @param WP_User $user The user object.
	 *
	 * @return void
	 */
	public function set_user( WP_User $user ): void {
		if ( null === $this->resource ) {
			$this->resource = 'user';
			$this->user     = $user;
		}
	}

	/**
	 * Set the file path
	 *
	 * @param string $file File path.
	 *
	 * @return void
	 */
	public function set_file( string $file ): void {
		if ( null === $this->resource ) {
			$this->resource = 'file';
			$this->file     = $file;
		}
	}

	/**
	 * Get the cache tokens (key and group)
	 *
	 * @return ?array{key:string,group:string} Cache tokens array (key and group); null if resource has not been provided.
	 */
	public function get_cache_tokens(): ?array {
		if ( null === $this->resource ) {
			return null;
		}

		$tokens = [
			'key'   => '',
			'group' => sprintf( '%1$s:%2$s', $this->container->get_data_prefix(), $this->group ),
		];

		switch ( $this->resource ) {
			/**
			 * One-of key
			 */
			case 'key':
				if ( null !== $this->key ) {
					$tokens['key'] = sprintf( 'key:%s', $this->key );
				}

				break;

			/**
			 * Post cache key
			 */
			case 'post':
				if ( null !== $this->post ) {
					$tokens['key'] = sprintf( 'post:%d', $this->post->ID );
				}

				break;

			/**
			 * User cache key
			 */
			case 'user':
				if ( null !== $this->user ) {
					$tokens['key'] = sprintf( 'user:%d', $this->user->ID );
				}

				break;

			/**
			 * File cache key
			 */
			case 'file':
				if ( null !== $this->file ) {
					$modified_at = filemtime( $this->file );

					if ( false === $modified_at ) {
						$modified_at = 0;
					}

					$tokens['key'] = sprintf( 'file:%1$s:%2$d', md5( $this->file ), $modified_at );
				}

				break;
		}

		// Extend key with container version to ensure cache invalidation on container updates.
		if ( ! empty( $tokens['key'] ) ) {
			$tokens['key'] = sprintf( '%1$s:%2$s', $tokens['key'], $this->container->get_version() );
		}

		return empty( $tokens['key'] ) ? null : $tokens;
	}

	/**
	 * Read the data from cache
	 *
	 * @return mixed|false The cache contents on success, false on failure to retrieve contents.
	 */
	public function read() {
		$tokens = $this->get_cache_tokens();
		return null === $tokens ? false : wp_cache_get( $tokens['key'], $tokens['group'] );
	}

	/**
	 * Write data into cache
	 *
	 * @param mixed $data   The contents to store in the cache.
	 * @param int   $expire When to expire the cache contents, in seconds; default 0 (no expiration).
	 *
	 * @return bool True on success, false on failure.
	 */
	public function write( $data, int $expire = 0 ): bool {
		$tokens = $this->get_cache_tokens();
		return null === $tokens ? false : wp_cache_set( $tokens['key'], $data, $tokens['group'], $expire );
	}

	/**
	 * Delete data from cache
	 *
	 * @return bool True on successful removal, false on failure.
	 */
	public function delete(): bool {
		$tokens = $this->get_cache_tokens();
		return null === $tokens ? false : wp_cache_delete( $tokens['key'], $tokens['group'] );
	}

	/**
	 * Delete data from cache for all blogs in network
	 *
	 * @return bool True on successful removal, false on failure.
	 */
	public function delete_network_wide(): bool {
		if ( false === $this->container->is_network_enabled() ) {
			return $this->delete();
		}

		/** @var WP_Site $site */
		foreach ( get_sites() as $site ) {
			switch_to_blog( Type::ensure_int( $site->blog_id ) );
			$this->delete();
			restore_current_blog();
		}

		return true;
	}
}
