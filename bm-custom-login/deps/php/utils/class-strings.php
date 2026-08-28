<?php
/**
 * Strings utils class
 *
 * @package Teydea_Studio\Login_Customizer\Dependencies\Utils
 */

namespace Teydea_Studio\Login_Customizer\Dependencies\Utils;

use Normalizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
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
		if ( $limit >= mb_strlen( $content, 'UTF-8' ) ) {
			return $content;
		}

		// Truncate string to $limit length.
		$content = mb_substr( wp_strip_all_tags( $content ), 0, $limit, 'UTF-8' );

		// Find last word end position.
		$last_word_end_pos = mb_strrpos( $content, ' ', 0, 'UTF-8' );

		// Truncate after the last word.
		if ( false !== $last_word_end_pos ) {
			$content = mb_substr( $content, 0, $last_word_end_pos, 'UTF-8' );
		}

		// Do not allow the last word to be a single char.
		if ( ' ' === mb_substr( $content, -2, 1, 'UTF-8' ) ) {
			$content = mb_substr( $content, 0, -2, 'UTF-8' );
		}

		// Do not allow the last char to be a stop char, other than a dot.
		if ( in_array( mb_substr( $content, -1, 1, 'UTF-8' ), [ ',', '!', '?', '-', ';', '…' ], true ) ) {
			$content = mb_substr( $content, 0, -1, 'UTF-8' );
		}

		// Add three dots if the last char is not a dot.
		if ( true === $add_ellipsis && '.' !== mb_substr( $content, -1, 1, 'UTF-8' ) ) {
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
	 * Sanitize a CSS color value
	 *
	 * Allows hex colors, CSS color functions (rgb, rgba, hsl, hsla),
	 * and named CSS colors. Returns an empty string for invalid values.
	 *
	 * @param string $color Color value to sanitize.
	 *
	 * @return string Sanitized color value or empty string.
	 */
	public static function sanitize_color( string $color ): string {
		$color = self::trim( $color );

		if ( '' === $color ) {
			return '';
		}

		// Allow hex colors (#rgb, #rgba, #rrggbb, #rrggbbaa).
		if ( 1 === preg_match( '/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{4}|[0-9a-fA-F]{6}|[0-9a-fA-F]{8})$/', $color ) ) {
			return $color;
		}

		// Allow CSS color functions: rgb, rgba, hsl, hsla (numeric channels only).
		if ( 1 === preg_match( '/^(rgb|rgba|hsl|hsla)\([0-9a-f.,\/%\s+-]+\)$/i', $color ) ) {
			return $color;
		}

		// Allow named CSS colors (alphabetic only).
		if ( 1 === preg_match( '/^[a-zA-Z]+$/', $color ) ) {
			return $color;
		}

		return '';
	}

	/**
	 * Sanitize a CSS box-shadow value
	 *
	 * Allows only the characters that make up valid box-shadow values: lengths,
	 * colors (hex, rgb(a)/hsl(a), named), the "inset" keyword, and commas
	 * separating multiple shadows. Any value containing a CSS-structural
	 * character ( { } ; : @ < > ) that could break out of the declaration and
	 * inject arbitrary rules is rejected as an empty string.
	 *
	 * @param string $shadow Box-shadow value to sanitize.
	 *
	 * @return string Sanitized box-shadow value or empty string.
	 */
	public static function sanitize_box_shadow( string $shadow ): string {
		$shadow = self::trim( $shadow );

		if ( '' === $shadow ) {
			return '';
		}

		return 1 === preg_match( '/^[a-zA-Z0-9\s.,%#()\/+\-]+$/', $shadow )
			? $shadow
			: '';
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
			$position = mb_strrpos( $content, $stop_char, 0, 'UTF-8' );

			if ( false !== $position && $cut_at < $position ) {
				$cut_at = $position;
			}
		}

		return mb_substr( $content, 0, $cut_at + 1, 'UTF-8' );
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
	 * Repair a string into well-formed UTF-8
	 *
	 * Byte sequences that are not valid UTF-8 are substituted rather than
	 * rejected, so an undecodable input still yields a usable string. The
	 * substitute is pinned to U+FFFD for the conversion: left to the ambient
	 * "mbstring.substitute_character", a value of "none" would drop the bad bytes
	 * instead, letting the surrounding text close up and form a string the input
	 * never contained.
	 *
	 * @param string $value Value to repair.
	 *
	 * @return string Value with every undecodable byte replaced by U+FFFD.
	 */
	public static function to_valid_utf8( string $value ): string {
		if ( mb_check_encoding( $value, 'UTF-8' ) ) {
			return $value;
		}

		$substitute_character = mb_substitute_character();
		mb_substitute_character( 0xFFFD );

		try {
			return Type::ensure_string( mb_convert_encoding( $value, 'UTF-8', 'UTF-8' ) );
		} finally {
			mb_substitute_character( $substitute_character );
		}
	}

	/**
	 * Resolve a string that carries character entities down to its plain text
	 *
	 * WordPress writes character entities into strings meant to be *read* —
	 * translatable UI copy above all — so a consumer that renders them as text
	 * rather than as markup has to decode them first, or the entity shows up
	 * verbatim on screen.
	 *
	 * Decoding also revives whatever markup characters the string happens to
	 * carry, which matters wherever the result is embedded rather than escaped
	 * on output: inside an inline script block, a revived "</script>" closes the
	 * element early. Angle brackets are therefore dropped instead of trusted.
	 * That is a deliberate narrowing of what this returns — plain text, never
	 * markup — so do not reach for it to sanitize a value whose markup is
	 * meaningful.
	 *
	 * @param string $value Value to resolve.
	 *
	 * @return string Value with its character entities decoded and its angle brackets removed.
	 */
	public static function to_plain_text( string $value ): string {
		return str_replace( [ '<', '>' ], '', html_entity_decode( $value, ENT_QUOTES, 'UTF-8' ) );
	}

	/**
	 * Reduce a string to the form case-insensitive substring comparisons run on
	 *
	 * Undecodable input is repaired rather than rejected — see
	 * self::to_valid_utf8() — so it still yields a comparable string. Unicode
	 * normalization needs the "intl" extension, which is not a declared
	 * requirement of this package; without it, canonically equivalent spellings
	 * stay distinct.
	 *
	 * @param string $value Value to canonicalize.
	 *
	 * @return string Value scrubbed to valid UTF-8, NFC-normalized and lowercased.
	 */
	public static function canonicalize( string $value ): string {
		$value = self::to_valid_utf8( $value );

		if ( class_exists( Normalizer::class ) ) {
			$normalized = Normalizer::normalize( $value, Normalizer::FORM_C );

			if ( is_string( $normalized ) ) {
				$value = $normalized;
			}
		}

		return mb_strtolower( $value, 'UTF-8' );
	}

	/**
	 * Look for a needle in an already canonicalized haystack
	 *
	 * @param string $haystack           The string to search in, as given.
	 * @param string $canonical_haystack The same string, canonicalized.
	 * @param string $needle             The substring to search for in the haystack.
	 *
	 * @return bool Returns true if needle is in haystack ignoring case, false otherwise.
	 */
	private static function canonical_contains( string $haystack, string $canonical_haystack, string $needle ): bool {
		if ( '' === $needle ) {
			return true;
		}

		if ( ! mb_check_encoding( $needle, 'UTF-8' ) && false !== stripos( $haystack, $needle ) ) {
			return true;
		}

		$canonical_needle = self::canonicalize( $needle );

		return '' !== $canonical_needle && self::str_contains( $canonical_haystack, $canonical_needle );
	}

	/**
	 * Case-insensitive variant of "str_contains"
	 *
	 * Both operands are compared in the canonical form described on
	 * self::canonicalize(). A needle that is not valid UTF-8 is compared
	 * byte-wise as well, because substituting its undecodable bytes can hide a
	 * needle the haystack carries verbatim.
	 *
	 * An empty needle returns true, consistent with PHP's str_contains; a needle
	 * that canonicalization empties does not inherit that, and matches byte-wise
	 * only.
	 *
	 * The reverse direction is not covered: when the haystack carries undecodable
	 * bytes, substituting them can split a valid needle straddling the substituted
	 * region, and that needle goes unfound.
	 *
	 * @param string $haystack The string to search in.
	 * @param string $needle   The substring to search for in the haystack.
	 *
	 * @return bool Returns true if needle is in haystack ignoring case, false otherwise.
	 */
	public static function str_icontains( string $haystack, string $needle ): bool {
		return self::canonical_contains( $haystack, self::canonicalize( $haystack ), $needle );
	}

	/**
	 * Case-insensitive variant of "str_contains_any"
	 *
	 * @param string   $haystack The string to search in.
	 * @param string[] $needles  An array of substrings to search for in the haystack.
	 *
	 * @return bool Returns true if any of the needles is in haystack ignoring case, false otherwise.
	 */
	public static function str_icontains_any( string $haystack, array $needles ): bool {
		$canonical_haystack = self::canonicalize( $haystack );

		foreach ( $needles as $needle ) {
			if ( self::canonical_contains( $haystack, $canonical_haystack, $needle ) ) {
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
