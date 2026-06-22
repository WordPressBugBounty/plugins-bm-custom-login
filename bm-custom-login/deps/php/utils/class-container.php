<?php
/**
 * Container controller class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Role;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Container" class
 */
abstract class Container {
	/**
	 * Capabilities version
	 *
	 * Used to determine whether the custom capabilities should
	 * be remapped for the user roles.
	 *
	 * @var string
	 */
	const CAPABILITIES_VERSION = 'v1';

	/**
	 * Request-scoped memo of cache group-version counters
	 *
	 * Shared across every {@see Cache} instance built from this container
	 * within a single request, so a version counter is read from the
	 * object cache at most once per request per group.
	 *
	 * @var array<string,int>
	 */
	protected array $cache_version_memo = [];

	/**
	 * Data prefix for the container settings
	 *
	 * @var string
	 */
	protected string $data_prefix;

	/**
	 * Instances of the container modules
	 *
	 * @var array<string,Module>
	 */
	protected array $instances = [];

	/**
	 * Whether the container is PRO
	 *
	 * @var bool
	 */
	protected bool $is_pro = false;

	/**
	 * Main directory of the container
	 *
	 * @var string
	 */
	protected string $main_dir;

	/**
	 * Main file path
	 *
	 * @var string
	 */
	protected string $main_file = '';

	/**
	 * Array of modules to register
	 *
	 * @var string[]
	 */
	protected array $modules = [];

	/**
	 * Container's name
	 *
	 * @var string
	 */
	protected string $name;

	/**
	 * Lazy-constructed post-meta registry for this container
	 *
	 * @var ?Post_Meta_Registry
	 */
	protected ?Post_Meta_Registry $post_meta_registry = null;

	/**
	 * Lazy-constructed runtime requirements checker
	 *
	 * @var ?Runtime_Requirements
	 */
	protected ?Runtime_Requirements $requirements = null;

	/**
	 * Container's slug
	 *
	 * @var string
	 */
	protected string $slug;

	/**
	 * Whether the container can be network-enabled or not
	 *
	 * @var bool
	 */
	protected bool $supports_network = false;

	/**
	 * Text domain
	 *
	 * @var string
	 */
	protected string $text_domain;

	/**
	 * Container type
	 *
	 * @var string
	 */
	protected string $type;

	/**
	 * Container's version
	 *
	 * @var string
	 */
	protected string $version;

	/**
	 * Register given array of modules to the list of known modules
	 *
	 * @param string[] $modules Array of modules to register.
	 *
	 * @return void
	 */
	public function register_modules( array $modules ): void {
		$this->modules = array_values(
			array_unique(
				array_merge(
					$this->modules,
					$modules,
				),
			),
		);
	}

	/**
	 * Deregister given array of modules from the list of known modules
	 *
	 * @param string[] $modules Array of modules to deregister.
	 *
	 * @return void
	 */
	public function deregister_modules( array $modules ): void {
		$this->modules = array_values(
			array_filter(
				$this->modules,
				function ( $known_module ) use ( $modules ): bool {
					return ! in_array( $known_module, $modules, true );
				},
			),
		);
	}

	/**
	 * Set the data prefix for the container settings
	 *
	 * @param string $data_prefix Data prefix for the container settings.
	 *
	 * @return void
	 */
	public function set_data_prefix( string $data_prefix ): void {
		$this->data_prefix = $data_prefix;
	}

	/**
	 * Set the "is PRO" flag
	 *
	 * @param bool $is_pro New value.
	 *
	 * @return void
	 */
	public function set_is_pro( bool $is_pro ): void {
		$this->is_pro = $is_pro;
	}

	/**
	 * Set main directory of the container
	 *
	 * @param string $main_dir Main directory of the container.
	 *
	 * @return void
	 */
	public function set_main_dir( string $main_dir ): void {
		$this->main_dir = $main_dir;
	}

	/**
	 * Declare an additional required PHP extension
	 *
	 * Adds to the baseline list of required extensions. Idempotent —
	 * declaring the same extension twice does not duplicate it.
	 *
	 * @param string $extension Extension name (e.g. 'dom').
	 *
	 * @return void
	 */
	public function add_required_extension( string $extension ): void {
		$this->get_requirements()->add_extension( $extension );
	}

