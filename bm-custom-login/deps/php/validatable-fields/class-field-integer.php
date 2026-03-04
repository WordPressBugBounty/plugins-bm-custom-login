<?php
/**
 * Field_Integer class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields;

use Closure;
use Teydea_Studio\Custom_Login\Dependencies\Utils;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Field_Integer class
 */
final class Field_Integer extends Field {
	/**
	 * Minimum valid value
	 *
	 * @var int
	 */
	protected int $min;

	/**
	 * Maximum valid value
	 *
	 * @var ?int
	 */
	protected ?int $max;

	/**
	 * Construct the object
	 *
	 * @param string   $key           Key of the field.
	 * @param int      $default_value Default value of the field.
	 * @param int      $min           Minimum valid value.
	 * @param ?int     $max           Maximum valid value.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 */
	public function __construct( string $key, int $default_value, int $min, ?int $max = null, ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ) {
		// Ensure the minimum allowed value is not bigger than maximum.
		if ( $min > $max ) {
			$max = null;
		}

		$this->key        = $key;
		$this->value_type = 'integer';
		$this->min        = $min;
		$this->max        = $max;
		$this->restorer   = $restorer;
		$this->sanitizer  = $sanitizer;
		$this->validator  = $validator;

		if (
			// Minimum valid value is bigger than default value?
			$min > $default_value
			||
			// Range of values is set, and the default value doesn't fit in between?
			( null !== $max && $min < $max && $max < $default_value )
		) {
			$default_value = $min;
		} elseif ( null !== $max && $max < $default_value ) {
			// There's no minimum valid value set, and default value is bigger than maximum?
			$default_value = $max;
		}

		$this->default_value = $default_value;
	}

	/**
	 * Get the minimum allowed value
	 *
	 * @return int Minimum allowed value.
	 */
	public function get_minimum(): int {
		return $this->min;
	}

	/**
	 * Get the maximum allowed value
	 *
	 * @return ?int Maximum allowed value.
	 */
	public function get_maximum(): ?int {
		return $this->max;
	}

	/**
	 * Validate the value
	 *
	 * @param mixed $value Provided value.
	 *
	 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
	 */
	protected function validate_value( $value ) {
		if ( ! is_int( $value ) ) {
			if ( is_string( $value ) ) {
				if ( 1 !== preg_match( '/^\\d+$/', $value ) ) {
					return new WP_Error(
						'non_integer_value',
						sprintf(
							// Translators: %1$s - field name, %2$s - invalid non-numeric string value.
							__( 'Value of the "%1$s" field must be an integer, non-numeric string given: "%2$s".', 'bm-custom-login' ),
							$this->get_key_camel_case(),
							$value,
						),
					);
				}

				$value = Utils\Type::ensure_int( $value );
			} else {
				return new WP_Error(
					'non_integer_value',
					sprintf(
						// Translators: %1$s - field name, %2$s - type of the value given.
						__( 'Value of the "%1$s" field must be an integer, %2$s given.', 'bm-custom-login' ),
						$this->get_key_camel_case(),
						gettype( $value ),
					),
				);
			}
		}

		if ( $this->min > $value ) {
			return new WP_Error(
				'integer_out_of_range',
				sprintf(
					// Translators: %1$d - given value, %2$d - minimum allowed value, %3$s - field name.
					__( 'Value %1$d must be greater than or equal to minimum allowed value: %2$d (field: "%3$s").', 'bm-custom-login' ),
					$value,
					$this->min,
					$this->get_key_camel_case(),
				),
			);
		}

		if ( null !== $this->max && $this->max < $value ) {
			return new WP_Error(
				'integer_out_of_range',
				sprintf(
					// Translators: %1$d - given value, %2$d - maximum allowed value, %3$s - field name.
					__( 'Value %1$d must be less than or equal to maximum allowed value: %2$d (field: "%3$s").', 'bm-custom-login' ),
					$value,
					$this->max,
					$this->get_key_camel_case(),
				),
			);
		}

		return parent::validate_value( $value );
	}

	/**
	 * Sanitize the value
	 *
	 * @param mixed $value Provided value.
	 *
	 * @return int Sanitized value.
	 */
	protected function sanitize_value( $value ): int {
		$default_value = Utils\Type::ensure_int( $this->default_value );

		$value = Utils\Type::ensure_int( $value );
		$value = parent::sanitize_value( $value );

		return is_int( $value )
			? $value
			: $default_value;
	}
}
