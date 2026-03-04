<?php
/**
 * User class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Session_Tokens;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "User" class
 */
class User {
	/**
	 * Container instance
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * User object, or null if the current user should be loaded
	 *
	 * @var ?WP_User
	 */
	protected ?WP_User $user;

	/**
	 * User ID
	 *
	 * @var ?int
	 */
	protected ?int $user_id = null;

	/**
	 * Constructor
	 *
	 * @param Container $container Container instance.
	 * @param ?WP_User  $user      User object, or null if the current user should be loaded.
	 */
	public function __construct( Container $container, ?WP_User $user = null ) {
		$this->container = $container;
		$this->user      = $user;

		if ( null === $this->user ) {
			$user = wp_get_current_user();

			if ( $user->exists() ) {
				$this->user = $user;
			}
		}

		if ( $this->user instanceof WP_User ) {
			$this->user_id = $this->user->ID;
		}
	}

	/**
	 * Add user meta
	 *
	 * @param string $meta_key     Metadata name.
	 * @param mixed  $meta_value   Metadata value. Must be serializable if non-scalar.
	 * @param bool   $network_wide Whether the user meta should apply network-wide.
	 * @param bool   $unique       Whether the same key should not be added.
	 *
	 * @return int|false Meta ID on success, false on failure.
	 */
	public function add_meta( string $meta_key, $meta_value, bool $network_wide = true, bool $unique = true ) {
		$user_id = $this->get_user_id();

		if ( null === $user_id ) {
			return false;
		}

		return add_user_meta(
			$user_id,
			$this->get_prefixed_meta_key( $meta_key, $network_wide ),
			$meta_value,
			$unique,
		);
	}

	/**
	 * Check whether the user has a specific capability
	 *
	 * @param string $capability Capability to check.
	 *
	 * @return bool Boolean "true" if user has a given capability, "false" otherwise.
	 */
	public function can( string $capability ): bool {
		if ( null === $this->get_user() ) {
			return false;
		}

		return user_can( $this->get_user(), $capability );
	}

	/**
	 * Delete user meta
	 *
	 * @param string $meta_key     Metadata name.
	 * @param bool   $network_wide Whether the network-wide or site-specific user meta should be deleted.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function delete_meta( string $meta_key, bool $network_wide = true ): bool {
		$user_id = $this->get_user_id();

		if ( null === $user_id ) {
			return false;
		}

		return delete_user_meta(
			$user_id,
			$this->get_prefixed_meta_key( $meta_key, $network_wide ),
		);
	}

	/**
	 * Add prefix to the user meta key, respecting network installations
	 *
	 * @param string $meta_key     Meta key to prefix.
	 * @param bool   $network_wide Whether the user meta should apply network-wide.
	 *
	 * @return string User meta key.
	 */
	public function get_prefixed_meta_key( string $meta_key, bool $network_wide = true ): string {
		// Add prefix for network installations.
		$meta_key = is_multisite() && false === $network_wide
			? sprintf(
				'%s:%d',
				$meta_key,
				get_current_blog_id(),
			)
			: $meta_key;

		// Return the value.
		return $meta_key;
	}

	/**
	 * Get the user object
	 *
	 * @return ?WP_User User object, or null if not set.
	 */
	public function get_user(): ?WP_User {
		return $this->user;
	}

	/**
	 * Get the user ID
	 *
	 * @return ?int User ID, or null if not set.
	 */
	public function get_user_id(): ?int {
		return $this->user_id;
	}

	/**
	 * Get the user login
	 *
	 * @return ?string User login, or null if not set.
	 */
	public function get_user_login(): ?string {
		return null === $this->get_user()
			? null
			: $this->get_user()->user_login;
	}

	/**
	 * Get the user meta value
	 *
	 * @param string $meta_key     The meta key to retrieve.
	 * @param bool   $network_wide Whether the user meta should apply network-wide.
	 *
	 * @return mixed User meta value.
	 */
	public function get_meta( string $meta_key, bool $network_wide = true ) {
		$user_id = $this->get_user_id();

		if ( null === $user_id ) {
			return null;
		}

		return get_user_meta(
			$user_id,
			$this->get_prefixed_meta_key( $meta_key, $network_wide ),
			true,
		);
	}