	/**
	 * Get (lazy-construct) the runtime requirements checker for this container
	 *
	 * @return Runtime_Requirements Requirements checker tied to this container.
	 */
	protected function get_requirements(): Runtime_Requirements {
		if ( null === $this->requirements ) {
			$this->requirements = new Runtime_Requirements( $this );
		}

		return $this->requirements;
	}

	/**
	 * Read a memoized cache group-version counter for this request
	 *
	 * @param string $index Fully-qualified version-counter index.
	 *
	 * @return ?int Memoized counter, or null when this index has not been read yet.
	 */
	public function get_cache_version_memo( string $index ): ?int {
		return $this->cache_version_memo[ $index ] ?? null;
	}

	/**
	 * Store a cache group-version counter in this request's memo
	 *
	 * @param string $index Fully-qualified version-counter index.
	 * @param int    $value Counter value to memoize.
	 *
	 * @return void
	 */
	public function set_cache_version_memo( string $index, int $value ): void {
		$this->cache_version_memo[ $index ] = $value;
	}

	/**
	 * Get (lazy-construct) the post-meta registry for this container
	 *
	 * @return Post_Meta_Registry Registry scoped to this container.
	 */
	public function get_post_meta_registry(): Post_Meta_Registry {
		if ( null === $this->post_meta_registry ) {
			$this->post_meta_registry = new Post_Meta_Registry();
		}

		return $this->post_meta_registry;
	}

	/**
	 * Set container's name
	 *
	 * @param string $name Container's name.
	 *
	 * @return void
	 */
	public function set_name( string $name ): void {
		$this->name = $name;
	}

	/**
	 * Set container's slug
	 *
	 * @param string $slug Container's slug.
	 *
	 * @return void
	 */
	public function set_slug( string $slug ): void {
		$this->slug = $slug;
	}

	/**
	 * Set whether the container can be network-enabled
	 *
	 * @param bool $supports_network Whether the container can be network-enabled.
	 *
	 * @return void
	 */
	public function set_supports_network( bool $supports_network ): void {
		$this->supports_network = $supports_network;
	}

	/**
	 * Set the text domain
	 *
	 * @param string $text_domain Text domain.
	 *
	 * @return void
	 */
	public function set_text_domain( string $text_domain ): void {
		$this->text_domain = $text_domain;
	}

	/**
	 * Set container's version
	 *
	 * @param string $version Container's version.
	 *
	 * @return void
	 */
	public function set_version( string $version ): void {
		$this->version = $version;
	}

	/**
	 * Run a given method on all container modules
	 *
	 * @param string $method Method to call on all modules.
	 *
	 * @return void
	 */
	protected function for_all_modules( string $method ): void {
		foreach ( $this->modules as $module ) {
			if ( ! isset( $this->instances[ $module ] ) ) {
				/** @var Module $instance */
				$instance                   = new $module( $this );
				$this->instances[ $module ] = $instance;
			}

			if ( method_exists( $this->instances[ $module ], $method ) ) {
				$this->instances[ $module ]->$method();
			}
		}
	}

	/**
	 * Initialize the container
	 *
	 * @return void
	 */
	public function init(): void {
		/**
		 * Short-circuit before any module registration if declared runtime
		 * requirements (PHP version, WordPress version, required extensions)
		 * are not met, and hook an undismissable admin error notice instead.
		 */
		$requirements = $this->get_requirements();
		$requirements->check();

		if ( $requirements->has_failures() ) {
			$requirements->register_failure_notice();
			return;
		}

		// Register modules.
		$this->for_all_modules( 'register' );

		// Maybe update the capabilities.
		add_action( 'admin_init', [ $this, 'maybe_update_capabilities' ] );

		// Only plugins can be enabled network-wide.
		if ( 'plugin' === $this->get_type() ) {
			// Add the network-wide capabilities after the user is granted Super Admin privileges.
			add_action( 'granted_super_admin', [ $this, 'add_network_capabilities_to_user' ] );

			// Remove the network-wide capabilities after the user's Super Admin privileges are revoked.
			add_action( 'revoked_super_admin', [ $this, 'remove_network_capabilities_from_user' ] );
		}
	}

