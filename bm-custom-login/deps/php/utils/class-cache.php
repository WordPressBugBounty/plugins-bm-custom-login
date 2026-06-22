<?php
/**
 * Cache class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Post;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Cache" class
 */
final class Cache {
	/**
	 * `wp_cache` group suffix where version counters are stored
	 *
	 * Joined with the container's data prefix to form
	 * `{data_prefix}:cache_versions`. Lives outside the groups it
	 * controls — otherwise reading a counter would recurse through
	 * {@see get_group_version()}.
	 *
	 * Inside this group, each versioned cache group's counter is
	 * stored under a `wp_cache` key matching the group's own name —
	 * e.g. group `findings`'s counter lives at
	 * `wp_cache_get('findings', '{data_prefix}:cache_versions')`.
	 *
	 * @var string
	 */
	protected const VERSION_STORAGE_GROUP = 'cache_versions';

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
	 * Whether this instance's keys participate in group-version invalidation
	 *
	 * @var bool
	 */
	protected bool $group_versioning_enabled = false;

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
	 * Opt this instance's keys into group-version invalidation
	 *
	 * When enabled, the resolved cache group is suffixed with the
	 * current group-version counter, so a single
	 * {@see bump_group_version()} call invalidates every cached key in
	 * the group at once — no per-key delete list to maintain. Reads
	 * cost one extra `wp_cache_get()` for the version counter, which
	 * is request-memoized so the cost is paid at most once per
	 * request per group.
	 *
	 * @return void
	 */
	public function enable_group_versioning(): void {
		$this->group_versioning_enabled = true;
	}

	/**
	 * Read the current group-version counter
	 *
	 * Returns `1` when the counter has never been written. The counter
	 * is fetched at most once per request per group (memoized on the
	 * container) so frequently-accessed groups don't pay repeated
	 * `wp_cache_get()` costs.
	 *
	 * @return int Current version (always >= 1).
	 */
	public function get_group_version(): int {
		$storage_group = $this->get_version_storage_group();
		$memo_index    = sprintf( '%1$s/%2$s', $storage_group, $this->group );
		$memoized      = $this->container->get_cache_version_memo( $memo_index );

		if ( null === $memoized ) {
			// The counter for this group lives in wp_cache at `key=$this->group, group=$storage_group`.
			$value    = wp_cache_get( $this->group, $storage_group );
			$memoized = ( is_int( $value ) && $value > 0 ) ? $value : 1;

			$this->container->set_cache_version_memo( $memo_index, $memoized );
		}

		return $memoized;
	}

	/**
	 * Increment the group-version counter
	 *
	 * Atomically invalidates every cached key in the group that
	 * participates in versioning (i.e. every reader that called
	 * {@see enable_group_versioning()}). Old keys at the previous
	 * version remain in cache but become unreachable; they expire on
	 * TTL or get evicted by the object cache.
	 *
	 * Updates the request-scoped memo as well so a writer that bumps
	 * mid-request sees its own new version on subsequent reads
	 * without a fresh `wp_cache_get()`.
	 *
	 * @return int New version after the bump.
	 */
	public function bump_group_version(): int {
		$next          = $this->get_group_version() + 1;
		$storage_group = $this->get_version_storage_group();
		$memo_index    = sprintf( '%1$s/%2$s', $storage_group, $this->group );

		wp_cache_set( $this->group, $next, $storage_group );
		$this->container->set_cache_version_memo( $memo_index, $next );

		return $next;
	}

	/**
	 * Build the `wp_cache` group string under which version counters live
	 *
	 * @return string Storage group for version counters.
	 */
	protected function get_version_storage_group(): string {
		return sprintf( '%1$s:%2$s', $this->container->get_data_prefix(), self::VERSION_STORAGE_GROUP );
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

		$group = sprintf( '%1$s:%2$s', $this->container->get_data_prefix(), $this->group );

		/**
		 * When versioning is enabled, suffix the resolved group with
		 * `:gv{N}` so a `bump_group_version()` call automatically
		 * routes future reads to a new group namespace, leaving the
		 * previous generation of keys unreachable (and eligible for
		 * LRU eviction / TTL expiry).
		 */
		if ( $this->group_versioning_enabled ) {
			$group = sprintf( '%1$s:gv%2$d', $group, $this->get_group_version() );
		}

		$tokens = [
			'key'   => '',
			'group' => $group,
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
					$modified_at = File::get_modified_time( $this->file );

					if ( false === $modified_at ) {
						$modified_at = 0;
					}

					$tokens['key'] = sprintf( 'file:%1$s:%2$d', md5( $this->file ), $modified_at );
				}

				break;
		}

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

		/** @var array<int,int|string> $blog_ids */
		$blog_ids = get_sites(
			[
				'fields' => 'ids',
				'number' => 0,
			],
		);

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( Type::ensure_int( $blog_id ) );
			$this->delete();
			restore_current_blog();
		}

		return true;
	}
}
