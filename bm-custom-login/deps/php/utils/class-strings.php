<?php
/**
 * Strings utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Strings utils class
 */
final class Strings {
	/**
	 * Get excerpt limited to a given number of chars, truncate after the last word
	 *
	 * @param string $content      Original excerpt content.
	 * @param int    $limit        Max length of final excerpt.
	 * @param bool   $add_ellipsis Whether to add elipsis at the end of limited content or not.
	 *
	 * @return string Truncated sentence.
	 */
	public static function length_limited( string $content, int $limit = 160, bool $add_ellipsis = true ): string {
		// Trim content.
		$content = self::trim( $content );

		// Do nothing if content length is smaller than limit.
		if ( $limit >= strlen( $content ) ) {
			return $content;
		}

		// Truncate string to $limit length.
		$content = substr( wp_strip_all_tags( $content ), 0, $limit );

		// Find last word end position.
		$last_word_end_pos = strrpos( $content, ' ' );

		// Truncate after the last word.
		if ( false === $last_word_end_pos ) {
			$content = substr( $content, 0 );
		} else {
			$content = substr( $content, 0, $last_word_end_pos );
		}

		if ( false === $content ) {
			$content = '';
		}

		// Do not allow the last word to be a single char.
		if ( ' ' === substr( $content, -2, 1 ) ) {
			$content = substr( $content, 0, -2 );
		}

		// Do not allow the last char to be a stop char, other than a dot.
		if ( in_array( substr( $content, -1, 1 ), [ ',', '!', '?', '-', ';', '…' ], true ) ) {
			$content = substr( $content, 0, -1 );
		}

		// Add three dots if the last char is not a dot.
		if ( true === $add_ellipsis && '.' !== substr( $content, -1, 1 ) ) {
			$content .= '…';
		}

		return $content;
	}

	/**
	 * Sanitize CSS
	 *
	 * @param mixed $value          Value to sanitize.
	 * @param bool  $allow_newlines Whether to allow newline characters.
	 *
	 * @return string Sanitized value.
	 */
	public static function sanitize_css( $value, bool $allow_newlines = false ): string {
		$value = Type::ensure_string( $value );

		if ( '' === $value ) {
			return $value;
		}

		/**
		 * Note that esc_html() cannot be used because `div &gt; span` is not interpreted properly.
		 * This is the same approach as in core's "wp_custom_css_cb" function.
		 */
		$value = wp_strip_all_tags( $value );

		// Remove or standardize newline characters.
		$value = str_replace( [ "\n", "\r", "\t" ], $allow_newlines ? PHP_EOL : '', $value );

		return $value;
	}

	/**
	 * Get excerpt truncate after the last sentence
	 *
	 * @param string $content Original excerpt content.
	 *
	 * @return string Truncated sentence.
	 */
	public static function sentence_limited( string $content ): string {
		// Trim content.
		$content = self::trim( $content );

		// Limit the content length.
		$content = self::length_limited( $content, 360, false );

		// Truncate after the last stop char (end of sentence), whichever occurs later.
		$cut_at = 0;

		foreach ( [ '.', '!', '?', ';', '…' ] as $stop_char ) {
			$position = strrpos( $content, $stop_char );

			if ( false !== $position && $cut_at < $position ) {
				$cut_at = $position;
			}
		}

		return substr( $content, 0, $cut_at + 1 );
	}

	/**
	 * Polyfill for the PHP 8.0 "str_starts_with" function
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the haystack.
	 *
	 * @return bool Returns true if haystack begins with needle, false otherwise.
	 */
	public static function str_starts_with( string $haystack, string $needle ): bool {
		if ( function_exists( 'str_starts_with' ) ) {
			return str_starts_with( $haystack, $needle );
		}

		return strlen( $needle ) === 0 || strpos( $haystack, $needle ) === 0;
	}

	/**
	 * Polyfill for the PHP 8.0 "str_ends_with" function
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the haystack.
	 *
	 * @return bool Returns true if haystack begins with needle, false otherwise.
	 */
	public static function str_ends_with( string $haystack, string $needle ): bool {
		if ( function_exists( 'str_ends_with' ) ) {
			return str_ends_with( $haystack, $needle );
		}

		$length = strlen( $needle );
		return 0 === $length || 0 === substr_compare( $haystack, $needle, - $length );
	}

	/**
	 * Polyfill for the PHP 8.0 "str_contains" function
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the haystack.
	 *
	 * @return bool Returns true if needle is in haystack, false otherwise.
	 */
	public static function str_contains( string $haystack, string $needle ): bool {
		if ( function_exists( 'str_contains' ) ) {
			return str_contains( $haystack, $needle );
		}

		return strlen( $needle ) === 0 || strpos( $haystack, $needle ) !== false;
	}

	/**
	 * Check whether any given character exists in the string
	 *
	 * @param string   $haystack The string to search in.
	 * @param string[] $needles  An array of characters to search for in the haystack.
	 *
	 * @return bool Returns true if any of the needles is in haystack, false otherwise.
	 */
	public static function str_contains_any( string $haystack, array $needles ): bool {
		foreach ( $needles as $needle ) {
			if ( self::str_contains( $haystack, $needle ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Convert string to camelCase
	 *
	 * @param string $input String to convert.
	 *
	 * @return string Converted string.
	 */
	public static function to_camel_case( string $input ): string {
		return lcfirst( str_replace( [ ' ', '-', '_' ], '', ucwords( $input, " \t\r\n\f\v-_" ) ) );
	}

	/**
	 * Convert string to snake_case
	 *
	 * @param string $input String to convert.
	 *
	 * @return string Converted string.
	 */
	public static function to_snake_case( string $input ): string {
		return strtolower( preg_replace( '/(?<!^)[A-Z]/', '_$0', $input ) ?? '' );
	}

	/**
	 * Trim and cleanup whitespaces
	 *
	 * @param string $content String content to process.
	 *
	 * @return string String with trimmed whatespaces.
	 */
	public static function trim( string $content ): string {
		$special_characters = [
			' '      => ' ',
			'\u00a0' => ' ',
			'&nbsp;' => ' ',
		];

		$content = str_replace( array_keys( $special_characters ), array_values( $special_characters ), $content );
		$content = preg_replace( '/\s+/', ' ', $content ) ?? '';

		return trim( $content );
	}
}
