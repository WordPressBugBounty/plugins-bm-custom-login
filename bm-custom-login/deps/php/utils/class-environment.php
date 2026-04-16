<?php
/**
 * Environment utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * Environment utils class
 */
final class Environment {
	/**
	 * Recognize whether this is an AJAX request
	 *
	 * @return bool
	 */
	public static function is_ajax_request(): bool {
		return wp_doing_ajax();
	}

	/**
	 * Recognize whether this is a WP-CLI run
	 *
	 * @return bool
	 */
	public static function is_wp_cli_request(): bool {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Recognize whether this is a CRON job run
	 *
	 * @return bool
	 */
	public static function is_cron_request(): bool {
		return wp_doing_cron();
	}

	/**
	 * Recognize whether the request is a part of the PHP unit tests
	 *
	 * @return bool
	 */
	public static function is_unit_tests(): bool {
		return defined( 'PHPUNIT_COMPOSER_INSTALL' );
	}

	/**
	 * Recognize whether the debug mode is enabled
	 *
	 * @return bool
	 */
	public static function is_debug_mode(): bool {
		return defined( 'WP_DEBUG' ) && WP_DEBUG;
	}

	/**
	 * Recognize whether the current environment is "production"
	 *
	 * @return bool
	 */
	public static function is_production(): bool {
		return 'production' === wp_get_environment_type();
	}

	/**
	 * Recognize whether the current environment is a local dev environment @ Teydea Studio
	 *
	 * @return bool
	 */
	public static function is_local_dev_environment(): bool {
		return 'development' === wp_get_environment_type() && '1' === getenv( 'TEYDEASTUDIO_IS_LOCAL' );
	}

	/**
	 * Get the WordPress version
	 *
	 * Uses wp_get_wp_version() when available (WP 6.7+),
	 * falls back to loading the version from version.php
	 * for older WordPress versions.
	 *
	 * @return string WordPress version string.
	 */
	public static function get_wp_version(): string {
		if ( function_exists( 'wp_get_wp_version' ) ) {
			return wp_get_wp_version();
		}

		// Fallback for WP < 6.7: mirror the wp_get_wp_version() implementation.
		static $wp_version;

		if ( ! isset( $wp_version ) ) {
			require_once ABSPATH . WPINC . '/version.php';
		}

		return Type::ensure_string( $wp_version );
	}

	/**
	 * Compare the current WordPress version against a given version
	 *
	 * Strips pre-release suffixes (e.g. '-RC2') before comparing,
	 * so development and release-candidate builds are treated as their
	 * base version.
	 *
	 * @param string $version  Version to compare against (e.g. '7.0').
	 * @param string $operator Comparison operator ('>', '>=', '<', '<=', '==', '!=').
	 *
	 * @return bool Whether the comparison is true.
	 */
	public static function compare_wp_version( string $version, string $operator ): bool {
		$wp_version = Type::ensure_string( preg_replace( '/-.*$/', '', self::get_wp_version() ) );
		return version_compare( $wp_version, $version, $operator );
	}

	/**
	 * Get the domain name
	 *
	 * @return ?string Domain name, null if not recognized.
	 */
	public static function get_domain(): ?string {
		$parts = wp_parse_url( get_home_url() );
		return isset( $parts['host'] ) ? $parts['host'] : null;
	}
}
