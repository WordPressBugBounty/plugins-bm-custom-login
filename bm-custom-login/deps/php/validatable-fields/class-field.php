<?php
/**
 * Field class
 *
 * @package Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields
 */

namespace Teydea_Studio\Custom_Login\Dependencies\Validatable_Fields;

use Closure;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Field class
 */
abstract class Field {
	/**
	 * Ensure objects of this class are key identifiable
	 */
	use Key_Identifiable;

	/**
	 * Key of the field
	 *
	 * @var string
	 */
	protected string $key;

	/**
	 * Field value type
	 *
	 * @var string
	 */
	protected string $value_type;

	/**
	 * Field value
	 *
	 * @var mixed
	 */
	protected $value = null;

	/**
	 * Field's default value
	 *
	 * @var mixed
	 */
	protected $default_value = null;

	/**
	 * Field's value additional restorer function
	 *
	 * @var ?Closure
	 */
	protected ?Closure $restorer = null;

	/**
	 * Field's value additional sanitizer function
	 *
	 * @var ?Closure
	 */
	protected ?Closure $sanitizer = null;

	/**
	 * Whether the default sanitization should be skipped
	 *
	 * @var bool
	 */
	protected bool $skip_default_sanitization = false;

	/**
	 * Field's additional validation function
	 *
	 * @var ?Closure
	 */
	protected ?Closure $validator = null;

	/**
	 * Get type of the returned value
	 *
	 * @return string Type of the returned value.
	 */
	public function get_value_type(): string {
		return $this->value_type;
	}

	/**
	 * Get value
	 *
	 * @return mixed Field value.
	 */
	public function get_value() {
		return $this->value;
	}

	/**
	 * Get default value
	 *
	 * @return mixed Field's default value.
	 */
	public function get_default_value() {
		return $this->default_value;
	}

	/**
	 * Get schema for the REST API
	 *
	 * @return array{type:string,items?:array{type:string}} Schema array.
	 */
	public function get_schema(): array {
		return [
			'type' => $this->value_type,
		];
	}

	/**
	 * Set value
	 *
	 * @param mixed $value       Provided value.
	 * @param bool  $use_restore Whether the value restore should happen on validation error or not.
	 *
	 * @return void
	 */
	public function set_value( $value, bool $use_restore = false ): void {
		// Check if provided value is valid.
		$validated = $this->validate_value( $value );

		if ( $validated instanceof WP_Error ) {
			if ( true === $use_restore ) {
				// Restore invalid value is possible.
				$value = $this->restore_value( $value );
			} else {
				// Assign WP_Error as value to pass it to the user notice.
				$value = $validated;
			}
		} else {
			// Sanitize valid value.
			$value = $this->sanitize_value( $value );
		}

		$this->value = $value;
	}

	/**
	 * Validate the value
	 *
	 * @param mixed $value Provided value.
	 *
	 * @return true|WP_Error Boolean "true" on success, instance of WP_Error otherwise.
	 */
	protected function validate_value( $value ) {
		return null !== $this->validator
			? call_user_func( $this->validator, $value )
			: true;
	}

	/**
	 * Sanitize the value
	 *
	 * @param mixed $value Provided value.
	 *
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_value( $value ) {
		return null !== $this->sanitizer
			? call_user_func( $this->sanitizer, $value )
			: $value;
	}

	/**
	 * Restore the value
	 *
	 * @param mixed $value Provided value.
	 *
	 * @return mixed Restored value.
	 */
	protected function restore_value( $value ) {
		return null !== $this->restorer
			? call_user_func( $this->restorer, $value )
			: $this->get_default_value();
	}
}
