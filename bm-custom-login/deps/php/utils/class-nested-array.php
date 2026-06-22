<?php
/**
 * Nested array utils class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Utils
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * The "Nested_Array" class
 *
 * Stateless helpers for converting between flat dot-notation keys and
 * nested array structures.
 */
final class Nested_Array {
	/**
	 * Recursively flatten nested snake_case arrays into camelCase dot-notation
	 *
	 * @param array<string,mixed> $input  Input array with snake_case keys.
	 * @param string              $prefix Current key prefix.
	 *
	 * @return array<string,mixed> Flattened array with camelCase dot-notation keys.
	 */
	public static function flatten_to_camel_case( array $input, string $prefix = '' ): array {
		$flat = [];

		foreach ( $input as $key => $value ) {
			$camel_key = Strings::to_camel_case( Type::ensure_string( $key ) );
			$full_key  = '' !== $prefix ? $prefix . '.' . $camel_key : $camel_key;

			if ( is_array( $value ) && ! wp_is_numeric_array( $value ) ) {
				/** @var array<string,mixed> $value */
				$flat = array_merge( $flat, self::flatten_to_camel_case( $value, $full_key ) );

				continue;
			}

			$flat[ $full_key ] = $value;
		}

		return $flat;
	}

	/**
	 * Set a value at a dot-notation path in a nested array
	 *
	 * @param array<string,mixed> $result Result array (modified by reference).
	 * @param string              $key    Dot-notation key.
	 * @param mixed               $value  Value to set.
	 *
	 * @return void
	 */
	public static function set_nested_value( array &$result, string $key, $value ): void {
		$segments      = explode( '.', $key );
		$segment_count = count( $segments );

		/** @var array<string,mixed> $current */
		$current = &$result;

		for ( $i = 0; $i < $segment_count - 1; $i++ ) {
			$segment = $segments[ $i ];

			if ( ! isset( $current[ $segment ] ) || ! is_array( $current[ $segment ] ) ) {
				$current[ $segment ] = [];
			}

			/** @var array<string,mixed> $current */
			$current = &$current[ $segment ];
		}

		$current[ $segments[ $segment_count - 1 ] ] = $value;
	}
}
