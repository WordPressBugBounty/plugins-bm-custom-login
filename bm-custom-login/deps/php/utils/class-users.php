<?php
/**
 * Users class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Site;
use WP_User;
use WP_User_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
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
			$coverage = $this->count_covered_users( $roles, $users, $exclude_current_user );
		}

		// Calculate coverage and return.
		return [
			'count'           => $coverage,
			'count_formatted' => number_format_i18n( $coverage ),
			'coverage'        => $total_users > 0 ? round( $coverage / $total_users, 4 ) : 0.0,
		];
	}

	/**
	 * Count the distinct users a role / explicit-user coverage selection resolves to
	 *
	 * Split out of {@see get_users_batch()} so the coverage indicator does not
	 * have to materialize every matching user ID into PHP just to `count()` it.
	 *
	 * On a single site the role-matched portion is counted with a SQL `COUNT`
	 * (`WP_User_Query::get_total()`); only the explicit-user list — bounded by
	 * what an administrator can type — is resolved to IDs and reconciled against
	 * the role match so the union is not double-counted. A site with a very large
	 * matching role therefore costs one indexed `COUNT`, not a full ID fetch.
	 *
	 * On a network the coverage count is the number of users matched on *any*
	 * site, deduplicated network-wide. That distinct cross-site count cannot be
	 * expressed as a single SQL `COUNT` (summing per-site counts would
	 * double-count users who belong to several sites), so it necessarily resolves
	 * the matching IDs via {@see get_users_batch()} and counts the deduplicated set.
	 *
	 * @param string[] $roles                Concrete role slugs to match.
	 * @param int[]    $users                Explicit user IDs / logins to include.
	 * @param bool     $exclude_current_user Whether to drop the current user from the count.
	 *
	 * @return int Distinct covered-user count.
	 */
	protected function count_covered_users( array $roles, array $users, bool $exclude_current_user ): int {
		if ( true === $this->container->is_network_enabled() ) {
			return count( $this->get_users_batch( false, $roles, $users, null, null, $exclude_current_user ) );
		}

		$current_user = wp_get_current_user();

		// Mirror get_users_batch()'s auth gate so the count matches the ID-resolving path in every context.
		if ( 0 === $current_user->ID && ! Environment::is_wp_cli_request() ) {
			return 0;
		}

		$roles        = array_values( $roles );
		$explicit_ids = empty( $users ) ? [] : $this->maybe_map_user_logins_to_user_ids( $users );

		// The mapper preserves duplicates (a login and its own numeric ID resolve alike); de-duplicate so the explicit-user counts below don't double-count.
		$explicit_ids = array_values( array_unique( $explicit_ids, SORT_NUMERIC ) );

		if ( empty( $roles ) ) {
			// Explicit-users-only coverage: the resolved explicit set is the whole covered set.
			$count = count( $explicit_ids );

			if ( true === $exclude_current_user && in_array( $current_user->ID, $explicit_ids, true ) ) {
				--$count;
			}

			return max( 0, $count );
		}

		$count = $this->count_users_in_roles( $roles, [] );

		if ( ! empty( $explicit_ids ) ) {
			// Union: add the explicit users the role match did not already include.
			$count += count( $explicit_ids ) - $this->count_users_in_roles( $roles, $explicit_ids );
		}

		if ( true === $exclude_current_user && $this->single_site_user_is_covered( $current_user, $roles, $explicit_ids ) ) {
			--$count;
		}

		return max( 0, $count );
	}

	/**
	 * Count users matching any of the given roles via a SQL `COUNT`
	 *
	 * @param string[] $roles       Role slugs to match (OR semantics).
	 * @param int[]    $include_ids When non-empty, restrict the count to these user IDs (intersection).
	 *
	 * @return int Number of matching users.
	 */
	protected function count_users_in_roles( array $roles, array $include_ids ): int {
		$args = [
			'role__in'    => $roles,
			'fields'      => 'ID',
			'number'      => 1,
			'count_total' => true,
		];

		if ( ! empty( $include_ids ) ) {
			$args['include'] = $include_ids;
		}

		$query = new WP_User_Query( $args );

		return Type::ensure_int( $query->get_total() );
	}

	/**
	 * Whether the given user falls within a single-site role / explicit-user coverage selection
	 *
	 * @param WP_User  $user         User to test.
	 * @param string[] $roles        Role slugs in the selection.
	 * @param int[]    $explicit_ids Explicit user IDs in the selection.
	 *
	 * @return bool True when the user is covered by the selection.
	 */
	protected function single_site_user_is_covered( WP_User $user, array $roles, array $explicit_ids ): bool {
		if ( in_array( $user->ID, $explicit_ids, true ) ) {
			return true;
		}

		return ! empty( array_intersect( $user->roles, $roles ) );
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

			// Tracks role slugs already collected so the per-site dedup is an O(1) set lookup rather than an O(n) scan of `$data`.
			$seen = [];

			foreach ( $roles->role_names as $role => $display_name ) {
				$data[] = [
					'id'    => count( $data ),
					'value' => $role,
					'title' => $display_name,
				];

				$seen[ $role ] = true;
			}

			if ( true === $this->container->is_network_enabled() ) {
				$current_blog_id = get_current_blog_id();

				/** @var WP_Site $site */
				foreach ( get_sites( [ 'number' => 0 ] ) as $site ) {
					if ( Type::ensure_int( $site->blog_id ) === $current_blog_id ) {
						continue;
					}

					switch_to_blog( Type::ensure_int( $site->blog_id ) );

					$site_roles = wp_roles();

					foreach ( $site_roles->role_names as $role => $display_name ) {
						// Add unique roles only.
						if ( isset( $seen[ $role ] ) ) {
							continue;
						}

						$seen[ $role ] = true;

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
				$role_data['id'] += 1;

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
	 * Resolve the site's default role for new user registrations
	 *
	 * Returns the configured "default_role" option when it is a role actually
	 * registered on this site, falling back to "subscriber" otherwise. Useful
	 * when code needs the role a freshly-registered account would receive but
	 * has no trusted role supplied by its caller. The result is always a
	 * registered role slug, never an empty or unknown value.
	 *
	 * @return string Registered default registration role slug.
	 */
	public function get_default_registration_role(): string {
		$default_role = Type::ensure_string( get_option( 'default_role', 'subscriber' ), 'subscriber' );
		return ( '' !== $default_role && null !== get_role( $default_role ) ) ? $default_role : 'subscriber';
	}

	/**
	 * Get users batch
	 *
	 * Use this when you need the actual user IDs (e.g. to iterate and act on each
	 * user). On a network it walks every site, materializes the matching IDs, and
	 * deduplicates them in PHP — necessary to return a distinct ID list across
	 * sites, but it loads every match into memory. If you only need the *count*
	 * of covered users, prefer {@see calculate_coverage()} / {@see count_covered_users()},
	 * which count via SQL on single sites instead of materializing the IDs.
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
			/** @var WP_Site $site */
			foreach ( get_sites( [ 'number' => 0 ] ) as $site ) {
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
	 * The per-site building block for {@see get_users_batch()} (which adds the
	 * logged-in/WP-CLI auth gate, the network-wide site loop, and PHP-side
	 * pagination on top). Background callers that run without a logged-in user
	 * or need native SQL pagination should use the context-free
	 * {@see get_paginated_coverage_ids()} / {@see get_explicit_coverage_ids()}
	 * pair instead, which encapsulate the same role / super-admin-sentinel /
	 * explicit-user coverage semantics without this path's auth gate. Keep the
	 * two in sync when the coverage rules change.
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
	 * Resolve the apply-to-all / role-based coverage user IDs for the current site, context-free
	 *
	 * The shared, background-safe coverage primitive: unlike {@see get_users_batch()}
	 * it applies **no** logged-in/WP-CLI auth gate, so it is safe to call from
	 * scheduled-actions or other background contexts where no user is logged in,
	 * and it paginates natively through `WP_User_Query` (`number`/`paged`) rather
	 * than materializing every match and slicing in PHP. It resolves the current
	 * site only — callers iterate sites themselves (e.g. via `switch_to_blog()`).
	 *
	 * The synthetic super-admin sentinel role is stripped here because it is not a
	 * real WordPress role; resolve those users via {@see get_explicit_coverage_ids()}.
	 * A stable `ID ASC` ordering makes the paged windows deterministic across calls,
	 * and `count_total` is disabled because callers only need the returned rows.
	 *
	 * @param bool     $include_all Whether to cover every user on the site (ignores `$roles`).
	 * @param string[] $roles       Concrete role slugs to match; the super-admin sentinel is ignored.
	 * @param ?int     $limit       Page size; pass with `$paged` to paginate. Null returns every match.
	 * @param ?int     $paged       1-based page number; pass with `$limit` to paginate. Null returns every match.
	 *
	 * @return int[] Matching user IDs for the current site.
	 */
	public function get_paginated_coverage_ids( bool $include_all, array $roles, ?int $limit = null, ?int $paged = null ): array {
		$args = [
			'fields'      => 'ID',
			'orderby'     => 'ID',
			'order'       => 'ASC',
			// Callers only need the returned rows, so skip the extra SELECT FOUND_ROWS().
			'count_total' => false,
		];

		if ( false === $include_all ) {
			$roles = $this->strip_super_admin_sentinel( $roles );

			if ( empty( $roles ) ) {
				return [];
			}

			$args['role__in'] = $roles;
		}

		if ( null !== $limit && null !== $paged ) {
			$args['number'] = $limit;
			$args['paged']  = $paged;
		}

		$query = new WP_User_Query( $args );

		/** @var int[]|string[] $results */
		$results = $query->get_results();

		return Type::ensure_array_of_ints( $results );
	}

	/**
	 * Resolve the explicit-users + super-admin coverage IDs for the current site, context-free
	 *
	 * The one-shot companion to {@see get_paginated_coverage_ids()}: the explicit
	 * `user_coverage.users` login list plus the network super-admins implied by the
	 * sentinel role. Bounded by what an administrator can enter manually, so it is
	 * not paginated. Applies no auth gate, so it is safe in background contexts.
	 *
	 * @param string[]       $roles       Role slugs; consulted only for the super-admin sentinel.
	 * @param int[]|string[] $user_logins Explicit user logins (or IDs) to include.
	 *
	 * @return int[] Matching user IDs for the current site.
	 */
	public function get_explicit_coverage_ids( array $roles, array $user_logins ): array {
		$resolved     = empty( $user_logins ) ? [] : $this->maybe_map_user_logins_to_user_ids( $user_logins );
		$super_admins = $this->resolve_super_admins_for_sentinel( $roles );

		return array_values( array_unique( array_merge( $resolved, $super_admins ), SORT_NUMERIC ) );
	}

	/**
	 * Strip the synthetic super-admin sentinel role from a role list
	 *
	 * The sentinel is exposed only by the plugin settings UI to target network
	 * super admins; it is not a real role and `WP_User_Query` would reject it.
	 *
	 * @param string[] $roles Role slugs.
	 *
	 * @return string[] Concrete role slugs safe to hand to `WP_User_Query`.
	 */
	private function strip_super_admin_sentinel( array $roles ): array {
		if ( false === $this->container->is_network_enabled() ) {
			return $roles;
		}

		$sentinel = $this->get_network_super_admin_role_key();

		return array_values( array_filter( $roles, static fn ( string $role ): bool => $role !== $sentinel ) );
	}

	/**
	 * Resolve network super-admin user IDs when the role list includes the sentinel
	 *
	 * @param string[] $roles Role slugs.
	 *
	 * @return int[] Super-admin user IDs, or empty when the sentinel is absent or the install is not multisite.
	 */
	private function resolve_super_admins_for_sentinel( array $roles ): array {
		if ( false === $this->container->is_network_enabled() ) {
			return [];
		}

		if ( ! in_array( $this->get_network_super_admin_role_key(), $roles, true ) ) {
			return [];
		}

		$super_admins = get_super_admins();

		return empty( $super_admins ) ? [] : $this->maybe_map_user_logins_to_user_ids( $super_admins );
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
