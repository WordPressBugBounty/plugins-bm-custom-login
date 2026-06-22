<?php
/**
 * File utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use WP_Filesystem_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * File utils class
 */
final class File {
	/**
	 * Get the last modification time of a file
	 *
	 * @param string $file_path Path to the file.
	 *
	 * @return int|false The modification timestamp, or false on failure.
	 */
	public static function get_modified_time( string $file_path ) {
		if ( ! is_readable( $file_path ) ) {
			return false;
		}

		return filemtime( $file_path );
	}

	/**
	 * Read the contents of a local file
	 *
	 * Uses WP_Filesystem when available (preferred by WordPress coding
	 * standards and required on some managed hosting platforms), with a
	 * direct file_get_contents fallback.
	 *
	 * @param string $file_path Absolute path to the file.
	 *
	 * @return ?string File contents, or null on failure.
	 */
	public static function get_contents( string $file_path ): ?string {
		if ( ! is_readable( $file_path ) ) {
			return null;
		}

		/**
		 * Try WP_Filesystem first. The 'direct' method is used to avoid
		 * FTP/SSH credential prompts — appropriate for reading local
		 * files that are already on disk.
		 */
		global $wp_filesystem;

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( ! $wp_filesystem instanceof WP_Filesystem_Base ) {
			WP_Filesystem( false, false, true );
		}

		if ( $wp_filesystem instanceof WP_Filesystem_Base ) {
			$contents = $wp_filesystem->get_contents( $file_path );

			if ( false !== $contents ) {
				return $contents;
			}
		}

		// Fallback to file_get_contents if WP_Filesystem is unavailable.
		$contents = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		return false !== $contents ? $contents : null;
	}
}
