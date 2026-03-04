<?php
/**
 * Users class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Site;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Users" class
 */
class Users {
	/**
	 * Cache group
	 *
	 * @var string
	 */
	const CACHE_GROUP = 'users';

	/**
	 * Cache key for "roles" storage
	 *
	 * @var string
	 */
	const CACHE_KEY__ROLES = 'roles';

	/**
	 * Container instance
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Constructor
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Calculate user coverage
	 *
	 * @param bool     $include_all          Whether the all users should be included, or not.
	 * @param string[] $roles                Array of user roles to include.
	 * @param int[]    $users                Array of user IDs to include.
	 * @param bool     $exclude_current_user Whether to exclude the current user from the coverage count.
	 *
	 * @return array{count:int,count_formatted:string,coverage:float} User coverage data.
	 */
	public function calculate_coverage( bool $include_all, array $roles, array $users, bool $exclude_current_user = false ): array {
		global $wpdb;

		$coverage    = 0;
		$total_users = 0;

		/**
		 * Get the total number of users depending
		 * on the site type (single vs network)
		 */
		if ( true === $this->container->is_network_enabled() ) {
			$total_users = Type::ensure_int( $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->users" ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		} else {
			$users_count = count_users();
			$total_users = $users_count['total_users'];

			unset( $users_count );
		}

		/**
		 * Calculate how many users will be covered
		 */
		if ( true === $include_all ) {
			// Note: current user may be excluded from processing.
			$coverage = $exclude_current_user ? $total_users - 1 : $total_users;
		} else {
			$coverage = count( $this->get_users_batch( $include_all, $roles, $users, null, null, $exclude_current_user ) );
		}

		// Calculate coverage and return.
		return [
			'count'           => $coverage,
			'count_formatted' => number_format_i18n( $coverage ),
			'coverage'        => $total_users > 0 ? round( $coverage / $total_users, 4 ) : 0.0,
		];
	}

	/**
	 * Get list of user roles known by a given site or a whole network
	 *
	 * @return array<int,array{value:string,title:string,id:int}> Array of known user roles.
	 */
	public function get_known_user_roles(): array {
		$cache = new Cache( $this->container );

		$cache->set_group( self::CACHE_GROUP );
		$cache->set_key( self::CACHE_KEY__ROLES );

		/** @var false|array<int,array{value:string,title:string,id:int}> $data */
		$data = $cache->read();

		if ( false === $data ) {
			$roles = wp_roles();
			$data  = [];

			foreach ( $roles->role_names as $role => $display_name ) {
				$data[] = [
					'id'    => count( $data ),
					'value' => $role,
					'title' => $display_name,
				];
			}

			if ( true === $this->container->is_network_enabled() ) {
				$current_blog_id = get_current_blog_id();

				/**
				 * Include roles from other sub-sites
				 *
				 * @var WP_Site $site
				 */
				foreach ( get_sites() as $site ) {
					// Skip the current blog as it was already processed.
					if ( Type::ensure_int( $site->blog_id ) === $current_blog_id ) {
						continue;
					}

					switch_to_blog( Type::ensure_int( $site->blog_id ) );

					// Get roles for the current site after switching.
					$site_roles = wp_roles();

					foreach ( $site_roles->role_names as $role => $display_name ) {
						// Add unique roles only.
						if ( in_array( $role, array_column( $data, 'value' ), true ) ) {
							continue;
						}

						$data[] = [
							'id'    => count( $data ),
							'value' => $role,
							'title' => $display_name,
						];
					}

					restore_current_blog();
				}

				/**
				 * Include the "Super Admin" role.
				 *
				 * This is a "fake" role, not included in the user roles
				 * array. It will be recognized with custom logic and
				 * applied only to users who has the network-wide
				 * admin capabilities.
				 */
				$data[] = [
					'id'    => count( $data ), // Ensure a unique ID.
					'value' => $this->get_network_super_admin_role_key(),
					'title' => __( 'Network Super Admin', 'bm-custom-login' ),
				];
			}

			foreach ( $data as &$role_data ) {
				// Ensure that ID starts with "1".
				$role_data['id']   += 1;
				$role_data['title'] = sprintf(
					'%1$s ("%2$s")',
					$role_data['title'],
					$role_data['value'],
				);
			}

			$cache->write( $data );
		}

		return $data;
	}

	/**
	 * Get the Network Super Admin role key
	 *
	 * @return string Network Super Admin role key.
	 */
	public function get_network_super_admin_role_key(): string {
		return sprintf( '%s:network-super-admin', $this->container->get_data_prefix() );
	}

	/**
	 * Get users batch
	 *
	 * @param bool     $include_all          Whether the all users should be fetch, or by role or specific ID.
	 * @param string[] $roles                User roles to include in query.
	 * @param int[]    $users                User IDs to include in query.
	 * @param ?int     $limit                Limit of users per batch, null if all results should be returned.
	 * @param ?int     $paged                Paged number, null if all results should be returned.
	 * @param bool     $exclude_current_user Whether to exclude the current user from the results.
	 *
	 * @return int[] Array of user IDs.
	 */
	public function get_users_batch( bool $include_all, array $roles, array $users, ?int $limit = null, ?int $paged = null, bool $exclude_current_user = false ): array {
		$current_user = wp_get_current_user();

		// If current user is not logged in and we're not running via WP-CLI, return empty array.
		if ( 0 === $current_user->ID && ! Environment::is_wp_cli_request() ) {
			return [];
		}

		$results = [];

		if ( true === $this->container->is_network_enabled() ) {
			/**
			 * Loop through all network sites
			 *
			 * @var WP_Site $site
			 */
			foreach ( get_sites() as $site ) {
				switch_to_blog( Type::ensure_int( $site->blog_id ) );

				$results = array_merge(
					$results,
					$this->get_site_users( $include_all, $roles, $users ),
				);

				restore_current_blog();
			}

			$results = array_unique( $results, SORT_NUMERIC );
		} else {
			$results = $this->get_site_users( $include_all, $roles, $users );
		}

		sort( $results, SORT_NUMERIC );

		// Exclude current user if required.
		if ( true === $exclude_current_user ) {
			$results = array_filter( $results, fn ( int $user_id ): bool => $user_id !== $current_user->ID );
		}

		// Apply limit and pagination if required.
		if ( null !== $limit && null !== $paged ) {
			$results = array_slice( $results, ( $paged - 1 ) * $limit, $limit );
		} elseif ( null !== $limit ) {
			$results = array_slice( $results, 0, $limit );
		}

		return array_values( $results );
	}

	/**
	 * Get user IDs from a single site based on
	 * a given criteria
	 *
	 * @param bool     $include_all Whether the all users should be fetch, or by role or specific ID.
	 * @param string[] $roles       User roles to include in query.
	 * @param int[]    $users       User IDs to include in query.
	 *
	 * @return int[] Array of user IDs.
	 */
	protected function get_site_users( bool $include_all, array $roles, array $users ): array {
		if ( false === $include_all && empty( $roles ) && empty( $users ) ) {
			return [];
		}

		$results = [];

		if ( true === $include_all ) {
			$results = get_users( [ 'fields' => 'ID' ] );
		} else {
			if ( ! empty( $roles ) ) {
				$results = array_merge(
					$results,
					get_users(
						[
							'role__in' => $roles,
							'fields'   => 'ID',
						],
					),
				);

				/**
				 * Do we need to include Network Super Admins?
				 */
				if ( true === $this->container->is_network_enabled() && in_array( $this->get_network_super_admin_role_key(), $roles, true ) ) {
					$super_admins = get_super_admins();

					if ( ! empty( $super_admins ) ) {
						$results = array_merge(
							$results,
							get_users(
								[
									'include' => $this->maybe_map_user_logins_to_user_ids( $super_admins ),
									'fields'  => 'ID',
								],
							),
						);
					}
				}
			}

			if ( ! empty( $users ) ) {
				$results = array_merge(
					$results,
					get_users(
						[
							'include' => $users,
							'fields'  => 'ID',
						],
					),
				);
			}
		}

		$results = Type::ensure_array_of_ints( $results );
		return array_unique( $results, SORT_NUMERIC );
	}

	/**
	 * Maybe map array of user logins to array of user IDs (if the given
	 * values are user logins; otherwise keep as IDs)
	 *
	 * @param int[]|string[] $users Array of user logins or user IDs.
	 *
	 * @return int[] Array of user IDs.
	 */
	public function maybe_map_user_logins_to_user_ids( array $users ): array {
		$user_ids = array_map(
			function ( $user_login ): ?int {
				if ( Type::is_positive_int( $user_login ) ) {
					return absint( $user_login );
				}

				$user = get_user_by( 'login', $user_login );
				return $user instanceof WP_User ? $user->ID : null;
			},
			$users,
		);

		// Remove non-existing users and invalid user IDs (0 is not a valid user ID), then re-index the array.
		$user_ids = array_values( array_filter( $user_ids, fn ( ?int $user_id ): bool => null !== $user_id && $user_id > 0 ) );

		return $user_ids;
	}
}