	/**
	 * Get the container's basename
	 *
	 * @return string Container's basename.
	 */
	abstract public function get_basename(): string;

	/**
	 * Get the WordPress capability gating activation/install of this container
	 *
	 * Used by Runtime_Requirements to decide who sees the unmet-requirements
	 * admin notice. Plugins return 'activate_plugins'; themes return
	 * 'switch_themes'.
	 *
	 * @return string Capability slug.
	 */
	abstract public function get_activate_capability(): string;

	/**
	 * Get the path to the JSON file with metadata definition for the block
	 *
	 * @param string $block The block's slug.
	 *
	 * @return string The block path.
	 */
	public function get_block_path( string $block ): string {
		return sprintf( '%1$s/build/%2$s', $this->main_dir, $block );
	}

	/**
	 * Get the data prefix for the container settings
	 *
	 * @return string Data prefix for the container settings.
	 */
	public function get_data_prefix(): string {
		return $this->data_prefix;
	}

	/**
	 * Get the "is PRO" flag value
	 *
	 * @return bool Value of the "is PRO" flag.
	 */
	public function get_is_pro(): bool {
		return $this->is_pro;
	}

	/**
	 * Get the main directory of the container
	 *
	 * @return string Main directory of the container.
	 */
	public function get_main_dir(): string {
		return $this->main_dir;
	}

	/**
	 * Get the main file of the container
	 *
	 * @return string Main file of the container.
	 */
	public function get_main_file(): string {
		return $this->main_file;
	}

	/**
	 * Get the URL to the container's directory
	 *
	 * @return string URL to the container's directory.
	 */
	abstract public function get_main_url(): string;

	/**
	 * Get the capability required for managing the
	 * container's settings
	 *
	 * @param string $scope Capability scope, either "network" for managing network options or "single" for managing single site options. Defaults to "detect", which means it will detect whether the container is running on the network level.
	 *
	 * @return string Capability required for managing the container's settings.
	 */
	public function get_managing_capability( $scope = 'detect' ): string {
		$for_network = 'network' === $scope || ( 'detect' === $scope && $this->is_network_enabled() );
		$capability  = $for_network
			? 'manage_network_options'
			: 'manage_options';

		return sprintf( '%1$s__%2$s', $this->get_data_prefix(), $capability );
	}

	/**
	 * Get the container's name
	 *
	 * @return string Container's name.
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the path to a file in the container directory
	 *
	 * @param string $file      File to get the path to.
	 * @param string $directory Directory the file is located at.
	 *
	 * @return string Path to the file in the container directory.
	 */
	public function get_path_to( string $file, string $directory = 'src/modules' ): string {
		return sprintf( '%1$s/%2$s/%3$s', $this->get_main_dir(), $directory, $file );
	}

	/**
	 * Get the container's slug
	 *
	 * @param bool $specific Whether to get the slug of the PRO container specifically, or the container in general.
	 *
	 * @return string Container's slug.
	 */
	public function get_slug( bool $specific = false ): string {
		return true === $specific && true === $this->get_is_pro()
			? sprintf( '%s-pro', $this->slug )
			: $this->slug;
	}

	/**
	 * Get whether the container can be network-enabled
	 *
	 * @return bool Whether the container can be network-enabled.
	 */
	public function get_supports_network(): bool {
		return $this->supports_network;
	}

	/**
	 * Get the container type
	 *
	 * @return string Container type.
	 */
	public function get_type(): string {
		return $this->type;
	}

	/**
	 * Get the container's text domain
	 *
	 * @return string Text domain.
	 */
	public function get_text_domain(): string {
		return $this->text_domain;
	}

	/**
	 * Get the URL to a file in the container directory
	 *
	 * @param string $file      File to get the URL to.
	 * @param string $directory Directory the file is located at.
	 *
	 * @return string URL to the file in the container directory.
	 */
	public function get_url_to( string $file, string $directory = 'src/modules' ): string {
		return sprintf( '%1$s/%2$s/%3$s', untrailingslashit( $this->get_main_url() ), $directory, $file );
	}