	/**
	 * Get the user meta value as array
	 *
	 * @param string $meta_key     The meta key to retrieve.
	 * @param bool   $network_wide Whether the user meta should apply network-wide.
	 *
	 * @return array<mixed>|array{} Array value of the user meta; if not found, defaults to empty array.
	 */
	public function get_meta_as_array( string $meta_key, bool $network_wide = true ): array {
		$value = $this->get_meta( $meta_key, $network_wide );

		return null === $value || ! is_array( $value )
			? []
			: $value;
	}

	/**
	 * Get the user meta value as boolean
	 *
	 * @param string $meta_key     The meta key to retrieve.
	 * @param bool   $network_wide Whether the user meta should apply network-wide.
	 *
	 * @return bool Boolean value of the user meta; if not found, defaults to "false".
	 */
	public function get_meta_as_boolean( string $meta_key, bool $network_wide = true ): bool {
		$value = $this->get_meta( $meta_key, $network_wide );

		return null === $value
			? false
			: Type::ensure_bool( $value );
	}

	/**
	 * Get the user meta value as integer
	 *
	 * @param string $meta_key     The meta key to retrieve.
	 * @param bool   $network_wide Whether the user meta should apply network-wide.
	 *
	 * @return int Integer value of the user meta; if not found, defaults to "0".
	 */
	public function get_meta_as_integer( string $meta_key, bool $network_wide = true ): int {
		$value = $this->get_meta( $meta_key, $network_wide );

		return null === $value
			? 0
			: Type::ensure_int( $value );
	}

	/**
	 * Get all roles that applies to the given user
	 *
	 * @return string[] Array of all user roles.
	 */
	public function get_user_roles(): array {
		if ( null === $this->get_user() ) {
			return [];
		}

		$roles = $this->get_user()->roles;

		if ( true === $this->container->is_network_enabled() && $this->is_super_admin() ) {
			$users   = new Users( $this->container );
			$roles[] = $users->get_network_super_admin_role_key();
		}

		return $roles;
	}

	/**
	 * Check if user is allowed to manage plugin options
	 *
	 * @return bool Boolean "true" if user is allowed to manage the plugin options, "false" otherwise.
	 */
	public function has_managing_permissions(): bool {
		if ( null === $this->get_user() ) {
			return false;
		}

		return $this->can(
			$this->container->get_managing_capability(),
		);
	}

	/**
	 * Check if user can manage (install and activate) plugins
	 *
	 * @return bool Boolean "true" if user is allowed to manage (install and activate) plugins, "false" otherwise.
	 */
	public function has_plugin_managing_permissions(): bool {
		return $this->can( 'install_plugins' ) && $this->can( 'activate_plugins' );
	}

	/**
	 * Check if user has the super-admin capabilities
	 *
	 * @return bool Boolean "true" if user has the super-admin capabilities, "false" otherwise.
	 */
	public function is_super_admin(): bool {
		return null !== $this->get_user_id() && is_super_admin( $this->get_user_id() );
	}

	/**
	 * Logout user everywhere and destroy all sessions
	 * associated with a user.
	 *
	 * @param bool $force_wp_logout Whether to use "wp_logout()" function to log out the current user regardless whether it's the user set in this class. Default is "false".
	 *
	 * @return void
	 */
	public function logout_everywhere( bool $force_wp_logout = false ): void {
		$user_id = $this->get_user_id();

		if ( null === $user_id ) {
			return;
		}

		$current_user = wp_get_current_user();

		// Use "wp_logout()" only if the user to log out is the current user.
		if ( $force_wp_logout || $user_id === $current_user->ID ) {
			wp_logout();
		}

		$sessions = WP_Session_Tokens::get_instance( $user_id );
		$sessions->destroy_all();
	}

	/**
	 * Set the user object
	 *
	 * @param WP_User $user User object.
	 *
	 * @return void
	 */
	public function set_user( WP_User $user ): void {
		$this->user    = $user;
		$this->user_id = $user->ID;
	}

	/**
	 * Update user meta
	 *
	 * @param string $meta_key     Metadata name.
	 * @param mixed  $meta_value   Metadata value. Must be serializable if non-scalar.
	 * @param bool   $network_wide Whether the user meta should apply network-wide.
	 *
	 * @return int|bool Meta ID if the key didn’t exist, true on successful update, false on failure or if the value passed to the function is the same as the one that is already in the database.
	 */
	public function update_meta( string $meta_key, $meta_value, bool $network_wide = true ) {
		$user_id = $this->get_user_id();

		if ( null === $user_id ) {
			return false;
		}

		return update_user_meta(
			$user_id,
			$this->get_prefixed_meta_key( $meta_key, $network_wide ),
			$meta_value,
		);
	}
}
