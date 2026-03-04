<?php
/**
 * Field_String_Of_Choice class
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
 * Field_String_Of_Choice class
 */
final class Field_String_Of_Choice extends Field {
	/**
	 * Array of allowed values
	 *
	 * @var string[]
	 */
	protected array $allowed_values;

	/**
	 * Construct the object
	 *
	 * @param string   $key                       Key of the field.
	 * @param string   $default_value             Default value of the field.
	 * @param string[] $allowed_values            Array of allowed values.
	 * @param ?Closure $restorer                  Additional function for value restore.
	 * @param ?Closure $sanitizer                 Additional sanitizer function.
	 * @param ?Closure $validator                 Additional validation function.
	 * @param bool     $skip_default_sanitization Whether the default sanitization should be skipped.
	 */
	public function __construct( string $key, string $default_value = '', array $allowed_values = [], ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null, bool $skip_default_sanitization = false ) {
		$this->key                       = $key;
		$this->value_type                = 'string';
		$this->default_value             = $default_value;
		$this->allowed_values            = $allowed_values;
		$this->restorer                  = $restorer;
		$this->sanitizer                 = $sanitizer;
		$this->validator                 = $validator;
		$this->skip_default_sanitization = $skip_default_sanitization;
	}

	/**
	 * Validate the value
	 *
	 * @param mixed $value Provided value.
	 *
	 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
	 */
	protected function validate_value( $value ) {
		if ( ! is_string( $value ) ) {
			return new WP_Error(
				'non_string_value',
				sprintf(
					// Translators: %1$s - field name, %2$s - type of the value given.
					__( 'Value of the "%1$s" field must be a string, %2$s given.', 'bm-custom-login' ),
					$this->get_key_camel_case(),
					gettype( $value ),
				),
			);
		}

		if ( ! in_array( $value, $this->allowed_values, true ) ) {
			return new WP_Error(
				'field_value_out_of_scope',
				sprintf(
					// Translators: %1$s - given value, %2$s - field name, %3$s - list of allowed values.
					__( '"%1$s" of the "%2$s" field is not a value within "%3$s".', 'bm-custom-login' ),
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
	 * @return string Sanitized value.
	 */
	protected function sanitize_value( $value ): string {
		$default_value = Utils\Type::ensure_string( $this->default_value );
		$value         = Utils\Type::ensure_string( $value, $default_value );

		if ( false === $this->skip_default_sanitization ) {
			$value = Utils\Strings::trim( sanitize_text_field( $value ) );
		}

		$value = parent::sanitize_value( $value );

		return is_string( $value )
			? $value
			: $default_value;
	}
}
