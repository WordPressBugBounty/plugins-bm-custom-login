<?php
/**
 * Type utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

use DateTime;
use Exception;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The "Type" class
 */
final class Type {
	/**
	 * Ensure given value is of type "array" and contains only ints
	 *
	 * @param mixed $values         Value given.
	 * @param int[] $default_values Default value to use when type change is not possible.
	 *
	 * @return int[] Value of type "array" that contains only ints.
	 */
	public static function ensure_array_of_ints( $values, array $default_values = [] ): array {
		if ( self::is_array_of_ints( $values ) ) {
			/** @var int[] $values */
			return $values;
		}

		if ( ! is_array( $values ) ) {
			return $default_values;
		}

		foreach ( $values as &$value ) {
			$value = self::ensure_int( $value );
		}

		/** @var int[] $values */
		return $values;
	}

	/**
	 * Ensure given value is of type "array" and contains only strings
	 *
	 * @param mixed    $values         Value given.
	 * @param string[] $default_values Default value to use when type change is not possible.
	 *
	 * @return string[] Value of type "array" that contains only strings.
	 */
	public static function ensure_array_of_strings( $values, array $default_values = [] ): array {
		if ( self::is_array_of_strings( $values ) ) {
			/** @var string[] $values */
			return $values;
		}

		if ( ! is_array( $values ) ) {
			return $default_values;
		}

		foreach ( $values as &$value ) {
			$value = self::ensure_string( $value );
		}

		$values = array_values(
			array_filter(
				$values,

				/**
				 * Ensure only the non-empty values are included
				 *
				 * @param mixed $value Value to validate.
				 *
				 * @return bool Whether the value is valid or not.
				 */
				function ( $value ): bool {
					return is_string( $value ) && ! empty( $value );
				},
			),
		);

		/** @var string[] $values */
		return $values;
	}

	/**
	 * Ensure given value is of type "bool"
	 *
	 * @param mixed $value         Value given.
	 * @param bool  $default_value Default value to use when type change is not possible.
	 *
	 * @return bool Value of type "bool".
	 */
	public static function ensure_bool( $value, bool $default_value = false ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}

		if ( is_string( $value ) || is_int( $value ) ) {
			return rest_sanitize_boolean( $value );
		}

		return $default_value;
	}

	/**
	 * Ensure given value is of type "float"
	 *
	 * @param mixed $value     Value given.
	 * @param int   $precision Float precision.
	 *
	 * @return float Value of type "float".
	 */
	public static function ensure_float( $value, int $precision = 2 ): float {
		if ( is_float( $value ) ) {
			return round( $value, $precision );
		}

		if ( is_array( $value ) || is_bool( $value ) || is_int( $value ) || is_string( $value ) || is_null( $value ) ) {
			return round( absint( $value ), $precision );
		}

		return round( 0, $precision );
	}

	/**
	 * Ensure given value is of type "int"
	 *
	 * @param mixed $value Value given.
	 *
	 * @return int Value of type "int".
	 */
	public static function ensure_int( $value ): int {
		if ( is_int( $value ) ) {
			return $value;
		}

		if ( is_array( $value ) || is_bool( $value ) || is_float( $value ) || is_string( $value ) || is_null( $value ) ) {
			return absint( $value );
		}

		return 0;
	}

	/**
	 * Ensure given value is of type "string"
	 *
	 * @param mixed  $value         Value given.
	 * @param string $default_value Default value to use when type change is not possible.
	 *
	 * @return string Value of type "string".
	 */
	public static function ensure_string( $value, string $default_value = '' ): string {
		if ( is_string( $value ) ) {
			return $value;
		}

		if ( is_bool( $value ) || is_float( $value ) || is_int( $value ) || is_null( $value ) ) {
			return strval( $value );
		}

		return $default_value;
	}

	/**
	 * Check if a given value is an array of ints
	 *
	 * @param mixed $values Value given.
	 *
	 * @return bool Whether the value given is of type "array" and contains only ints, or not.
	 */
	public static function is_array_of_ints( $values ): bool {
		if ( ! is_array( $values ) ) {
			return false;
		}

		foreach ( $values as $value ) {
			if ( ! is_int( $value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if a given value is an array of strings
	 *
	 * @param mixed $values Value given.
	 *
	 * @return bool Whether the value given is of type "array" and contains only strings, or not.
	 */
	public static function is_array_of_strings( $values ): bool {
		if ( ! is_array( $values ) ) {
			return false;
		}

		foreach ( $values as $value ) {
			if ( ! is_string( $value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if a given value is a date string
	 *
	 * @param mixed $value Value given.
	 *
	 * @return bool Whether the value given is a date string.
	 */
	public static function is_date( $value ): bool {
		if ( ! is_string( $value ) || empty( $value ) ) {
			return false;
		}

		try {
			$date  = new DateTime( $value );
			$check = checkdate(
				intval( $date->format( 'n' ) ),
				intval( $date->format( 'j' ) ),
				intval( $date->format( 'Y' ) ),
			);

			return true === $check;
		} catch ( Exception $exception ) {
			return false;
		}
	}

	/**
	 * Check if a given value is a positive integer
	 *
	 * @param mixed $value Value given.
	 *
	 * @return bool Whether the value given is a positive integer.
	 */
	public static function is_positive_int( $value ): bool {
		if ( is_int( $value ) && $value > 0 ) {
			return true;
		}

		if ( is_string( $value ) && preg_match( '/^\d+$/', $value ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if a given value is a URL
	 *
	 * @param mixed $value Value given.
	 *
	 * @return bool Whether the value given is a URL.
	 */
	public static function is_url( $value ): bool {
		if ( ! is_string( $value ) ) {
			return false;
		}

		$parsed_url = wp_parse_url( $value );

		if ( false === $parsed_url ) {
			return false;
		}

		$protocol  = isset( $parsed_url['scheme'] ) ? $parsed_url['scheme'] : null;
		$authority = isset( $parsed_url['host'] ) ? $parsed_url['host'] : null;

		if ( null === $protocol || null === $authority ) {
			return false;
		}

		if ( ! in_array( $protocol, [ 'http', 'https' ], true ) ) {
			return false;
		}

		if ( ! preg_match( '/^[^\s#?]+$/', $authority, $matches, PREG_UNMATCHED_AS_NULL ) ) {
			return false;
		}

		if ( Strings::str_starts_with( $authority, '.' ) || Strings::str_ends_with( $authority, '.' ) ) {
			return false;
		}

		return true;
	}
}
