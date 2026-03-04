<?php
/**
 * Field_Boolean class
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
 * Field_Boolean class
 */
final class Field_Boolean extends Field {
	/**
	 * Construct the object
	 *
	 * @param string   $key           Key of the field.
	 * @param bool     $default_value Default value of the field.
	 * @param ?Closure $restorer      Additional function for value restore.
	 * @param ?Closure $sanitizer     Additional sanitizer function.
	 * @param ?Closure $validator     Additional validation function.
	 */
	public function __construct( string $key, bool $default_value, ?Closure $restorer = null, ?Closure $sanitizer = null, ?Closure $validator = null ) {
		$this->key           = $key;
		$this->value_type    = 'boolean';
		$this->default_value = $default_value;
		$this->restorer      = $restorer;
		$this->sanitizer     = $sanitizer;
		$this->validator     = $validator;
	}

	/**
	 * Validate the value
	 *
	 * @param mixed $value Provided value.
	 *
	 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
	 */
	protected function validate_value( $value ) {
		if ( ! is_bool( $value ) ) {
			if ( ! in_array( $value, [ 1, 0, '1', '0', '' ], true ) && ( is_string( $value ) && ! in_array( strtolower( $value ), [ 'true', 'false' ], true ) ) ) {
				return new WP_Error(
					'non_boolean_value',
					sprintf(
						// Translators: %1$s - field name, %2$s - type of the value given.
						__( 'Value of the "%1$s" field must be a boolean, %2$s given.', 'bm-custom-login' ),
						$this->get_key_camel_case(),
						gettype( $value ),
					),
				);
			}
		}

		return parent::validate_value( $value );
	}

	/**
	 * Sanitize the value
	 *
	 * @param mixed $value Provided value.
	 *
	 * @return bool Sanitized value.
	 */
	protected function sanitize_value( $value ): bool {
		$default_value = Utils\Type::ensure_bool( $this->default_value );

		$value = Utils\Type::ensure_bool( $value, $default_value );
		$value = parent::sanitize_value( $value );

		return is_bool( $value )
			? $value
			: $default_value;
	}
}
