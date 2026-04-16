<?php
/**
 * Field_Integer_Of_Choice class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields;

use Closure;
use Teydea_Studio\Custom_Login\Dependencies\Utils;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // @codeCoverageIgnore
}

/**
 * Field_Integer_Of_Choice class
 */
final class Field_Integer_Of_Choice extends Field {
	/**
	 * Array of allowed values
	 *
	 * @var int[]
	 */
	protected array $allowed_values;

	/**
	 * Construct the object
	 *
	 * @param string   $key            Key of the field.
	 * @param int      $default_value  Default value of the field.
	 * @param int[]    $allowed_values Array of allowed values.
	 * @param ?Closure $restorer       Additional function for value restore.
	 * @param ?Closure $sanitizer      Additional sanitizer function.
	 * @param ?Closure $validator      Additional validation function.
	 */
	public function __construct( string $key, int $default_value = 0, array $allowed_values = [], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ) {
		$this->key            = $key;
		$this->value_type     = 'integer';
		$this->default_value  = $default_value;
		$this->allowed_values = $allowed_values;
		$this->restorer       = $restorer;
		$this->sanitizer      = $sanitizer;
		$this->validator      = $validator;
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
			if ( is_string( $value ) && 1 === preg_match( '/^-?\\d+$/', $value ) ) {
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

		if ( ! in_array( $value, $this->allowed_values, true ) ) {
			return new WP_Error(
				'field_value_out_of_scope',
				sprintf(
					// Translators: %1$d - given value, %2$s - field name, %3$s - list of allowed values.
					__( '%1$d of the "%2$s" field is not a value within [%3$s].', 'bm-custom-login' ),
					$value,
					$this->get_key_camel_case(),
					implode( ', ', $this->allowed_values ),
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
