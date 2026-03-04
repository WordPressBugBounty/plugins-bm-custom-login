<?php
/**
 * Environment utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
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
	 * Get the domain name
	 *
	 * @return ?string Domain name, null if not recognized.
	 */
	public static function get_domain(): ?string {
		$parts = wp_parse_url( get_home_url() );
		return isset( $parts['host'] ) ? $parts['host'] : null;
	}
}