	/**
	 * Get the container's version
	 *
	 * @return string container's version.
	 */
	public function get_version(): string {
		return $this->version;
	}

	/**
	 * Determine if a given container is network-enabled
	 *
	 * @return bool Boolean "true" if container is network-enabled, "false" otherwise.
	 */
	public function is_network_enabled(): bool {
		return false;
	}

	/**
	 * Maybe update the capabilities
	 *
	 * @return void
	 */
	public function maybe_update_capabilities(): void {
		// The option key.
		$option_key = sprintf( '%s__capabilities_version', $this->get_data_prefix() );

		// Get the current capabilities version.
		$capabilities_version = get_option( $option_key, false );

		// Only proceed if capabilities has not yet been updated.
		if ( self::CAPABILITIES_VERSION !== $capabilities_version ) {
			/**
			 * Grant the capability to manage the container
			 * options for administrators
			 */
			$role = get_role( 'administrator' );

			if ( $role instanceof WP_Role ) {
				$role->add_cap( $this->get_managing_capability( 'single' ) );
			}

			/**
			 * Update the option so this will not be executed until
			 * the capabilities version changes in the future
			 */
			update_option( $option_key, self::CAPABILITIES_VERSION );
		}

		/**
		 * On network installations, grant special capability
		 * to manage the network-level container options
		 * for Super Admin users
		 */
		if ( is_multisite() && 'plugin' === $this->get_type() ) {
			// Get the current network ID.
			$network_id = get_current_network_id();

			// The option key.
			$network_option_key = sprintf( '%s__network_capabilities_version', $this->get_data_prefix() );

			// Get the current capabilities version.
			$network_capabilities_version = get_network_option( $network_id, $network_option_key, false );

			// Only proceed if capabilities has not yet been updated.
			if ( self::CAPABILITIES_VERSION !== $network_capabilities_version ) {
				$super_admins = get_super_admins();

				foreach ( $super_admins as $user_login ) {
					$user = get_user_by( 'login', $user_login );

					if ( $user instanceof WP_User ) {
						$user->add_cap( $this->get_managing_capability( 'network' ) );
					}
				}

				/**
				 * Update the option so this will not be executed until
				 * the capabilities version changes in the future
				 */
				update_network_option( $network_id, $network_option_key, self::CAPABILITIES_VERSION );
			}
		}
	}

	/**
	 * Add the network-wide capabilities after the user is granted Super Admin privileges
	 *
	 * @param int $user_id ID of the user to grant the capabilities to.
	 *
	 * @return void
	 */
	public function add_network_capabilities_to_user( int $user_id ): void {
		$user = get_user_by( 'ID', $user_id );

		if ( $user instanceof WP_User ) {
			$user->add_cap( $this->get_managing_capability( 'network' ) );
		}
	}

	/**
	 * Remove the network-wide capabilities after the user's Super Admin privileges are revoked
	 *
	 * @param int $user_id ID of the user to revoke the capabilities from.
	 *
	 * @return void
	 */
	public function remove_network_capabilities_from_user( int $user_id ): void {
		$user = get_user_by( 'ID', $user_id );

		if ( $user instanceof WP_User ) {
			$user->remove_cap( $this->get_managing_capability( 'network' ) );
		}
	}

	/**
	 * Run custom actions on each module during the container activation
	 *
	 * Aborts activation with wp_die() if declared runtime requirements
	 * are not met, so the plugin remains inactive afterwards.
	 *
	 * @return void
	 */
	public function on_activation(): void {
		$requirements = $this->get_requirements();
		$requirements->check();

		if ( $requirements->has_failures() ) {
			$requirements->abort_activation();
		}

		$this->maybe_update_capabilities();
		$this->for_all_modules( 'on_container_activation' );
	}

	/**
	 * Run custom actions on each module during the container deactivation
	 *
	 * @return void
	 */
	public function on_deactivation(): void {
		$this->for_all_modules( 'on_container_deactivation' );
	}
}
