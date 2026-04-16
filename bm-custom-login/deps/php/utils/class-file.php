<?php
/**
 * File utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

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
}
