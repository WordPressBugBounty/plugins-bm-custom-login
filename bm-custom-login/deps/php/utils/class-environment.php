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
	 * and reads the $wp_version global on older versions.
	 * The global is populated by wp-includes/version.php, which
	 * core loads during wp-settings.php before any plugin runs.
	 *
	 * @return string WordPress version string.
	 */
	public static function get_wp_version(): string {
		if ( function_exists( 'wp_get_wp_version' ) ) {
			return wp_get_wp_version();
		}

		global $wp_version;

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
	 * Get the PHP version
	 *
	 * @return string PHP version string.
	 */
	public static function get_php_version(): string {
		return PHP_VERSION;
	}

	/**
	 * Compare the current PHP version against a given version
	 *
	 * Strips pre-release suffixes (e.g. '-dev') before comparing,
	 * mirroring the behavior of compare_wp_version().
	 *
	 * @param string $version  Version to compare against (e.g. '7.4').
	 * @param string $operator Comparison operator ('>', '>=', '<', '<=', '==', '!=').
	 *
	 * @return bool Whether the comparison is true.
	 */
	public static function compare_php_version( string $version, string $operator ): bool {
		$php_version = Type::ensure_string( preg_replace( '/-.*$/', '', self::get_php_version() ) );
		return version_compare( $php_version, $version, $operator );
	}

	/**
	 * Check whether a given PHP extension is loaded
	 *
	 * Thin wrapper around extension_loaded() for testability — using
	 * extension_loaded() (not function_exists() checks per function)
	 * avoids false positives from polyfills that cover only part of
	 * an extension's surface.
	 *
	 * @param string $extension Extension name (e.g. 'mbstring').
	 *
	 * @return bool Whether the extension is loaded.
	 */
	public static function is_extension_loaded( string $extension ): bool {
		return extension_loaded( $extension );
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

	/**
	 * Get the site's bare domain label
	 *
	 * For `example.com`, returns `example`. For `subdomain.example.com`,
	 * returns the leftmost label (`subdomain`). A leading `www.` is
	 * stripped before the leftmost label is taken. The result is always
	 * lowercased.
	 *
	 * Returns an empty string for hosts that have no meaningful "domain
	 * label" to match against: localhost variants (`localhost`,
	 * `127.0.0.1`, `::1`) and any host whose entire value is a numeric
	 * IP literal (IPv4 or IPv6, with or without surrounding brackets).
	 * Without this short-circuit a leftmost-label rule would flag a user
	 * named `localhost` on a local install, or any user named `192` (the
	 * first octet of an IP-hosted site) — both are false positives by
	 * definition.
	 *
	 * @return string Bare domain label, or empty string when unavailable or non-applicable.
	 */
	public static function get_domain_label(): string {
		$host = wp_parse_url( get_home_url(), PHP_URL_HOST );

		if ( ! is_string( $host ) || '' === $host ) {
			return '';
		}

		$host = strtolower( $host );

		// Strip an IPv6 literal's surrounding brackets so the IP check below catches it.
		if ( '[' === substr( $host, 0, 1 ) && ']' === substr( $host, -1 ) ) {
			$host = Type::ensure_string( substr( $host, 1, -1 ) );
		}

		// Local-only hostnames carry no externally meaningful "site name".
		if ( in_array( $host, [ 'localhost', '127.0.0.1', '::1' ], true ) ) {
			return '';
		}

		// IP-hosted sites (IPv4 or IPv6) have no domain label.
		if ( false !== filter_var( $host, FILTER_VALIDATE_IP ) ) {
			return '';
		}

		// Strip the leading "www." if present.
		if ( Strings::str_starts_with( $host, 'www.' ) ) {
			$trimmed = substr( $host, 4 );
			$host    = false === $trimmed ? $host : $trimmed;
		}

		$parts = explode( '.', $host );

		if ( '' === $parts[0] ) {
			return '';
		}

		return $parts[0];
	}
}
