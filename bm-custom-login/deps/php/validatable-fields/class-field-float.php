<?php
/**
 * Field_Float class
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
 * Field_Float class
 */
final class Field_Float extends Field {
	/**
	 * Minimum valid value
	 *
	 * @var float
	 */
	protected float $min;

	/**
	 * Maximum valid value
	 *
	 * @var ?float
	 */
	protected ?float $max;

	/**
	 * Float precision
	 *
	 * @var int
	 */
	protected int $precision;

	/**
	 * Construct the object
	 *
	 * @param string   $key           Key of the field.
	 * @param float    $default_value Default value of the field.
	 * @param float    $min           Minimum valid value.
	 * @param ?float   $max           Maximum valid value.
	 * @param int      $precision     Float precision.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 */
	public function __construct( string $key, float $default_value, float $min, ?float $max = null, int $precision = 2, ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ) {
		// Ensure the minimum allowed value is not bigger than maximum.
		if ( $min > $max ) {
			$max = null;
		}

		$this->key        = $key;
		$this->value_type = 'number';
		$this->min        = $min;
		$this->max        = $max;
		$this->precision  = $precision;
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

		$this->default_value = round( $default_value, $precision );
		$this->min           = round( $this->min, $precision );

		if ( null !== $this->max ) {
			$this->max = round( $this->max, $precision );
		}
	}

	/**
	 * Get the minimum allowed value
	 *
	 * @return float Minimum allowed value.
	 */
	public function get_minimum(): float {
		return $this->min;
	}

	/**
	 * Get the maximum allowed value
	 *
	 * @return ?float Maximum allowed value.
	 */
	public function get_maximum(): ?float {
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
		if ( is_int( $value ) ) {
			$value = Utils\Type::ensure_float( $value, $this->precision );
		}

		if ( ! is_float( $value ) ) {
			if ( is_string( $value ) ) {
				if ( 1 !== preg_match( '/^\\d+$/', $value ) ) {
					return new WP_Error(
						'non_float_value',
						sprintf(
							// Translators: %1$s - field name, %2$s - invalid non-numeric string value.
							__( 'Value of the "%1$s" field must be a float, non-numeric string given: "%2$s".', 'bm-custom-login' ),
							$this->get_key_camel_case(),
							$value,
						),
					);
				}

				$value = Utils\Type::ensure_float( $value, $this->precision );
			} else {
				return new WP_Error(
					'non_float_value',
					sprintf(
						// Translators: %1$s - field name, %2$s - type of the value given.
						__( 'Value of the "%1$s" field must be a float, %2$s given.', 'bm-custom-login' ),
						$this->get_key_camel_case(),
						gettype( $value ),
					),
				);
			}
		}

		if ( $this->min > $value ) {
			return new WP_Error(
				'float_out_of_range',
				sprintf(
					// Translators: %1$f - given value, %2$f - minimum allowed value, %3$s - field name.
					__( 'Value %1$f must be greater than or equal to minimum allowed value: %2$f (field: "%3$s").', 'bm-custom-login' ),
					$value,
					$this->min,
					$this->get_key_camel_case(),
				),
			);
		}

		if ( null !== $this->max && $this->max < $value ) {
			return new WP_Error(
				'float_out_of_range',
				sprintf(
					// Translators: %1$f - given value, %2$f - maximum allowed value, %3$s - field name.
					__( 'Value %1$f must be less than or equal to maximum allowed value: %2$f (field: "%3$s").', 'bm-custom-login' ),
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
	 * @return float Sanitized value.
	 */
	protected function sanitize_value( $value ): float {
		$default_value = Utils\Type::ensure_float( $this->default_value, $this->precision );

		$value = Utils\Type::ensure_float( $value, $this->precision );
		$value = parent::sanitize_value( $value );

		return round(
			is_float( $value )
				? $value
				: $default_value,
			$this->precision,
		);
	}
}
